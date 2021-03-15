<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Exchange;
use Illuminate\Database\QueryException;
use App\Http\Controllers\CoinsController as Coins;

class CheckRefill extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:refill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check video if eligible to get fill';

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
       $exc = Exchange::select('id','total_views','yt_before','yt_after','refill','refill_api')->get();
       if($exc->count() > 0)
       {
          foreach($exc as $row):
          
            $exchange = Exchange::find($row->id);

            /*MANUAL REFILL*/
            if($row->refill == 1 && $row->refill_api == 1)
            {
               $this->manual_refill($row,$exchange);
            }
            else if($row->refill == 2 && $row->refill_api == 1)
            {
              /*AUTOMATIC REFILL*/
              $coin = new Coins;
              $coin->refill($row->id,true);
            }
            else
            {
              continue;
            }
          endforeach;
       }
    }

    private function manual_refill($row,$exchange)
    {
      $calculate_view = $row->yt_after - $row->yt_before;

      if($calculate_view < $row->total_views):
        $exchange->refill_btn = 1;
        try{
          $exchange->save();
        }
        catch(QueryException $e)
        {
          //$e->f=getMessage(); 
        }
      endif;
    }

/*end of class*/
}
