<?php

namespace RaviSaini\AdminDashboard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstallAdminCommand extends Command
{
    protected $signature = 'admin:install';
    protected $description = 'Install and configure admin dashboard with authentication, guards, and layout.';

    public function handle()
    {
        $this->info('📦 Installing Admin Dashboard...');


        Artisan::call('vendor:publish', [
            '--provider' => 'RaviSaini\\AdminDashboard\\Providers\\AdminDashboardServiceProvider',
            '--tag' => 'config',
            '--force' => true
        ]);

        // Theme selection
        $theme = $this->choice('🎨 Select admin theme', ['theme1'], 0);

        // Auth table selection
        $table = $this->choice('Authentication table', ['users', 'admins'], 0);


        if ($table === 'users') {
            $this->ensureIsAdminColumnExists('users');
        } else {
            $this->ensureAdminGuardConfigured();
            $this->ensureAdminsTableExists();
        }
        // Save config to file
        $this->setConfigKey('theme', $theme);
        $this->setConfigKey('guard', $table === 'users' ? 'web' : 'admin');
        $this->setConfigKey('route_prefix', 'admin');
        $this->setConfigKey('table', $table);


        // Prompt for admin credentials
        $email = $this->ask('📧 Enter admin email');
        $password = $this->secret('🔐 Enter admin password');

        $this->createAdminUser($table, $email, $password);

        $this->info('✅ Admin dashboard installed and configured!');


        Artisan::call('vendor:publish', [
            '--provider' => 'RaviSaini\\AdminDashboard\\Providers\\AdminDashboardServiceProvider',
            '--tag' => 'admin-assets',
            '--force' => true
        ]);
        Artisan::call('vendor:publish', [
            '--provider' => 'RaviSaini\\AdminDashboard\\Providers\\AdminDashboardServiceProvider',
            '--tag' => 'admin-views',
            '--force' => true
        ]);
        Artisan::call('vendor:publish', [
            '--provider' => 'RaviSaini\\AdminDashboard\\Providers\\AdminDashboardServiceProvider',
            '--tag' => 'admin-routes',
            '--force' => true
        ]);
        Artisan::call('vendor:publish', [
            '--provider' => 'RaviSaini\\AdminDashboard\\Providers\\AdminDashboardServiceProvider',
            '--tag' => 'admin-controllers',
            '--force' => true
        ]);

        $webRouteFile = base_path('routes/web.php');
        $requireLine = "require __DIR__.'/admin-dashboard.php';";

        if (!str_contains(file_get_contents($webRouteFile), $requireLine)) {
            file_put_contents($webRouteFile, "\n$requireLine\n", FILE_APPEND);
        }
        $this->info(Artisan::output());
    }

    protected function setConfig($key, $value)
    {
        $configPath = config_path('admin.php');
        $config = file_exists($configPath) ? include $configPath : [];

        data_set($config, $key, $value);

        $content = "<?php\n\nreturn " . var_export($config, true) . ";\n";
        file_put_contents($configPath, $content);
    }

    protected function ensureIsAdminColumnExists($table)
    {
        if (!DB::getSchemaBuilder()->hasColumn($table, 'is_admin')) {
            $this->info("Adding 'is_admin' column to {$table} table...");
            DB::statement("ALTER TABLE {$table} ADD COLUMN is_admin TINYINT(1) DEFAULT 0");
        }
    }

    protected function ensureAdminGuardConfigured()
    {
        $authPath = config_path('auth.php');
        $auth = include $authPath;

        if (!isset($auth['guards']['admin'])) {
            $this->info("Adding 'admin' guard to config/auth.php...");

            $auth['guards']['admin'] = [
                'driver' => 'session',
                'provider' => 'admins',
            ];

            $auth['providers']['admins'] = [
                'driver' => 'eloquent',
                'model' => \App\Models\Admin::class,
            ];

            $content = "<?php\n\nreturn " . var_export($auth, true) . ";\n";
            file_put_contents($authPath, $content);
        }
    }

    protected function ensureAdminsTableExists()
    {
        $adminModelPath = app_path('Models/Admin.php');
        $migrationCreated = false;

        if (!DB::getSchemaBuilder()->hasTable('admins')) {
            $this->info("Creating 'Admin' model and 'admins' table...");

            // Generate model with migration if not exists
            if (!file_exists($adminModelPath)) {
                Artisan::call('make:model', ['name' => 'Admin', '--migration' => true]);

                if (file_exists($adminModelPath)) {
                    $contents = file_get_contents($adminModelPath);

                    // ✅ Replace Model with Authenticatable
                    $contents = str_replace(
                        'use Illuminate\\Database\\Eloquent\\Model;',
                        'use Illuminate\\Foundation\\Auth\\User as Authenticatable;',
                        $contents
                    );

                    // ✅ Clean up guarded
                    $contents = preg_replace('/protected \$guarded = \[.*?\];\n?/s', '', $contents);

                    // ✅ Replace class declaration to extend Authenticatable with fillable
                    if (preg_match('/class Admin extends Model[^{]*{[\s\n\r]*}/s', $contents)) {
                        $contents = preg_replace(
                            '/class Admin extends Model[^{]*{[\s\n\r]*}/s',
                            "class Admin extends Authenticatable\n{\n    protected \$fillable = ['name', 'email', 'password'];\n}\n",
                            $contents
                        );
                    } elseif (strpos($contents, 'fillable') !== false) {
                        $contents = preg_replace(
                            '/protected \$fillable = \[.*?\];/s',
                            'protected $fillable = [\'name\', \'email\', \'password\'];',
                            $contents
                        );
                    } else {
                        $contents = preg_replace(
                            '/class Admin extends Model\s*{/',
                            "class Admin extends Authenticatable\n{\n    protected \$fillable = ['name', 'email', 'password'];\n",
                            $contents
                        );
                    }

                    file_put_contents($adminModelPath, $contents);
                }

                $migrationCreated = true;
            } else {
                // Check if migration exists
                $migrationPath = collect(glob(database_path('migrations/*_create_admins_table.php')))->last();
                if (!$migrationPath) {
                    Artisan::call('make:migration', ['name' => 'create_admins_table']);
                    $migrationCreated = true;
                }
            }

            // Customize migration
            $migrationPath = collect(glob(database_path('migrations/*_create_admins_table.php')))->last();
            if ($migrationPath) {
                $contents = file_get_contents($migrationPath);
                $updated = str_replace(
                    '$table->id();',
                    '$table->id();' . "\n" .
                        '                $table->string(\'name\')->nullable();' . "\n" .
                        '                $table->string(\'email\')->unique();' . "\n" .
                        '                $table->string(\'password\');' . "\n" .
                        '                $table->rememberToken();',
                    $contents
                );
                file_put_contents($migrationPath, $updated);
            }

            // Run migration if needed
            if ($migrationCreated || !DB::getSchemaBuilder()->hasTable('admins')) {
                $this->info("Running migration...");
                Artisan::call('migrate');
                $this->info(Artisan::output());
            }
        }
    }


    protected function createAdminUser($table, $email, $password)
    {
        $this->info("Creating admin user in '{$table}' table...");

        if ($table === 'users') {
            $modelClass = \App\Models\User::class;
        } else {
            $modelClass = \App\Models\Admin::class;

            if (!class_exists($modelClass)) {
                $this->warn("Admin model not found. Running dump-autoload...");
                exec('composer dump-autoload');
            }
        }

        if (!class_exists($modelClass)) {
            $this->error("Model {$modelClass} still not found. Please check your setup.");
            return;
        }


        $data = ['email' => $email, 'password' => Hash::make($password)];
        if ($table === 'users') {
            $data['is_admin'] = 1;
        }
        $data['name'] = 'Admin';

        $modelClass::updateOrCreate(['email' => $email], $data);

        $this->info("✅ Admin user created successfully!");

        // Auto-set fillable property in Admin model
    }

    protected function setConfigKey($key, $value)
    {
        $configPath = config_path('admin.php');

        if (!file_exists($configPath)) {
            $this->error("❌ Config file not found: admin.php");
            return;
        }

        $contents = file_get_contents($configPath);

        $pattern = "/(['\"]{$key}['\"]\s*=>\s*)(['\"]?)[^'\"]*\\2(,?)/";
        $replacement = "$1'$value'$3";

        $contents = preg_replace($pattern, $replacement, $contents);

        file_put_contents($configPath, $contents);
    }
}
