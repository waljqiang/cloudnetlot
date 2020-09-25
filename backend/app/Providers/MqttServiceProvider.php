<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Utils\Mqtt;

class MqttServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mqtt', function ($app) {
            return new Mqtt(config('mqtt.options'));
        });
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides(){
        return ['mqtt'];
    }
}
