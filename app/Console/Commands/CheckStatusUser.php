<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class CheckStatusUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user\'s suspend status and set according on membership status after a week';

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
        $current_time = Carbon::now();
        $users = User::where('status',3)->get();

        if($users->count() > 0)
        {
            foreach($users as $row):
                if($current_time->gte(Carbon::parse($row->suspend_date)->addWeek()))
                {
                    $user = User::find($row->id);
                    self::restore_user_status($user);
                }
            endforeach;
        }
    }

    public static function restore_user_status($user)
    {
        if($user->membership == 'free')
        {
            $status = 1;
        }
        else
        {
            $status = 2;
        }

        $user->suspend = 0;
        $user->suspend_date = null;
        $user->status = $status;
        $user->save();
    }

/*end class*/
}
