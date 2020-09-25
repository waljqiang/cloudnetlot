<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;
use Lcobucci\JWT\Parser;
use Illuminate\Support\Facades\DB;

class WmsgPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "ws-msg-push {action=start : start | restart | reload(平滑重启) | stop | status | connections} {port=9093} {--d : daemon or debug}";

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

    private $uids = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Parser $parser)
    {
        parent::__construct();
        $this->parser = $parser;
        $this->signer = new $this->signers[config("jwt.algo")];
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
            unset($this->uids[$connection->id]);
            $connection->close("连接关闭");
        };

        //认证处理
        $worker->onMessage = function($connection,$data){
            if($uid = $this->auth($connection,$data)){
                if(!isset($connection->uid)){
                    $this->uids[$connection->id] = $uid;
                    $connection->uid = $uid;
                }  
            }
        };

        //推送消息
        $worker->onWorkerStart = function($worker){
            $this->handleData($worker);
        };

        Worker::runAll();
    }

    public function auth($connection,$data){
        try{
            $token = $this->parser->parse($data);
            if(!$token->verify($this->signer,config("jwt.secret")) || $token->isExpired()){
                $connection->close("登录失败");
                return false;
            }
            $uid = $token->getClaim("sub");
            return $uid;
        }catch(\Exception $e){
            $connection->close("登录失败");
            return false;
        }
    }

    public function handleData($worker){
        \Workerman\Timer::add(30,function()use($worker){
            foreach ($worker->connections as $connection) {
                if(time() - $connection->time > 60 && !isset($connection->uid)){
                    $connection->close();
                }
            }
        });

        \Workerman\Timer::add(5, function()use($worker){
            $infos = DB::table("command")->select(["command.user_id",DB::raw("count(" . DB::getConfig('prefix') . "message_read.id) as counts")])->leftJoin("message_read",function($query){
                $query->on("command.comm_id","=","message_read.comm_id")->on("command.user_id","=","message_read.user_id");
            })->whereIn("command.user_id",array_values($this->uids))->groupBy("command.user_id")->get()->pluck("counts","user_id");

            foreach($worker->connections as $connection) {
                if(isset($connection->uid) && in_array($connection->uid,array_values($this->uids)) && isset($infos[$connection->uid])){
                    $connection->send($infos[$connection->uid]);
                }
            }
        });
    }
}
