<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 1:10 PM
 */

use App\Http\Controllers\CheckInRecordController;

Route::get('events/{event}/checkinRecords',
    CheckInRecordController::class . "@index")
     ->name('events.checkinRecords');

Route::get('events/{event}/checkinRecords/export',
    CheckInRecordController::class . "@export")
     ->name('events.checkinRecords.export');

Route::post('events/{event}/checkinRecords/notification',
    CheckInRecordController::class . "@notification")
     ->name('events.checkinRecords.notification');


