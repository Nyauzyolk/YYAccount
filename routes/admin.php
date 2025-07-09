<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;

Route::get('/', [HomeController::class, 'index'])->name('index')->middleware('auth:admin')->withoutMiddleware('admin.validateReferer');

Route::withoutMiddleware(['auth:admin', 'admin.validateReferer'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}/delete', [UserController::class, 'destroy'])->name('users.destroy');
});