<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ConfirmPassword
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 检查上次密码确认时间
        if ($request->session()->has('auth.password_confirmed_at') &&
            time() - $request->session()->get('auth.password_confirmed_at') < (60 * 10)) {
            return $next($request);
        }

        return redirect()->route('password.confirm');
    }
}