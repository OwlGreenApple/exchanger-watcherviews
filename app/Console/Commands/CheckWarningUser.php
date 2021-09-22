<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

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
        $users = User::where('warning','>',0)->get();
        $id = array();

        if($users->count() > 0)
        {
            foreach($users as $row):
                $id[] = $row->id;
            endforeach;
        }

        // SET WARNING TO 0 IF USER HAVE WARNING
        if(count($id) > 0)
        {
            $user = User::whereIn('id',$id)->update(['warning'=>0]);
        }
    }
}
