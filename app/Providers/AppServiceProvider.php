<?php

namespace App\Providers;

use App\Services\AccountService;
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
        //
    }
}
