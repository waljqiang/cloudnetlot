<?php

namespace App\Utils\Passport;

use App\Utils\Passport\RouteRegistrar;
use Illuminate\Support\Facades\Route;

use Laravel\Passport\Passport as PassportBase;

class Passport extends PassportBase{

    /**
     * Binds the Passport routes into the controller.
     *
     * @param  callable|null  $callback
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = []){
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router,'auth'));
        });
    }

}
