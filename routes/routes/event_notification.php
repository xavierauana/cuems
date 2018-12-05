<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:54 PM
 */

use App\Http\Controllers\NotificationsController;
use Illuminate\Support\Facades\Route;

Route::get("events/{event}/notifications/import",
    NotificationsController::class."@getImport")
     ->name('events.notifications.import');
Route::post("events/{event}/notifications/import",
    NotificationsController::class."@postImport");
Route::resource("events.notifications", NotificationsController::class);