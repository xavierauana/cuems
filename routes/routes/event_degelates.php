<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:52 PM
 */

use App\Http\Controllers\DelegatesController;
use Illuminate\Support\Facades\Route;

Route::get("events /{
        event}/delegates / export",
    DelegatesController::class . "@export")
     ->name("events . delegates . export");
Route::post("events /{
        event}/delegates / import",
    DelegatesController::class . "@postImport")
     ->name("events . delegates . import");
Route::post("events /{
        event}/delegates / search",
    DelegatesController::class . "@postSearch")
     ->name("events . delegates . import");
Route::resource("events . delegates", DelegatesController::class);
