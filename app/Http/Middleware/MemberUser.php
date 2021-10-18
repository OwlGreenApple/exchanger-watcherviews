<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MemberUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $iduser = Auth::id();
        $user = User::where([['id',$iduser],['is_admin',0]])->first();

        if(is_null($user)){
            return redirect('account');
        }
        return $next($request);
    }
}
