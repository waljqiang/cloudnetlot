<?php
namespace App\Utils\Passport;

use League\OAuth2\Server\Grant\RefreshTokenGrant as Grant;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Events\AccessTokenRefreshed;

/**
 * Refresh token grant.
 */
class RefreshTokenGrant extends Grant{

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
        $oldRefreshToken = $this->validateOldRefreshToken($request, $client->getIdentifier());
        $scopes = $this->validateScopes($this->getRequestParameter(
            'scope',
            $request,
            implode(self::SCOPE_DELIMITER_STRING, $oldRefreshToken['scopes']))
        );

        // The OAuth spec says that a refreshed access token can have the original scopes or fewer so ensure
        // the request doesn't include any new scopes
        foreach ($scopes as $scope) {
            if (in_array($scope->getIdentifier(), $oldRefreshToken['scopes']) === false) {
                throw OAuthServerException::invalidScope($scope->getIdentifier());
            }
        }

        // Expire old tokens
        /*$this->accessTokenRepository->revokeAccessToken($oldRefreshToken['access_token_id']);
        $this->refreshTokenRepository->revokeRefreshToken($oldRefreshToken['refresh_token_id']);*/

        // Issue and persist new tokens
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $oldRefreshToken['user_id'], $scopes);
        //$refreshToken = $this->issueRefreshToken($accessToken);

        // Inject tokens into response
        $responseType->setAccessToken($accessToken);
        //$responseType->setRefreshToken($refreshToken);

        return $responseType;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string                 $clientId
     *
     * @throws OAuthServerException
     *
     * @return array
     */
    protected function validateOldRefreshToken(ServerRequestInterface $request, $clientId){
        $encryptedRefreshToken = $this->getRequestParameter('refresh_token', $request);
        if (is_null($encryptedRefreshToken)) {
            throw OAuthServerException::invalidRefreshToken('Refresh token must be required');
        }

        // Validate refresh token
        try {
            $refreshToken = $this->decrypt($encryptedRefreshToken);
        } catch (\Exception $e) {
            throw OAuthServerException::invalidRefreshToken('Cannot decrypt the refresh token');
        }

        $refreshTokenData = json_decode($refreshToken, true);
        if ($refreshTokenData['client_id'] !== $clientId) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::REFRESH_TOKEN_CLIENT_FAILED, $request));
            throw OAuthServerException::invalidRefreshToken('Refresh Token is not linked to client');
        }

        if ($refreshTokenData['expire_time'] < time()) {
            throw OAuthServerException::invalidRefreshToken('Refresh Token has expired');
        }

        if ($this->refreshTokenRepository->isRefreshTokenRevoked($refreshTokenData['refresh_token_id']) === true) {
            throw OAuthServerException::invalidRefreshToken('Refresh Token has been revoked');
        }

        return $refreshTokenData;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'refresh_token';
    }
}
