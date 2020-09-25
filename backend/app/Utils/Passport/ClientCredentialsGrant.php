<?php
/**
 * OAuth 2.0 Client credentials grant.
 *
 * @author      Alex Bilbie <hello@alexbilbie.com>
 * @copyright   Copyright (c) Alex Bilbie
 * @license     http://mit-license.org/
 *
 * @link        https://github.com/thephpleague/oauth2-server
 */

namespace App\Utils\Passport;

use League\OAuth2\Server\Grant\ClientCredentialsGrant as Grant;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use App\Utils\Passport\Scope;
use App\Events\AccessTokenCreated;
use App\Utils\Passport\Passport;

/**
 * Client credentials grant class.
 */
class ClientCredentialsGrant extends Grant{

    /**
     * @param UserRepositoryInterface         $userRepository
     * @param RefreshTokenRepositoryInterface $refreshTokenRepository
     */
    public function __construct(
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setRefreshTokenRepository($refreshTokenRepository);

        $this->refreshTokenTTL = new \DateInterval('P1M');
    }
    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        //添加默认接口访问范围
        $clientID = $client->getIdentifier();
        $defaultScope = Scope::where('client_id',$clientID)->first()->scopes;
        Passport::tokensCan($defaultScope);
        $this->setDefaultScope(implode(self::SCOPE_DELIMITER_STRING,array_keys($defaultScope)));
        //校验scope
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client);

        // Issue and persist access token and refresh token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, null, $finalizedScopes);
        $refreshToken = $this->issueRefreshToken($accessToken);
        
        event(new AccessTokenCreated(
            $accessToken->getIdentifier(),
            $accessToken->getUserIdentifier(),
            $accessToken->getClient()->getIdentifier()
        ));

        // Inject access token and refresh token into response type
        $responseType->setAccessToken($accessToken);
        $responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'client_credentials';
    }
}
