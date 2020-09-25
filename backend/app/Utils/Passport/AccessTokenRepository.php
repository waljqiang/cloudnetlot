<?php
namespace App\Utils\Passport;

use Laravel\Passport\Bridge\AccessTokenRepository as BaseRepository;
use App\Utils\Passport\TokenRepository;
use Illuminate\Contracts\Events\Dispatcher;

class AccessTokenRepository extends BaseRepository{

	/**
     * Create a new repository instance.
     *
     * @param  \Laravel\Passport\TokenRepository  $tokenRepository
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     */
    public function __construct(TokenRepository $tokenRepository, Dispatcher $events){
        parent::__construct($tokenRepository,$events);
    }

    public function findInvalidTokens($tokenId,$clientId,$userId = ''){
    	return $this->tokenRepository->findInvalidTokens([
            ['client_id',$clientId],
            ['user_id',$userId],
            ['revoked',false],          
            ['id','!=',$tokenId]
        ]);
    }

    public function findInvalidTokensOnlyId($tokenId){
        return $this->tokenRepository->findInvalidTokensOnlyId($tokenId);
    }

    public function revokeAccessTokens($ids){
    	return $this->tokenRepository->revokeAccessTokens($ids);
    }

}
