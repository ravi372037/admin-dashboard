<?php

use App\Http\Controllers\AdminDashboard\Auth\ChangePasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboard\Auth\LoginController;
use App\Http\Controllers\AdminDashboard\Auth\LogoutController;
use App\Http\Controllers\AdminDashboard\DashboardController;

$prefix = config('admin.route_prefix', 'admin');
$guard = config('admin.guard', 'web');

Route::middleware(['guest'])->group(function () use ($prefix, $guard) {
    // Guest Routes
    Route::get("{$prefix}/login", [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post("{$prefix}/login", [LoginController::class, 'login'])->name('admin.login.submit');
});

Route::middleware(["auth:{$guard}"])->prefix($prefix)->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

    Route::get('change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('change-password', [ChangePasswordController::class, 'change'])->name('password.change');
});
