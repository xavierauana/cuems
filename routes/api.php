<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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


Route::get('data', function (Request $request) {

    Log::info($request->query());

    return response()->json(["message" => "good job! with query {$request->query('first')}"]);

});

Route::post('new', function (Request $request) {

    Log::info("data is, ", $request->all());
    Log::info("query is, ", $request->query());

    return response()->json(["message" => "new good job!"]);

});