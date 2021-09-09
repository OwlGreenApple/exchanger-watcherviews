<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Helpers\Price;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\Auth\RegisterController::class, 'price_page']);
Route::get('checkout/{id?}', [App\Http\Controllers\OrderController::class, 'index']);
Route::post('submit_payment',[App\Http\Controllers\OrderController::class, 'submit_payment'])->middleware('check_valid_order');
Route::get('summary',[App\Http\Controllers\OrderController::class, 'summary']);

/*AUTH*/
Route::post('offer',[App\Http\Controllers\Auth\RegisterController::class, 'offer_upgrade']);
Route::get('register-redirect',[App\Http\Controllers\Auth\RegisterController::class, 'register_redirect']);
Route::post('loginajax',[App\Http\Controllers\Auth\LoginController::class, 'loginAjax']);// user login via ajax

Auth::routes();

/*USER*/
Route::group(['middleware'=>['auth','web']],function()
{
	Route::get('end', [App\Http\Controllers\HomeController::class, 'end_membership']);
	Route::get('thankyou',[App\Http\Controllers\OrderController::class,'thankyou']);
	Route::get('home', [App\Http\Controllers\HomeController::class, 'index']);
	Route::get('purchase', [App\Http\Controllers\HomeController::class, 'purchase']);
	Route::get('comments/{sellerid?}',[App\Http\Controllers\HomeController::class, 'comments']);

	//BUY
	Route::get('buy',[App\Http\Controllers\HomeController::class, 'buying_page']);
	Route::get('buy-detail',[App\Http\Controllers\HomeController::class, 'detail_buy']);
	Route::get('deal',[App\Http\Controllers\HomeController::class, 'deal']);
	Route::get('buyer-confirm',[App\Http\Controllers\HomeController::class, 'buyer_confirm']);
	Route::get('buyer-dispute',[App\Http\Controllers\HomeController::class, 'buyer_dispute']);

	//SELL
	Route::get('sell',[App\Http\Controllers\SellerController::class, 'selling_page']);
	Route::post('selling',[App\Http\Controllers\SellerController::class, 'selling_save']);
	Route::get('transfer',[App\Http\Controllers\HomeController::class, 'transfer']);
	Route::get('seller-dispute',[App\Http\Controllers\HomeController::class, 'seller_dispute']);

	// SETTINGS
	// Route::get('trade',[App\Http\Controllers\HomeController::class, 'trade']);
	Route::post('connect-api',[App\Http\Controllers\HomeController::class, 'connect_api'])->middleware('check_connection');
	Route::get('logout-watcherviews',[App\Http\Controllers\SettingController::class, 'logout_watcherviews']);

	// WITHDRAW COIN TO WALLET OR SEND COIN TO WATCHERVIEWS
	Route::get('wallet',[App\Http\Controllers\HomeController::class,'wallet']);
	Route::post('wallet-transaction',[App\Http\Controllers\HomeController::class,'transaction_watcherviews_coin'])->middleware(['end_membership','check_coin']);
	
	// ACCOUNT
	Route::get('orders',[App\Http\Controllers\HomeController::class, 'order_list']);
	Route::post('order-confirm-payment',[App\Http\Controllers\HomeController::class, 'confirm_payment_order']);
	Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->middleware('check_profile');
	Route::get('account/{conf?}', [App\Http\Controllers\HomeController::class, 'account']);
});

/*ADMIN*/
Route::group(['middleware'=>['auth','web','is_admin']],function()
{
	Route::get('kurs-admin',[App\Http\Controllers\Admin\AdminController::class,'trade']);
	Route::get('user-list',[App\Http\Controllers\Admin\AdminController::class,'user_list']);
	Route::get('user-fetch',[App\Http\Controllers\Admin\AdminController::class,'fetch_user']);
	Route::get('user-ban',[App\Http\Controllers\Admin\AdminController::class,'ban_user']);
	Route::get('order-list',[App\Http\Controllers\Admin\AdminController::class,'index']);
	Route::get('order-load',[App\Http\Controllers\Admin\AdminController::class,'order']);
	Route::get('order-confirm',[App\Http\Controllers\Admin\AdminController::class,'confirm_order']);
	Route::post('save-rate',[App\Http\Controllers\Admin\AdminController::class,'save_rate']);

	// WA MESSAGE
	Route::get('wa-message',[App\Http\Controllers\Admin\AdminController::class,'set_order_message']);
	Route::post('save-message',[App\Http\Controllers\Admin\AdminController::class,'save_message']);
});