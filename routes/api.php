<?php

use App\Http\Controllers\AdvertisementsController;
use App\Http\Controllers\Api\DelegatesController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\TalksController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'cors'], function () {
    Route::get("events/{event}/sessions",
        SessionsController::class . "@apiGetSessions");
    Route::get("events/{event}/sessions/search",
        SessionsController::class . "@apiSearchSessions");
    Route::get("events/{event}/talks/search",
        TalksController::class . "@apiSearchTalks");
    Route::get("events/{event}/delegates",
        DelegatesController::class . "@getDelegates")->name('api.delegates');
    Route::get("events/{event}/advertisements",
        AdvertisementsController::class . "@apiAdvertisements")
         ->name('api.advertisements');
});