<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * 定义路由模型绑定等
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        
        $this->routes(function () {
            // Web 路由
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
            
            // Admin 路由 - 修复中间件为 auth:admin
            Route::middleware(['web', 'admin.validateReferer', 'auth:admin'])
                ->prefix('admin')
                ->as('admin.')
                ->group(base_path('routes/admin.php'));
        });
    }

    /**
     * 配置请求频率限制
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}