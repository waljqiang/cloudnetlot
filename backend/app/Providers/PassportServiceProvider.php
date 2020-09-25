<?php
namespace App\Providers;

use DateInterval;
use App\Utils\Passport\Passport;
use App\Utils\Passport\ClientCredentialsGrant;
use App\Utils\Passport\RefreshTokenGrant;
use App\Utils\Passport\TokenGuard;
use App\Utils\Passport\ClientRepository;
use App\Utils\Passport\TokenRepository;

use \Laravel\Passport\PassportServiceProvider as ServiceProvider;
use \League\OAuth2\Server\AuthorizationServer;
use \Laravel\Passport\Bridge\PersonalAccessGrant;
use Illuminate\Auth\RequestGuard;
use \Laravel\Passport\Bridge\RefreshTokenRepository;
use League\OAuth2\Server\ResourceServer;
use Illuminate\Support\Facades\Auth;


class PassportServiceProvider extends ServiceProvider{
    

    /**
     * Register the authorization server.
     *
     * @return void
     */
    protected function registerAuthorizationServer(){
        $this->app->singleton(AuthorizationServer::class, function () {
            return tap($this->makeAuthorizationServer(), function ($server) {
                $server->enableGrantType(
                    $this->makeAuthCodeGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    $this->makeRefreshTokenGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    $this->makePasswordGrant(), Passport::tokensExpireIn()
                );

                $server->enableGrantType(
                    new PersonalAccessGrant, new DateInterval('P1Y')
                );

                $server->enableGrantType(
                    $this->makeCredentialsGrant(), Passport::tokensExpireIn()
                );

                if (Passport::$implicitGrantEnabled) {
                    $server->enableGrantType(
                        $this->makeImplicitGrant(), Passport::tokensExpireIn()
                    );
                }
            });
        });
    }

    /**
     * Make an instance of the token guard
     *
     * @param array $config
     * @return \Illuminate\Auth\RequestGuard
     */
    protected function makeGuard(array $config){
    	return new RequestGuard(function($request) use ($config){
    		return (new TokenGuard(
    			$this->app->make(ResourceServer::class),
    			Auth::createUserProvider($config['provider']),
    			$this->app->make(TokenRepository::class),
    			$this->app->make(ClientRepository::class),
    			$this->app->make('encrypter')
    		))->user($request);
    	},$this->app['request']);
    }

    /**
     * Create and configure a Password grant instance.
     *
     * @return \League\OAuth2\Server\Grant\PasswordGrant
     */
    protected function makeCredentialsGrant(){
        $grant = new ClientCredentialsGrant(
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }

    /**
     * Create and configure a Refresh Token grant instance.
     *
     * @return \League\OAuth2\Server\Grant\RefreshTokenGrant
     */
    protected function makeRefreshTokenGrant(){
        $repository = $this->app->make(RefreshTokenRepository::class);

        return tap(new RefreshTokenGrant($repository), function ($grant) {
            $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());
        });
    }

}
