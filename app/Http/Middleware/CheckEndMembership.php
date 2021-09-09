<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;

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

        if( ($membership == 'free' && $trial == 0) || (Carbon::now()->gte($end_membership)))
        {
            return response()->json(['err'=>'trial']);
        }

        return $next($request);
    }
}
