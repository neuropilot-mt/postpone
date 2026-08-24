<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\SocialOAuthController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');

    Route::get('accounts', [SocialAccountController::class, 'index'])->name('accounts.index');
    Route::post('accounts', [SocialAccountController::class, 'store'])->name('accounts.store');
    Route::delete('accounts/{socialAccount}', [SocialAccountController::class, 'destroy'])->name('accounts.destroy');

    Route::get('oauth/{platform}/redirect', [SocialOAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('oauth/{platform}/callback', [SocialOAuthController::class, 'callback'])->name('oauth.callback');

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('users', [AdminController::class, 'users'])->name('users');
        Route::patch('users/{user}/admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
        Route::get('posts', [AdminController::class, 'posts'])->name('posts');
    });
});

require __DIR__.'/settings.php';
