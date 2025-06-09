# 🛠️ Laravel Admin Dashboard Package

A plug-n-play Laravel package to instantly scaffold an **Admin Panel** with full **authentication**, **theme setup**, **guard configuration**, and **controller-routing views** — all in a single command!

## Requirements

- PHP >= 8.1
- Laravel >= 11.0

---

## 🎯 Features

- 🔐 Admin authentication (via `users` or custom `admins` table)
- 🎨 Ready-to-use theme (Otika included)
- 🛡️ Admin guard (`admin`) auto-configured
- ⚙️ One command to publish config, assets, views, routes, controllers
- 👤 Create admin user during install
- 🚀 Works out-of-the-box!

---

## 📦 Installation

### Step 1: Install via Composer

```bash
composer require ravi-saini/admin-dashboard
```

### Step 2: Run Installation Command

```bash
php artisan admin:install
```

The installation process will guide you through:

1. Theme Selection
2. Authentication Table Choice
3. Admin User Creation

Example installation flow:

```
📦 Installing Admin Dashboard...

 🎨 Select admin theme 
 [0] theme1
 > 0

Authentication table [users]:
  [0] users
  [1] admins
 > 1

Adding 'admin' guard to config/auth.php...
Creating 'Admin' model and 'admins' table...
Running migration...

   INFO  Running migrations.

  2025_06_09_171108_create_admins_table ...................................... 29.61ms DONE

 📧 Enter admin email:
 > admin@gmail.com

 🔐 Enter admin password:
 > ••••••••

Creating admin user in 'admins' table...
✅ Admin user created successfully!
✅ Admin dashboard installed and configured!

   INFO  Publishing [admin-controllers] assets.

  Copying directory [packages/AdminDashboard/src/Http/Controllers] to [app/Http/Controllers/AdminDashboard]  DONE

```

## 🔧 Configuration

The package will automatically:
- Configure the admin guard
- Create necessary database tables
- Set up authentication
- Publish required assets and views

### Middleware Configuration

Add these lines in your `bootstrap/app.php` file to handle authentication redirects:

```php
$middleware->redirectGuestsTo(fn() => route('admin.login'));
$middleware->redirectUsersTo(fn() => route('admin.dashboard'));
```

This ensures:
- Unauthenticated users are redirected to the login page
- Authenticated users are redirected to the dashboard

## 🚀 Usage

After installation, you can access your admin panel at:
```
http://your-app.com/admin/login
```

Login with the credentials you provided during installation.

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
