<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);


Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('2fa')->name('home');

    Route::get('/profile', [App\Http\Controllers\Auth\ProfileController::class, 'edit'])->middleware('2fa')->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\Auth\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [App\Http\Controllers\Auth\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/2fa', [App\Http\Controllers\Auth\TwoFactorAuthController::class, 'index'])->middleware('2fa')->name('2fa.index');
    Route::post('/2fa/enable', [App\Http\Controllers\Auth\TwoFactorAuthController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [App\Http\Controllers\Auth\TwoFactorAuthController::class, 'disable'])->name('2fa.disable');
    Route::match(['get', 'post'], '/2fa/verify', [App\Http\Controllers\Auth\TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/recovery', [App\Http\Controllers\Auth\TwoFactorAuthController::class, 'recovery'])->name('2fa.recovery');

    Route::get('/2fa/re-code', function () { return view('auth.2fa.re-code'); })->name('2fa.qrCode');
});
