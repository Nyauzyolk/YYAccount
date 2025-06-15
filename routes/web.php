<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\ConfirmPasswordController;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use App\Http\Controllers\Auth\ProfileController;

Route::get('/', function () { return view('welcome'); });

Auth::routes(['verify' => true]);

Route::middleware(['auth'])->group(function () {
    Route::get('/password/confirm', [ConfirmPasswordController::class, 'showConfirmForm'])->name('password.confirm');
    
    Route::post('/password/confirm', [ConfirmPasswordController::class, 'confirm']);
});

Route::get('/auth/redirect', function () { return Socialite::driver('github')->redirect(); })->name('auth.github');

Route::get('/auth/callback', function () {
    try {
            $githubUser = Socialite::driver('github')->user();

            $user = User::where('github_id', $githubUser->getId())->first();

            if (!$user) {
                $user = User::where('email', $githubUser->getEmail())->first();
                if ($user) {
                    $user->update([
                        'github_id' => $githubUser->getId(),
                        'github_token' => $githubUser->token,
                        'github_refresh_token' => $githubUser->refreshToken,
                        'github_token_expires_at' => now()->addHours(6),
                    ]);
                }
            }

            if (!$user) {
                $user = User::create([
                    'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'email' => $githubUser->getEmail(),
                    'password' => bcrypt(Str::random(24)),
                    'github_id' => $githubUser->getId(),
                    'github_token' => $githubUser->token,
                    'github_refresh_token' => $githubUser->refreshToken,
                    'github_token_expires_at' => now()->addHours(6),
                ]);
            }

            Auth::login($user, true);

            Log::info('GitHub 登录成功', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return redirect('/home')
                ->with('success', 'GitHub 登录成功！');

        } catch (Exception $e) {
            Log::error('GitHub 登录失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('login')
                ->with('error', 'GitHub 登录失败，请重试');
        }
    });

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->middleware('2fa')->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->middleware('2fa')->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/2fa', [TwoFactorAuthController::class, 'index'])->middleware('2fa')->name('2fa.index');
    Route::post('/2fa/enable', [TwoFactorAuthController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorAuthController::class, 'disable'])->name('2fa.disable')->middleware(['password.confirm']);;
    Route::match(['get', 'post'], '/2fa/verify', [TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/recovery', [TwoFactorAuthController::class, 'recovery'])->name('2fa.recovery');

    Route::get('/2fa/re-code', function () { return view('auth.2fa.re-code'); })->name('2fa.qrCode');
});
