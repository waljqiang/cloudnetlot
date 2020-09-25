<?php

namespace App\Utils\Passport;

use Laravel\Passport\Bridge\RefreshTokenRepository as BaseRepository;
use Illuminate\Database\Connection;
use Illuminate\Contracts\Events\Dispatcher;
use App\Utils\Passport\AccessTokenRepository;

class RefreshTokenRepository extends BaseRepository{
    /**
     * Create a new repository instance.
     *
     * @param  \Laravel\Passport\Bridge\AccessTokenRepository  $tokens
     * @param  \Illuminate\Database\Connection  $database
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(AccessTokenRepository $tokens,Connection $database,Dispatcher $events){
    	parent::__construct($tokens,$database,$events);
    }


    public function revokeRefreshTokens($tokenIds){
        $this->database->table('oauth_refresh_tokens')
                    ->whereIn('access_token_id', $tokenIds)->update(['revoked' => true]);
    }

}
