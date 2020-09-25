<?php

namespace App\Listeners;

use App\Events\AccessTokenCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Utils\Passport\AccessTokenRepository;
use App\Utils\Passport\RefreshTokenRepository;

class RevokeOldTokens{
    private $accessTokenRepository;
    private $refreshTokenRepository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(AccessTokenRepository $accessTokenRepository,RefreshTokenRepository $refreshTokenRepository){
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle(AccessTokenCreated $event){
        $invalidTokens = $this->accessTokenRepository->findInvalidTokens($event->tokenId,$event->clientId,$event->userId);
        $invalidTokenIDs = $invalidTokens->pluck('id');
        $this->accessTokenRepository->revokeAccessTokens($invalidTokenIDs);
        $this->refreshTokenRepository->revokeRefreshTokens($invalidTokenIDs);
    }
}
