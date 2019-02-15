<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:52 PM
 */

use App\Http\Controllers\EventsController;
use Illuminate\Support\Facades\Route;

Route::get("events/{event}/details", EventsController::class . "@details")
     ->name('events.details');
Route::resource("events", EventsController::class);
