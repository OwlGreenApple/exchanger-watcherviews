<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;
use App\Models\Transaction;

class CheckEndMembership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $membership = Auth::user()->membership;
        $trial = Auth::user()->trial;
        $end_membership = Carbon::parse(Auth::user()->end_membership)->toDateTimeString();

        if($request->id !== null)
        {
            $tr_trial = null;
             $tr = Transaction::find($request->id);
             if(!is_null($tr))
             {
                $tr_trial = $tr->trial;
             }

             // EXCEPTIONAL FOR TRANSACTION CREATED FROM TRIAL
             if($tr_trial == 1)
             {
                return $next($request);
             }
        }

        $valid_membership = true;
        if(Auth::user()->end_membership !== null)
        {
            if(Carbon::now()->gte($end_membership))
            {
                $valid_membership = false;
            }
        }

        if( ($membership == 'free' && $trial == 0) || $valid_membership == false)
        {
            return response()->json(['err'=>'trial']);
        }

        return $next($request);
    }
}
