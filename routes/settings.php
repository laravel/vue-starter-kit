<?php

use App\Http\Controllers\Settings\PasskeyController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('settings/passkey', [PasskeyController::class, 'edit'])->name('passkey.edit');
    Route::get('settings/passkey/register-options', [PasskeyController::class, 'generatePasskeyOptions'])->name('passkey.register-options');
    Route::post('settings/passkey', [PasskeyController::class, 'store'])->name('passkey.store');
    Route::delete('settings/passkey/{passkey}', [PasskeyController::class, 'destroy'])->name('passkey.destroy');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
