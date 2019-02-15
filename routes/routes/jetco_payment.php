<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:50 PM
 */

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('token', PaymentController::class . "@token");

Route::post("paymentCallBack", PaymentController::class . "@paid")
     ->name('paymentCallBack');