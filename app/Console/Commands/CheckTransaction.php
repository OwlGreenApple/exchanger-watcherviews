<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use Carbon\Carbon;;

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
        $tr = Transaction::where(function($query){
            $query->where('status','>',0);
            $query->Where('status','<',3);
        })->get();

        if($tr->count() > 0)
        {
            foreach($tr as $row):
                $update_date = Carbon::parse($row->updated_at)->addHours(3)->toDateTimeString();
                $str = Transaction::find($row->id);

                if(Carbon::now()->gte($update_date)):
                    // in case if buyer doen't make confirmation
                    if($row->status == 1)
                    {
                        $str->status = 0;
                        $str->save();
                    }

                    // in case to show dispute button
                    if($row->status == 2)
                    {
                        $str->status = 4;
                        $str->save();
                    }

                endif;
            endforeach;
        }
    }

/*end class*/
}
