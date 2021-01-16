<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;
use Lcobucci\JWT\Parser;
use App\Repositories\CacheRepository;
use Illuminate\Support\Facades\DB;
use Modules\Home\Repositories\UserRepository;
use Modules\Home\Repositories\CommandRepository;

/**
 * 没有控制清楚流程，暂时不使用
 */
class WmsgPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "w-message-push {action=start : start | restart | reload(平滑重启) | stop | status | connections} {port=9093} {--d : daemon or debug}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "websocket消息推送服务";
    //token解析器
    private $parser;
    //signer
    private $signer;

    private $signers = [
        "ES256" => \Lcobucci\JWT\Signer\Ecdsa\Sha256::class,
        "ES384" => \Lcobucci\JWT\Signer\Ecdsa\Sha256::class,
        "ES512" => \Lcobucci\JWT\Signer\Ecdsa\Sha512::class,
        "HS256" => \Lcobucci\JWT\Signer\Hmac\Sha256::class,
        "HS384" => \Lcobucci\JWT\Signer\Hmac\Sha384::class,
        "HS512" => \Lcobucci\JWT\Signer\Hmac\Sha512::class,
        "RS256" => \Lcobucci\JWT\Signer\Rsa\Sha256::class,
        "RS384" => \Lcobucci\JWT\Signer\Rsa\Sha384::class,
        "RS512" => \Lcobucci\JWT\Signer\Rsa\Sha512::class,
    ];

    private $cacheRepository;
    private $userRepository;
    private $commandRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Parser $parser,CacheRepository $cacheRepository,UserRepository $userRepository,CommandRepository $commandRepository)
    {
        parent::__construct();
        $this->parser = $parser;
        $this->signer = new $this->signers[config("jwt.algo")];
        $this->cacheRepository = $cacheRepository;
        $this->userRepository = $userRepository;
        $this->commandRepository = $commandRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        global $argv;
        $argv[0] = "MsgPush";
        $argv[1] = $this->argument("action");//start | restart | reload(平滑重启) | stop | status | connections
        $argv[2] = $this->option("d") ? "-d" : "";//守护进程模式或调试模式启动
        $port = $this->argument("port");
        
        Worker::$pidFile = storage_path() . "/logs/msgpush.pid";
        Worker::$logFile = storage_path() . "/logs/workman.log";

        $worker = new Worker("websocket://0.0.0.0:" . $port);
        $worker->name = "ws-msg-push";
        $worker->count = 1;//必须为单进程

        //当有客户端连接时
        $worker->onConnect = function($connection){
            echo $connection->worker->id . "->" . $connection->id . " connected" . PHP_EOL;
            $connection->time = time();
        };

        //当连接关闭时回调
        $worker->onClose = function($connection){
            echo $connection->worker->id . "->" . $connection->id . " closed" . PHP_EOL;
            $connection->close("连接关闭");
        };

        //收到消息处理
        $worker->onMessage = function($connection,$data){
            if(!$uid = $this->auth($data)){
                $connection->close("登录失败");
            }
            if(!($oplogNums = $this->cacheRepository->getUserOplogNums($uid))){
                $user = $this->userRepository->getInfos([["id",$uid]],[],["*"],true);
                if($user->is_primary){//主账号
                    $userIDs = $user->childs->pluck("id")->toArray();
                }
                $userIDs[] = $user->id;
                $oplogNums = $this->commandRepository->statics([
                    ["status",3],
                    [
                        function($query)use($userIDs){
                            $query->whereIn("user_id",$userIDs);
                        }
                    ]
                ]);
                $this->cacheRepository->setUserOplogNums($uid,$oplogNums);
            }
            $connection->send(intval($oplogNums["unreads"]));
        };

        //推送消息
        $worker->onWorkerStart = function($worker){
            //$this->checkHeart($worker);
        };

        Worker::runAll();
    }

    public function auth($data){
        try{
            $token = $this->parser->parse($data);
            if(!$token->verify($this->signer,config("jwt.secret")) || $token->isExpired()){
                return false;
            }
            $uid = $token->getClaim("sub");
            return $uid;
        }catch(\Exception $e){
            return false;
        }
    }

    public function checkHeart($worker){
        \Workerman\Timer::add(30,function()use($worker){
            foreach ($worker->connections as $connection) {
                if(time() - $connection->time > 60 && !isset($connection->uid)){
                    $connection->close();
                }
            }
        });
    }
}
