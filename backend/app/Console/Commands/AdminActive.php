<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Modules\Admin\Repositories\AdminRepository;

class AdminActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Active admin for the platform of manager';

    private $adminRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AdminRepository $adminRepository){
        parent::__construct();
        $this->adminRepository = $adminRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $admin = $this->adminRepository->getInfos([
            ["username",config("public.default.admin")]
        ],[],["*"],true);
        if(!$admin){
            $this->error('Failure.');
            exit(-1);
        }

        if($admin->status == 1){
            $this->info('The admin is already Actived.');
            exit(-1);
        }

        $rs = $admin->update([
            "status" => 1,
            "updated_at" => Carbon::now()->timestamp
        ]);
        if($rs){
            $this->info('Successfully.');
            exit(-1);
        }else{
            $this->error('Failure.');
            exit(-1);
        }
    }
}
