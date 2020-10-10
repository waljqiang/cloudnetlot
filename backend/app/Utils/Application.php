<?php
namespace App\Utils;

use Illuminate\Foundation\Application as Base;
use App\Providers\LogServiceProvider;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Routing\RoutingServiceProvider;

/**
 * rewrite Illuminate\Foundation\Application
 *
 * rewrite LogServiceProvider to set name and path
 */
class Application extends Base{
	/**
     * Register all of the base service providers.
     *
     * @return void
     */
    protected function registerBaseServiceProviders(){
        $this->register(new EventServiceProvider($this));
        $this->register(new LogServiceProvider($this));
        $this->register(new RoutingServiceProvider($this));
    }
}