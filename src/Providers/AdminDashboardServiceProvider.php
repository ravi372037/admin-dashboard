<?php

namespace RaviSaini\AdminDashboard\Providers;

use Illuminate\Support\ServiceProvider;

class AdminDashboardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('admin.php'),
        ], 'config');

        // Load config
        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'admin');

        // Load theme
        $theme = config('admin.theme', 'theme1');

        // Load views for selected theme
        $this->loadViewsFrom(__DIR__ . "/../Themes/{$theme}/views", 'admin');

        // Publish views
        $this->publishes([
            __DIR__ . "/../Themes/{$theme}/views" => resource_path("views/vendor/admin"),
        ], 'admin-views');

        // Publish assets
        $this->publishes([
            __DIR__ . "/../Themes/{$theme}/assets" => public_path("assets/admin/{$theme}"),
        ], 'admin-assets');

        // Load default package routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/admin.php');

        // Load custom routes if published
        $customRoutes = base_path('routes/admin-dashboard.php');
        if (file_exists($customRoutes)) {
            $this->loadRoutesFrom($customRoutes);
        }

        // Register artisan commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \RaviSaini\AdminDashboard\Console\InstallAdminCommand::class,
            ]);

            // Allow publishing extra resources only in console
            $this->publishes([
                __DIR__ . '/../routes/admin.php' => base_path('routes/admin-dashboard.php'),
            ], 'admin-routes');

            $this->publishes([
                __DIR__ . '/../Http/Controllers' => app_path('Http/Controllers/AdminDashboard'),
            ], 'admin-controllers');

            // ADD THIS BLOCK for migrations
            $this->publishes([
                __DIR__ . '/../database/migrations/' => database_path('migrations'),
            ], 'admin-migrations');
        }

    }
}
