<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\OpenAdmin\Repositories\UserRepository;
use App\Utils\Passport\ClientRepository;
use Modules\OpenAdmin\Repositories\ScopeRepository;
use Carbon\Carbon;

class OpenClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open:client {account} {scopes} {--clientname= : The name of the client}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a client for issuing access tokens';

    /**
     * @var App\Utils\Passport\ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var Modules\Open\Repositories\UserRepository
     */
    protected $userRepository;

    protected $scopeRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository,ClientRepository $clientRepository,ScopeRepository $scopeRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->clientRepository = $clientRepository;
        $this->scopeRepository = $scopeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $account = $this->argument('account');
        $scopes = $this->argument('scopes');
        $clientName = $this->option('clientname') ? $this->option('clientname') : getStr(3,'abcdefghijklmnopqrstuvwxyz') . getStr(13);
        $scopes = explode(',',$scopes);
        $allScopes = config('open.scopes');
        $allScopesKeys = array_keys($allScopes);
        $dataScopes = [];
        foreach ($scopes as $scope) {
            if(!in_array($scope,$allScopesKeys)){
                $this->error('The scope[' . $scope .'] is unsupport');
                exit(-1);
            }else{
                $dataScopes[$scope] = $allScopes[$scope];
            }
        }
        $time = Carbon::now()->timestamp;
        $user = $this->userRepository->getUser([['account',$account]],[],['id','account'],true);
        if(!$user){
            $this->error('The user of ' . $userName . ' is not exsist');
            exit(-1);
        }
        $client = $this->clientRepository->findClientForUser([['user_id',$user->id]]);
        if(!$client){//客户端不存在,创建客户端
            $client = $this->clientRepository->createPasswordGrantClient($user->id,$clientName,'http:localhost');
            $this->scopeRepository->addScopes(
                [
                    'client_id' => $client->id,
                    'scopes' => json_encode($dataScopes),
                    'create_time' => $time,
                    'update_time' => $time
                ]
            );
        }elseif($client->revoked){//客户端存在但禁用时启用客户端
            $rs = $this->clientRepository->enable($client);
            if(!$rs){
                $this->error('Create client failure');
                exit(-1);
            }
        }else{
            $this->error('The client of the user exists');
           exit(-1);
        }
        $hashids = resolve('Hashids');
        $this->info('New client created successfully.');
        $this->line("<comment>client_id:{$hashids->encodeHash($client->id)}</comment>");
        $this->line("<comment>secret:{$client->secret}</comment>");
    }
}
