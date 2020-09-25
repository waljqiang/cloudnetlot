<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Utils\Passport\Passport;
use Carbon\Carbon;
use App\Utils\Passport\Scope;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //passport认证
        /*Passport::routes();
        Passport::tokensExpireIn(Carbon::now()->addMinutes(config('open.token_expire_in')));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(config('open.refresh_token_expire_in')));*/
        //scope控制
        //Passport::tokensCan(config('open.scopes'));
    }
}
