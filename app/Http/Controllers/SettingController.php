<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\QueryException;
use App\Models\User;
use App\Helpers\Price;
use App\Helpers\Api;
use Carbon\Carbon;
use Storage, Session;

class SettingController extends Controller
{
    // WATCHERVIEWS LOGOUT
    public function logout_watcherviews()
    {
        $user = User::find(Auth::id());

        try
        {
            $user->watcherviews_id = 0;
            $user->save();
            $data['err'] = 0;
        }
        catch(Queryexception $e)
        {
            $data['err'] = $e->getMessage();
        }

        return response()->json($data); 
    }
}
