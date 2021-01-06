<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController as Home;
use App\Http\Controllers\OrderController as Pricing;

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
Route::get('pricing',[Pricing::class, 'index']);
Route::post('payment',[Pricing::class, 'payment']);
Route::get('summary',[Pricing::class, 'summary']);


/*Route::group(['middleware' => ['web','auth']], function () {

});
*/