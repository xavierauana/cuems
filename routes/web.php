<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelegateRolesController;
use App\Http\Controllers\DelegatesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\InstitutionsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\TalksController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});

Route::post('delegates', PaymentController::class . "@pay");

Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', DashboardController::class . "@index")
         ->name('dashboard');

    Route::resource("events", EventsController::class);

    Route::post("events/{event}/delegates/import",
        DelegatesController::class . "@postImport")
         ->name("events.delegates.import");
    Route::resource("events.delegates", DelegatesController::class);

    Route::get("events/{event}/tickets/import",
        TicketsController::class . "@getImport")->name('events.tickets.import');
    Route::post("events/{event}/tickets/import",
        TicketsController::class . "@postImport");
    Route::resource("events.tickets", TicketsController::class);
    Route::resource("events.sessions", SessionsController::class);

    Route::resource("events.sessions.talks", TalksController::class);
    Route::get("events/{event}/details", EventsController::class . "@details")
         ->name('events.details');

    Route::resource("events.transactions", TransactionController::class);

    Route::get("events/{event}/notifications/import",
        NotificationsController::class . "@getImport")
         ->name('events.notifications.import');
    Route::post("events/{event}/notifications/import",
        NotificationsController::class . "@postImport");
    Route::resource("events.notifications", NotificationsController::class);

    Route::resource('roles', DelegateRolesController::class);
    Route::get('institutions/search', InstitutionsController::class . "@search")
         ->name('institutions.search');
    Route::resource('institutions', InstitutionsController::class);
    Route::resource('events.templates', TemplatesController::class);

});
