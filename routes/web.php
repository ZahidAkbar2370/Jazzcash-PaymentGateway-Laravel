<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/checkout', 'App\Http\Controllers\CheckoutController@index');
Route::post('/checkout','App\Http\Controllers\CheckoutController@DoCheckout');

Route::post('/paymentstatus','App\Http\Controllers\CheckoutController@paymentStatus');