<?php
namespace App\Utils\Passport;

use Laravel\Passport\ClientRepository as BaseRepository;
use Laravel\Passport\Client;

class ClientRepository extends BaseRepository{

	public function findActiveForUser($where){
		return Client::where($where)->where('revoked',0)->first();
	}

    public function findClientForUser($where){
        return Client::where($where)->first();
    }

    public function enable(Client $client){
        return $client->forceFill(['revoked' => false])->save();
    }

    public function disable(Client $client){
        try{
            $this->delete($client);
            return true;
        } catch(\Exception $e){
            return false;
        }
    }

	/**
	 * rewrite parent create
     * Store a new client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirectsub
     * @param  bool  $personalAccess
     * @param  bool  $password
     * @return \Laravel\Passport\Client
     */
    public function create($userId, $name, $redirect, $personalAccess = false, $password = false){
        $client = (new Client)->forceFill([
            'user_id' => $userId,
            'name' => $name,
            'secret' => str_random(40),
            'redirect' => $redirect,
            'personal_access_client' => $personalAccess,
            'password_client' => $password,
            'revoked' => false,
        ]);

        $client->save();

        return $client;
    }
}