<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 1:10 PM
 */

use App\Http\Controllers\CheckinController;

Route::get('events/{event}/checkin/{token}/delegate',
    CheckinController::class . "@getDelegate")
     ->name('events.checkin.getDelegate');

Route::post('events/{event}/checkin/{token}',
    CheckinController::class . "@checkIn")
     ->name('events.checkin.delegate');

Route::get('events/{event}/checkin/search',
    CheckinController::class . "@search")
     ->name('events.checkin.index');

Route::get('events/{event}/checkin', CheckinController::class . "@index")
     ->name('events.checkin.index');

Route::get('events/{event}/checkin_simple',
    CheckinController::class . "@simpleIndex")
     ->name('events.checkin.simple');
