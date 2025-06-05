<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterUserController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/register', [RegisterUserController::class, 'create'])
    ->name('register');

Route::post('/register', [RegisterUserController::class, 'store'])
    ->name('register.store');

Route::get('/verify-email', [RegisterUserController::class, 'verifyEmail'])
    ->name('verify.email');

Route::get('/email-not-verified', [RegisterUserController::class, 'emailNotVerified'])
    ->name('email-not-verified');

Route::post('/resend-verification-email', [RegisterUserController::class, 'resendVerificationEmail'])
    ->name('resend-verification-email');

Route::get('/login', [LoginController::class, 'create'])
    ->name('login');

Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store');

Route::get('/logout', [LoginController::class, 'destroy'])
    ->name('logout');

Route::get('/forgot-username', [LoginController::class, 'forgotUsername'])
    ->name('login.forgot-username');

Route::post('/forgot-username', [LoginController::class, 'emailUsername'])
    ->name('login.email-username');

Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])
    ->name('login.forgot-password');

Route::post('/forgot-password', [LoginController::class, 'emailPassword'])
    ->name('login.email-password');

Route::get('/reset-password', [LoginController::class, 'resetPassword'])
    ->name('login.reset-password');

Route::post('/update-password', [LoginController::class, 'updatePassword'])
    ->name('login.update-password');

Route::get('/profile', [ProfileController::class, 'create'])
    ->name('profile');

Route::get('/entry', [EntryController::class, 'create'])
    ->name('entry.create');

Route::post('/entry', [EntryController::class, 'store'])
    ->name('entry.store');

Route::get('/list', [EntryController::class, 'list'])
    ->name('entry.list');

Route::get('/subgroups/{group}', [EntryController::class, 'getSubgroups']);

Route::get('/places/suggest', [EntryController::class, 'suggestPlaces'])
    ->name('places.suggest');

Route::get('/locations/suggest', [EntryController::class, 'suggestLocations'])
    ->name('locations.suggest');