<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:55 PM
 */

use App\Http\Controllers\TicketsController;
use Illuminate\Support\Facades\Route;

Route::get("events/{event}/tickets/import",
    TicketsController::class . "@getImport")->name('events.tickets.import');
Route::post("events/{event}/tickets/import",
    TicketsController::class . "@postImport");
Route::resource("events.tickets", TicketsController::class);