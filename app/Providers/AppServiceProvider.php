<?php

namespace App\Providers;

use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Enterprise\Service\EnterpriseService;
use App\Services\Mqtt\MqttService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EnterpriseService::class, function ($app) {
            return new EnterpriseService(new Enterprise());
        });

        $this->app->singleton(MqttService::class, function ($app) {
            return new MqttService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // DB::statement("SET time_zone = '+07:00'");
        //
    }
}
