<?php
namespace Database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DummyTableSeeder extends Seeder
{
  //php artisan db:seed --class=DummyTableSeeder
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $user = new User;
      $user->email="rizkyredjo@gmail.com";
      $user->password=Hash::make("testrizky");
      $user->name="superview";
      $user->phone_number="08123238793";
      $user->email_verified_at=Carbon::now();
      $user->is_admin=1;
      $user->coin=99999;
      $user->gender=1;
      $user->save();
    }
}
