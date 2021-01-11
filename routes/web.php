<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController as Register;
use App\Http\Controllers\Auth\LoginController as Login;
use App\Http\Controllers\HomeController as Home;
use App\Http\Controllers\OrderController as Orders;

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

//HOME OR DAHSBOARD
Route::get('home', [Home::class, 'index'])->name('home');

//ORDER
Route::get('pricing',[Orders::class, 'index']);
Route::get('thankyou',[Orders::class, 'thankyou']);
Route::post('payment',[Orders::class, 'payment']);
Route::get('summary',[Orders::class, 'summary']);
Route::post('submit-summary',[Orders::class, 'submit_summary']);


//AUTH AJAX
Route::post('register',[Register::class, 'register']);
Route::post('loginajax',[Login::class, 'loginAjax']);// user login via ajax

/*Route::group(['middleware' => ['web','auth']], function () {

});
*/