<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->two_factor_enabled && !session()->has('just_enabled_2fa') && !session()->has('2fa_verified')) {
            session(['url.intended' => $request->url()]);
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}