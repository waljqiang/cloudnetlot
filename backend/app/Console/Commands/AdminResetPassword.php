<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use Modules\Admin\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;

class AdminResetPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:resetpassword';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset information for the admin';
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
        $oldpassword = $this->secret('Please input the old password for the admin');
        if(!Hash::check($oldpassword,$admin->password)){
            $this->error("The old password is incorrect.");
            exit(-1);
        }
        $newpassword = $this->secret('Please input the new password for the admin');
        if(!preg_match("/^[a-zA-Z0-9_-]{6,20}$/",$newpassword)){
            $this->error("The new password is invalid");
            exit(-1);
        }
        $rs = $admin->update([
            "password" => bcrypt($newpassword),
            "updated_at" => Carbon::now()->timestamp
        ]);
        if($rs){
            $this->info("Successfully.");
            exit(-1);
        }else{
            $this->error("Failure.");
            exit(-1);
        }
    }
}
