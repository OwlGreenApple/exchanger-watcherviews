<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Atcoupons;
use App\Http\Controllers\Admin\AdminController as Admin;
use App\Mail\WarningEmail;

class CheckActivTemplateCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:atm_coupon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check activtemplate available coupons';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $at1 = Atcoupons::where([['is_used',0],['type',1]])->count();
        $at2 = Atcoupons::where([['is_used',0],['type',2]])->count();

        if($at1 < 20 && $at2 < 20)
        {
            return self::notify_admin(null);
        }

        if($at1 < 20)
        {
            return self::notify_admin(1);
        }

        if($at2 < 20)
        {
            return self::notify_admin(2);
        }
    }

    private static function notify_admin($type)
    {
        $admin = new Admin;

        if($type==1)
        {
            $vc = 'Rp.100.000';
        }
        else if($type==2)
        {
            $vc = 'Rp.200.000';
        }
        else
        {
            $vc = null;
        }

        $msg ='';
        $msg .='Jumlah kupon activtemplate '.$vc.' kurang dari 20, segera generate dan import disini :'."\n";
        $msg .=url('atm-coupon-list'); 

        $data = [
              'message'=>$msg,
              'phone_number'=>env('ADMIN_PHONE'),
              'email'=>env('ADMIN_MAIL'),
              'obj'=>new WarningEmail($vc,null,'Kupon Activtemplate'),
        ];
              
        $admin->notify_user($data);
    }
}
