<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Models\Transaction;
use App\Helpers\Price;
use Carbon\Carbon;

class CheckBuyerLimit
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
        $tr = Transaction::where([['id',$request->id],['status',0]])->first();

        if(is_null($tr))
        {
            return response()->json(['err'=>1]);
        }

        return self::check_total_success_transaction($tr->total,$request,$next);
    }

    private function check_total_success_transaction($current_transaction,$request,$next)
    {
        $buyer_id = Auth::id();
        $pc = new Price;
        $tr = Transaction::where([['buyer_id',$buyer_id],['status',3]])->get();
        $total_success = $tr->count();

        $current_transaction = $pc::get_rate() * $current_transaction;
        $limit = $pc::buyer_limit_day($total_success);

        // count total transaction by buyer / day
        $current_day = Carbon::now()->toDateString();
        $trs = Transaction::where([['buyer_id',$buyer_id],['status','>',0]])->whereRaw("DATE(date_buy) = CURDATE()")->selectRaw('COALESCE(SUM(total),0) AS total_money')->first();

        $total_money = $trs->total_money + $current_transaction;

        if($total_money > $limit)
        {
            return response()->json(['err'=>'limit','total'=>$total_success,'shop_day'=>Lang::get('custom.currency').' '.$pc->pricing_format($total_money)]);
        }
        else
        {
            return $next($request);
        }
    }
}
