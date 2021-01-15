<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
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
    protected $description = 'To check validate user membership';

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

        $users = User::where('membership','<>',null)->get();
        $today = Carbon::today();

        if($users->count() > 0):
          foreach ($users as $row):

            $user = User::find($row->id);
            $valid_until = Carbon::parse($user->valid_until)->setTime(0, 0, 0);

            // dd($valid_until);

            if($today->gte($valid_until))
            {
              $user->membership = null;
              $user->save();
            }
    
          endforeach;
        endif;
    }
}
