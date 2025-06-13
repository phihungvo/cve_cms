<?php

// namespace App\Providers;
namespace App\Domains\Mail\Providers;

// use App\Services\Mail\MailService;
use Illuminate\Support\ServiceProvider;
use App\Domains\Mail\Services\MailService;

class MailServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MailService::class, function ($app) {
            return new MailService();
        });
    }

    public function boot()
    {
        // Đăng ký routes từ router.php
        if (file_exists($router = __DIR__ . '/../Controllers/router.php')) {
            $this->loadRoutesFrom($router);
        }
    }
}