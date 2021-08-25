<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Orders;
use App\Http\Controllers\OrderController;
use App\Helpers\Api;

class CheckPurchasing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:purchase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check whether user make confirmation or not, if status user = 0 then will get WA message 6 hours after created at';

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
        $orders = Orders::where('orders.status','<',2)->leftJoin('users','users.id','=','orders.user_id')->select('orders.*','users.phone_number')->get();
        $api = new Api;
        $otc = new OrderController;

        if($orders->count() > 0)
        {
            foreach($orders as $row):
                $curtime = Carbon::now();
                $day = Carbon::parse($row->created_at)->addDays(1);
                $hours = Carbon::parse($row->created_at)->addHour(6);

                // SET USER STATUS TO 3 IF AFTER 24 HOURS USER DOESN'T PAY ORDER
                if($curtime->gte($day))
                {
                   $order = Orders::find($row->id);
                   $order->status = 3;
                   $order->save();
                }
                
                // SEND WA MESSAGE AFTER 6 HOURS ORDER
                if($curtime->gte($hours))
                {
                    $otc->send_message($row->package,$row->price,$row->total,$row->no_order,$row->phone_number,1);
                }
            endforeach;
        }
    }

/**/
}
