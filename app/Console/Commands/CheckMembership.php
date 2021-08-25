<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Membership;
use App\Models\Orders;
use Carbon\Carbon;

class CheckMembership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:membership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'turn user status to 1 and membership to free when time to end membership reached';

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
        $users = User::where('status',2)->get();
        $today = Carbon::now();

        if($users->count() > 0)
        {
            foreach($users as $row):
                $user = User::find($row->id);
                $end_membership = Carbon::parse($user->end_membership)->toDateTimeString();

                if($today->gte($end_membership))
                {
                    $user->membership = 'free';
                    $user->status = 1;
                    $user->save();
                }
            endforeach;
        }

        // TO CHECK ORDER LATER FROM MEMBERSHIPS
        $order_later = User::where('status',1)->get();
        if($order_later->count() > 0)
        {
            foreach($order_later as $row):
                $member = User::find($row->id);
                self::check_membership($member);
                sleep(0.5);
            endforeach;
        }
    }

    public static function check_membership($user)
    {
        $user_id = $user->id;
        $today = Carbon::now();
        $mb = Membership::where([['user_id',$user_id],['status',0]])->orderBy('id','asc')->get();

        if($mb->count() > 0)
        {
            foreach($mb as $row):
                $start = Carbon::parse($row->start)->toDatetimeString();
                $end = $row->end;
                $order = Orders::find($row->order_id);

                if($today->gte($start))
                {
                    $user->membership = $order->package;
                    $user->end_membership = $end;
                    $user->status = 2;
                    $user->save();

                    $ms = Membership::find($row->id);
                    $ms->status = 1;
                    $ms->save();
                }
            endforeach;
        }
    }

/**/
}
