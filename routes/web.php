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

use Adldap\AdldapInterface;
use App\Event;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelegateRolesController;
use App\Http\Controllers\InstitutionsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PositionsController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\SettingsControllers;
use App\Http\Controllers\TalksController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    $id = $request->get('event');
    $event = Event::findOrFail($id);

    return view("welcome")->withEvent($event);
})->name('index');

Route::get('reg', function (Request $request) {
    $id = $request->get('event');
    $event = Event::findOrFail($id);

    return view("welcome", compact('event'));
});

Route::get('ldap', function (Request $request, AdldapInterface $adldap) {

    try {
        $adldap->search()->users()->first();

        return "Okay";

    } catch (Exception $e) {
        return $e->getMessage();
    }
});


Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});


// JETCO Payment
@include('routes/jetco_payment.php');

Route::post('delegates', PaymentController::class . "@pay");

Route::group(['middleware' => 'auth'], function () {

    // Users
    @include('routes/users.php');

    Route::get('/dashboard', DashboardController::class . "@index")
         ->name('dashboard');

    // Event and detail
    @include('routes/event_detail.php');

    // Event delegates
    @include('routes/event_degelates.php');

    // Session and talks
    Route::resource("events.sessions", SessionsController::class);
    Route::resource("events.sessions.talks", TalksController::class);


    // Tickets
    @include('routes/tickets.php');

    // Transaction
    Route::resource("events.transactions", TransactionController::class);

    // Event Notification
    @include('routes/event_notification.php');

    // Delegate Roles
    Route::resource('roles', DelegateRolesController::class);

    // Institution
    Route::get('institutions/search', InstitutionsController::class . "@search")
         ->name('institutions.search');
    Route::resource('institutions', InstitutionsController::class);

    // Position
    Route::get('positions/search', PositionsController::class . "@search")
         ->name('positions.search');
    Route::resource('positions', PositionsController::class);


    // Templates
    Route::resource('events.templates', TemplatesController::class);

    // Event settings
    Route::get('events/{event}/settings/search',
        SettingsControllers::class . "@search")->name('events.settings.search');
    Route::resource('events.settings', SettingsControllers::class);

    // Expenses Related
    @include('routes/expenses.php');

    //Upload file
    Route::post('/files', function (Request $request) {

        $path = $request->file('file')->store('files');

        return response()->json(['path' => $path]);

    });

});
