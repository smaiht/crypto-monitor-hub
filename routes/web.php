<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ExchangeController;


Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin');
});

route::get('/get-top-100/{exchangeName}/{quote}', [ExchangeController::class, 'getTop100Coins']);


Route::get('/price', [ExchangeController::class, 'getPrice']);
