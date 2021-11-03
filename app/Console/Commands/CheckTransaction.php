<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\AdminController as adm;
use App\Http\Controllers\BuyerController;
use App\Models\Transaction;
use App\Helpers\Messages;
use Carbon\Carbon;

class CheckTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:transaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check transaction and dispute';

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
        $tr = Transaction::whereIn('status',[1,2,7])->get();

        if($tr->count() > 0)
        {
            foreach($tr as $row):
                $req_buy = Carbon::parse($row->date_buy)->addHours(24)->toDateTimeString();
                $dispute_date = Carbon::parse($row->updated_at)->addHours(12)->toDateTimeString();
                $cancel_order = Carbon::parse($row->updated_at)->addHours(6)->toDateTimeString();
                $str = Transaction::find($row->id);

                // in case if seller doen't make respond
                if(Carbon::now()->gte($req_buy) && $row->status == 1):
                    $str->status = 2;
                    $str->save();

                    // MAIL TO BUYER IF ORDER HAS ACCEPTED
                    $buy = new BuyerController;
                    $buy::notify_buyer($row->no,$row->buyer_id,$row->id);
                endif;

                // in case to buyer doesn't make confirmation
                if(Carbon::now()->gte($cancel_order) && $row->status == 2):
                    $str->buyer_id = 0;
                    $str->date_buy = null;
                    $str->kurs = 0;
                    $str->total = $str->amount;
                    $str->status = 0;
                    $str->save();
                endif;

                // in case to show dispute button
                if(Carbon::now()->gte($dispute_date) && $row->status == 7):
                    $str->status = 4;
                    $str->save();
                endif;
            endforeach;
        }
    }

/*end class*/
}
