<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('checkout/{id?}', [App\Http\Controllers\OrderController::class, 'index']);
Route::post('submit_payment',[App\Http\Controllers\OrderController::class, 'submit_payment'])->middleware('check_valid_order');
Route::get('summary',[App\Http\Controllers\OrderController::class, 'summary']);

/*AUTH*/
Route::post('loginajax',[App\Http\Controllers\Auth\LoginController::class, 'loginAjax']);// user login via ajax

Auth::routes();

/*USER*/
Route::group(['middleware'=>['auth','web']],function()
{
	Route::get('thankyou',[App\Http\Controllers\OrderController::class,'thankyou']);
	Route::get('home', [App\Http\Controllers\HomeController::class, 'index']);
	Route::get('profile', [App\Http\Controllers\HomeController::class, 'profile']);
	Route::get('order',[App\Http\Controllers\HomeController::class, 'order']);
	Route::get('orders',[App\Http\Controllers\HomeController::class, 'order_list']);
	Route::get('connect_api',[App\Http\Controllers\HomeController::class, 'connect_api']);
	Route::get('transaction',[App\Http\Controllers\HomeController::class, 'history_transaction']);
	Route::post('order-confirm-payment',[App\Http\Controllers\HomeController::class, 'confirm_payment_order']);
	Route::post('/update-profile', [App\Http\Controllers\HomeController::class, 'update_profile'])->middleware('check_profile');

	// SHOP
	Route::get('buy',[App\Http\Controllers\HomeController::class, 'buying_page']);
	Route::get('sell',[App\Http\Controllers\HomeController::class, 'selling_page']);
	Route::get('trade',[App\Http\Controllers\HomeController::class, 'trade']);

	//WITHDRAW COIN TO WALLET
	Route::get('wallet',[App\Http\Controllers\HomeController::class,'wallet']);
	Route::post('wallet-top-up',[App\Http\Controllers\HomeController::class,'get_watcherviews_coin'])->middleware('check_coin');
});

/*ADMIN*/
Route::group(['middleware'=>['auth','web','is_admin']],function()
{
	Route::get('kurs-admin',[App\Http\Controllers\Admin\AdminController::class,'trade']);
	Route::get('order-list',[App\Http\Controllers\Admin\AdminController::class,'index']);
	Route::get('order-load',[App\Http\Controllers\Admin\AdminController::class,'order']);
	Route::get('order-confirm',[App\Http\Controllers\Admin\AdminController::class,'confirm_order']);

	// WA MESSAGE
	Route::get('wa-message',[App\Http\Controllers\Admin\AdminController::class,'set_order_message']);
	Route::post('save-message',[App\Http\Controllers\Admin\AdminController::class,'save_message']);
});