<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Waljqiang\Yunlot\Yunlot;

class YunlotServiceProvider extends ServiceProvider
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
        $this->app->singleton("yunlot", function ($app) {
            return new Yunlot(["protocol" => config("yunlot.protocol"),"encodetype" => config("yunlot.encodetype"),"token" => config("yunlot.token"),"key" => config("yunlot.key")]);
        });
    }

    /**
     * 获取由提供者提供的服务.
     *
     * @return array
     */
    public function provides(){
        return ["yunlot"];
    }
}
