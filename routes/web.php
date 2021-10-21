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
Route::get('logs-8877', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::post('pass_reset', [App\Http\Controllers\Auth\RegisterController::class, 'reset'])->name('pass-reset');

Auth::routes();

/*USER*/
Route::group(['middleware'=>['auth','web','banned']],function()
{
	Route::get('thankyou',[App\Http\Controllers\OrderController::class,'thankyou']);
	Route::get('end', [App\Http\Controllers\HomeController::class, 'end_membership']);
	Route::get('error',[App\Http\Controllers\HomeController::class, 'error']);
	Route::get('connectapi',[App\Http\Controllers\HomeController::class, 'connectapi']);

	Route::group(['middleware'=>['connect_api']],function()
	{
		Route::get('home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('is_user');
		Route::get('event/{id}', [App\Http\Controllers\HomeController::class, 'event_page']);
		Route::get('change-event', [App\Http\Controllers\HomeController::class, 'change_event']);
		Route::post('save-dispute', [App\Http\Controllers\HomeController::class, 'save_dispute'])->middleware('check_dispute');
		Route::get('page-dispute', [App\Http\Controllers\HomeController::class, 'dispute_page']);
		//BUY 
		// Route::get('test-wa',[App\Http\Controllers\BuyerController::class, 'test_wa']);
		Route::get('buy',[App\Http\Controllers\BuyerController::class, 'buying_page']);
		Route::get('buy-list',[App\Http\Controllers\BuyerController::class, 'buyer_table']);

		Route::group(['middleware'=>['suspend']],function()
		{
			Route::get('buy-request',[App\Http\Controllers\BuyerController::class, 'buy_request'])->middleware('buyer_limit');
			Route::get('buy-detail/{invoice}',[App\Http\Controllers\BuyerController::class, 'detail_buy']);
			Route::get('deal/{id}',[App\Http\Controllers\BuyerController::class, 'deal']);
			Route::get('comments/{user_id}/{invoice?}',[App\Http\Controllers\BuyerController::class, 'comments']);
		});
		Route::get('buy-deal',[App\Http\Controllers\BuyerController::class, 'buyer_deal']);
		Route::get('buyer-confirm/{id}',[App\Http\Controllers\BuyerController::class, 'buyer_confirm']);
		Route::get('buy-history',[App\Http\Controllers\BuyerController::class, 'buyer_history']);
		Route::post('buyer-proof',[App\Http\Controllers\BuyerController::class, 'buyer_proof'])->middleware('check_proof');
		Route::post('display-comments',[App\Http\Controllers\BuyerController::class, 'display_comments']);
		Route::post('save-comments',[App\Http\Controllers\BuyerController::class, 'save_comments']);
		Route::get('buyer-dispute/{id}',[App\Http\Controllers\BuyerController::class, 'buyer_dispute']);

		//SELL
		Route::get('sell',[App\Http\Controllers\SellerController::class, 'selling_page']);
		Route::post('selling',[App\Http\Controllers\SellerController::class, 'selling_save'])->middleware(['end_membership','check_sell']);
		Route::get('sell-list',[App\Http\Controllers\SellerController::class, 'display_sell']);
		Route::get('sell-del',[App\Http\Controllers\SellerController::class, 'del_sell'])->middleware(['end_membership']);
		Route::get('sell-detail/{id}',[App\Http\Controllers\SellerController::class, 'detail_sell']);
		Route::get('sell-confirm/{id}',[App\Http\Controllers\SellerController::class, 'sell_confirm']);
		Route::get('sell-confirmed',[App\Http\Controllers\SellerController::class, 'confirm_selling']);
		Route::get('thank-you-sell',[App\Http\Controllers\SellerController::class, 'thank_you']);
		Route::get('seller-dispute/{id}',[App\Http\Controllers\SellerController::class, 'seller_dispute']);
		Route::post('seller-decision',[App\Http\Controllers\SellerController::class, 'seller_decision']);

		// SELLER COMMENTS
		Route::get('comments-seller/{user_id}/{invoice?}',[App\Http\Controllers\SellerController::class,'comments']);

		// WALLET
		Route::get('wallet',[App\Http\Controllers\HomeController::class,'wallet']);
		Route::post('wallet-transaction',[App\Http\Controllers\HomeController::class,'wallet_transaction'])->middleware(['end_membership','check_coin']);
		Route::get('wallet-list',[App\Http\Controllers\HomeController::class,'display_wallet']);

		// CHAT
		Route::get('chat/{trans_id}',[App\Http\Controllers\ChatController::class, 'room']);
		Route::get('display_chat',[App\Http\Controllers\ChatController::class, 'display_chat']);
		Route::post('save-chats',[App\Http\Controllers\ChatController::class, 'save_chat']);
	});

	// SETTINGS
	// Route::get('trade',[App\Http\Controllers\HomeController::class, 'trade']);
	Route::post('connect-api',[App\Http\Controllers\HomeController::class, 'connect_api'])->middleware('check_connection');
	Route::get('logout-watcherviews',[App\Http\Controllers\SettingController::class, 'logout_watcherviews']);
	
	// ACCOUNT
	Route::get('orders',[App\Http\Controllers\HomeController::class, 'order_list']);
	Route::post('order-confirm-payment',[App\Http\Controllers\HomeController::class, 'confirm_payment_order']);
	Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->middleware('check_profile');
	Route::post('/save-bank-payment', [App\Http\Controllers\HomeController::class, 'save_bank_payment']);
	Route::get('account/{conf?}', [App\Http\Controllers\HomeController::class, 'account']);
	Route::post('payment-upload', [App\Http\Controllers\HomeController::class, 'payment_upload']);
	Route::get('delete-payment', [App\Http\Controllers\HomeController::class, 'delete_payment']);
});

/*ADMIN*/
Route::group(['middleware'=>['auth','web','is_admin','banned']],function()
{
	Route::get('dispute-admin',[App\Http\Controllers\Admin\AdminController::class,'dispute']);
	Route::get('dispute-list-admin',[App\Http\Controllers\Admin\AdminController::class,'display_dispute']);
	Route::get('dispute-user',[App\Http\Controllers\Admin\AdminController::class,'dispute_user']);
	Route::get('dispute-notify',[App\Http\Controllers\Admin\AdminController::class,'dispute_notify']);
	Route::get('dispute-notify-users',[App\Http\Controllers\Admin\AdminController::class,'dispute_notify_user']);

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

	// AUTH LOGIN WITH ID
	Route::get('loginauthid/{id}',[App\Http\Controllers\Admin\AdminController::class,'LoginUser']);
});