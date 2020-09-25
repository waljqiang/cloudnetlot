<?php

namespace App\Utils\Passport;

use Laravel\Passport\TokenRepository as BaseRepository;
use Laravel\Passport\Token;

class TokenRepository extends BaseRepository{
    
    public function findInvalidTokens($where){
    	return Token::where($where)->get();
    }

    public function findInvalidTokensOnlyId($tokenId){
    	return Token::where('id','!=',$tokenId)->where('client_id',function($query) use ($tokenId){
    		$query->select('client_id')->from('oauth_access_tokens')->where('id',$tokenId);
    	})->where('revoked',false)->get();
    }

    public function revokeAccessTokens($ids){
    	return Token::whereIn('id', $ids)->update(['revoked' => true]);
    }

}
