<?php

namespace App\Providers;

use App\Facades\Accounts;
use App\Services\AccountService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(AccountService::class);
        $this->app->alias(AccountService::class, 'accounts');
    }

    public function boot(): void
    {
        RateLimiter::for('mail', function (Request $request) {
            return Limit::perMinute(20)->by($request->user() ? Accounts::current()->id : $request->ip());
        });
    }
}
