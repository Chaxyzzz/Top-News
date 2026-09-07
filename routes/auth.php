<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ReaderRegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReaderAccountController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication & Profile Routes (Phase 02)
|--------------------------------------------------------------------------
*/

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Public Reader Registration (Phase 08)
    Route::get('/register', [ReaderRegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [ReaderRegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Suspended Notice Route
Route::get('/suspended', function () {
    return view('auth.suspended');
})->name('suspended');

// Authenticated Account & Profile Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Staff Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Public Reader Account Area (Phase 08)
    Route::get('/account', [ReaderAccountController::class, 'bookmarks'])->name('account.index');
    Route::get('/account/bookmarks', [ReaderAccountController::class, 'bookmarks'])->name('account.bookmarks');
    Route::get('/account/comments', [ReaderAccountController::class, 'comments'])->name('account.comments');
    Route::get('/account/profile', [ReaderAccountController::class, 'profile'])->name('account.profile');
    Route::put('/account/profile', [ReaderAccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::get('/account/security', [ReaderAccountController::class, 'security'])->name('account.security');
    Route::put('/account/security', [ReaderAccountController::class, 'updatePassword'])->name('account.security.update');
});
