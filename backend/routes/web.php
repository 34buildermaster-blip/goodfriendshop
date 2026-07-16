<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ContentPostController;
use App\Http\Controllers\Admin\GamePackageController;
use App\Http\Controllers\Admin\GameProductController;
use App\Http\Controllers\Admin\HeroSlideController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PremiumAppController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/auth/google/redirect', [AuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::resource('orders', OrderController::class)
            ->only(['index', 'edit', 'update']);
        Route::resource('products', GameProductController::class)
            ->except('show')
            ->parameters(['products' => 'game']);
        Route::get('products/{game}/packages/create', [GamePackageController::class, 'create'])
            ->name('products.packages.create');
        Route::post('products/{game}/packages', [GamePackageController::class, 'store'])
            ->name('products.packages.store');
        Route::get('packages', [GamePackageController::class, 'index'])
            ->name('packages.index');
        Route::get('packages/create', [GamePackageController::class, 'createAny'])
            ->name('packages.create');
        Route::post('packages', [GamePackageController::class, 'storeAny'])
            ->name('packages.store');
        Route::get('packages/{package}/edit', [GamePackageController::class, 'edit'])
            ->name('packages.edit');
        Route::put('packages/{package}', [GamePackageController::class, 'update'])
            ->name('packages.update');
        Route::delete('packages/{package}', [GamePackageController::class, 'destroy'])
            ->name('packages.destroy');
        Route::resource('premium-apps', PremiumAppController::class)
            ->except('show')
            ->parameters(['premium-apps' => 'premiumApp']);
        Route::resource('content-posts', ContentPostController::class)
            ->except('show')
            ->parameters(['content-posts' => 'contentPost']);
        Route::get('site-settings', [SiteSettingController::class, 'edit'])
            ->name('site-settings.edit');
        Route::put('site-settings', [SiteSettingController::class, 'update'])
            ->name('site-settings.update');
        Route::resource('hero-slides', HeroSlideController::class)
            ->except('show')
            ->parameters(['hero-slides' => 'heroSlide']);
        Route::resource('announcements', AnnouncementController::class)
            ->except('show');
        Route::resource('users', UserManagementController::class)
            ->except('show');
    });
});
