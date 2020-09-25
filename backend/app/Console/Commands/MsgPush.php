<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Lcobucci\JWT\Parser;
use App\Repositories\CacheRepository;
use Illuminate\Support\Facades\DB;


class MsgPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "message-push {action=start : start | restart | reload(平滑重启) | stop | status | connections} {port=9093} {--d : daemon or debug}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "消息推送服务";

    //token解析器
    private $tokenParser;

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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Parser $parser,CacheRepository $cacheRepository)
    {
        parent::__construct();
        $this->tokenParser = $parser;
        $this->signer = new $this->signers[config("jwt.algo")];
        $this->cacheRepository = $cacheRepository;
    }

    /**
     *
     * 根据脚本参数开启PHPSocketIO服务
     * PHPSocketIO服务的端口是9093
     */
    public function handle(){
        global $argv;
        $argv[0] = "MsgPush";
        $argv[1] = $this->argument("action");//start | restart | reload(平滑重启) | stop | status | connections
        $argv[2] = $this->option("d") ? "-d" : "";//守护进程模式或调试模式启动
        $port = $this->argument("port");
        
        Worker::$pidFile = storage_path() . "/logs/msgpush.pid";
        Worker::$logFile = storage_path() . "/logs/workman.log";

        //PHPSocketIO服务
        $io = new SocketIO($port);
        //客户端发起连接事件时回调
        $io->on("connection",function($socket){
            //数据处理
            $socket->on("push_oplog_unreads",function($token) use ($socket){
                if($uid = $this->getUid($token)){
                    $total = $this->handleOplogUnreads($uid);
                    $socket->emit("push_oplog_unreads",json_encode([
                        "status" => config("exceptions.SUCCESS"),
                        "data" => $total,
                        "errorCode" => []
                    ]));
                }else{
                    $socket->emit("push_oplog_unreads",json_encode([
                        "status" => config("exceptions.ERROR"),
                        "message" => "Auth failure",
                        "errorCode" => [
                            config("exceptions.AUTH_NO")
                        ]
                    ]));
                    $socket->disconnect();
                }
            });
        });
       
        Worker::runAll();
    }

    public function getUid($token){
        try{
            $token = $this->tokenParser->parse($token);
            //验证token签名
            if(!$token->verify($this->signer,config("jwt.secret")) || $token->isExpired()){
                return "";
            }
            $uid = $token->getClaim("sub");
            return $uid;
        }catch(\Exception $e){
            return "";
        }
    }

    public function handleOplogUnreads($uid){
        if($oplogNums = $this->cacheRepository->getUserOplogNums($uid)){
            return $oplogNums["total"] - $oplogNums["reads"];
        }else{
            $totals = DB::select("select count(m.id) as nums,count(n.id) as readnums from " . DB::getConfig("prefix") . "command m left join " . DB::getConfig("prefix") . "message_read n on m.comm_id=n.comm_id and m.user_id=n.user_id where m.user_id = ? limit 1",[2]);
            $oplogNums = [
                "total" => isset($totals[0]) ? intval($totals[0]->nums) : 0,
                "reads" => isset($totals[0]) ? intval($totals[0]->readnums) : 0
            ];
            $this->cacheRepository->setUserOplogNums($uid,$oplogNums);
        }
        return $oplogNums["total"] - $oplogNums["reads"];
    }
}
