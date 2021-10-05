<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CheckWarningUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:warning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore user\'s warning to 0 every 1st month';

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
        $users = User::where([['warning','>',0],['status','>',0]])->orWhere('suspend','>',0)->select('id','suspend_date')->get()->toArray();

        if(count($users) > 0)
        {
            foreach($users as $row):
                $user = User::find($row['id']);
                $date_suspend = Carbon::parse($row['suspend_date'])->addMonths(3)->toDateString();

                if(Carbon::today()->lt($date_suspend))
                {
                    continue;
                }

                if($user->suspend > 0)
                {
                    $user->suspend = 0;
                    $user->suspend_date = Carbon::now();
                    $user->save();
                    continue;
                }
                
                if($user->warning > 0)
                {
                    $user->warning = 0;
                    $user->suspend_date = null;
                    $user->save();
                }
            endforeach;
        }
    }
}
