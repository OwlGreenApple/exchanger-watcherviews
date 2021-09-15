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

        dd($tr);
    }
}
