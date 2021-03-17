<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Drips;
use App\Http\Controllers\CoinsController as Coins;
use Carbon\Carbon;

class CheckDrip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:drip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled drip video to watcherviews';

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
        $drips = Drips::where('drips.status',0)
                ->leftJoin('exchanges','exchanges.id','=','drips.exchange_id')
                ->select('drips.id','drips.schedule','drips.status','exchanges.yt_link','exchanges.duration','exchanges.views')
                ->get();

        if($drips->count() > 0):
          foreach($drips as $row)
          {
              $current_time = Carbon::now();
              if($current_time->gte(Carbon::parse($row->schedule)))
              {
                  $drp = Drips::find($row->id);
                  $drp->status = 1;
                  $drp->save();

                  $data = [
                    'ytlink'=>$row->yt_link,
                    'duration'=>$row->duration,
                    'views'=>$row->views,
                    'celebfans_id'=>$row->exchange_id
                  ];

                  /* API WATCHERVIEW */
                  Coins::add_youtube_link($data);
              }
          }
        endif;
    }

// END COMMAND
}
