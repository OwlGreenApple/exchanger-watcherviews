<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController as Register;
use App\Http\Controllers\Auth\LoginController as Login;
use App\Http\Controllers\HomeController as Home;
use App\Http\Controllers\OrderController as Orders;
use App\Http\Controllers\CoinsController as Coins;
use App\Http\Controllers\Admin\AdminController as Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middlelware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//ORDER
Route::get('pricing',[Orders::class, 'index']);
Route::get('thankyou',[Orders::class, 'thankyou']); 
Route::post('payment',[Orders::class, 'payment']);
Route::get('summary',[Orders::class, 'summary']);
Route::post('submit-summary',[Orders::class, 'submit_summary']);

//AUTH AJAX
Route::post('register',[Register::class, 'register']);
Route::post('loginajax',[Login::class, 'loginAjax']);// user login via ajax

//REFERRAL FORM
Route::get('referral-reg/{link}',[Home::class, 'referral_register']);

/*LOGIN USER*/
Route::group(['middleware' => ['web','auth']], function () {
  //HOME OR DAHSBOARD
  Route::get('home', [Home::class, 'index'])->name('home');
  Route::get('profile',[Home::class , 'profile']);
  Route::post('update-profile',[Home::class , 'update_profile'])->middleware('profile');
  Route::get('history-order',[Home::class, 'order_history']);
  Route::get('thank-confirm',[Home::class, 'confirm_thank_you']);

  //CONTACT
  Route::get('contact',[Home::class, 'contact']);
  Route::post('send-contact',[Home::class, 'save_contact']);
  Route::get('contact-table',[Home::class, 'contact_table']);

  //REFERRAL
  Route::get('referral',[Home::class, 'referral']);
  Route::get('referral-link',[Home::class, 'generate_referral_link']);

  //TRANSACTION
  Route::get('transaction',[Home::class, 'transaction']);

  //ORDER
  Route::post('confirm-payment',[Orders::class, 'confirm_payment_order']); 

  //BUY COINS
  Route::get('buy-coins',[Coins::class, 'index']);
  Route::post('purchase-coins',[Coins::class, 'purchase_coins']);

  //EXCHANGE COINS
  Route::get('exchange-coins',[Coins::class, 'exchange']);
  Route::post('exchange-submit-coins',[Coins::class, 'submit_exchange'])->middleware('exchange');
  Route::get('exchange-table',[Coins::class, 'exchange_table']);
});

/*ADMIN USER*/
Route::group(['middleware' => ['web','auth','is_admin']], function () 
{
  Route::get('list-order', [Admin::class, 'index']);
  Route::post('confirm-order',[Admin::class, 'confirm_order']);

  //CONTACTS
  Route::get('user-contacts',[Admin::class, 'user_contacts']);
  Route::get('user-contacts-table',[Admin::class, 'user_contacts_table']);
});


