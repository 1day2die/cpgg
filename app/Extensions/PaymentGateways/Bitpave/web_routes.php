<?php

use Illuminate\Support\Facades\Route;
use App\Extensions\PaymentGateways\Bitpave\BitpaveExtension;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('payment/Bitpave/{shopProduct}', function () {
        BitpaveExtension::pay(request());
    })->name('payment.BitpavePay');

    Route::get(
        'payment/BitpaveSuccess',
        function () {
            BitpaveExtension::success(request());
        }
    )->name('payment.BitpaveSuccess');


    Route::post('payment/BitpaveCallback', function () {
        BitpaveExtension::callback(request());
    })->name('payment.BitpaveCallback');

});
