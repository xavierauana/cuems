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

use App\Event;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelegateRolesController;
use App\Http\Controllers\InstitutionsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentRecordsController;
use App\Http\Controllers\PositionsController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\SettingsControllers;
use App\Http\Controllers\TalksController;
use App\Http\Controllers\TemplatesController;
use App\Http\Controllers\UploadFilesController;
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

Route::view('admin/login', 'admin_login');
Route::post('admin/login', LoginController::class . "@adminLogin");

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});


// JETCO Payment
@include('routes/jetco_payment.php');

Route::post('delegates', PaymentController::class . "@pay");

Route::group(
    ['middleware' => 'auth'], function () {

    Route::redirect('/home', '/dashboard');

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

    // Sponsors
    @include('routes/sponsors.php');

    // Transaction
    @include('routes/transactions.php');

    // Event Notification
    @include('routes/event_notification.php');

    // Delegate Roles
    Route::resource('roles', DelegateRolesController::class);

    // Institution
    Route::get('institutions/import', InstitutionsController::class . '@import')
         ->name('institutions.import');
    Route::post('institutions/import',
        InstitutionsController::class . '@postImport');
    Route::get('institutions/export', InstitutionsController::class . '@export')
         ->name('institutions.export');
    Route::get('institutions/search', InstitutionsController::class . "@search")
         ->name('institutions.search');
    Route::resource('institutions', InstitutionsController::class);

    // Position
    Route::get('positions/import', PositionsController::class . '@import')
         ->name('positions.import');
    Route::post('positions/import', PositionsController::class . '@postImport');
    Route::get('positions/export', PositionsController::class . '@export')
         ->name('positions.export');
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
    Route::resource('events.uploadFiles',
        UploadFilesController::class);

    //Payment Records
    Route::get('events/{event}/paymentRecords',
        PaymentRecordsController::class . "@index")
         ->name('events.payment_records.index');
    Route::get('events/{event}/paymentRecords/{record}',
        PaymentRecordsController::class . "@show")
         ->name('events.payment_records.show');
    Route::get('events/{event}/paymentRecords/{record}/convert',
        PaymentRecordsController::class . "@convert")
         ->name('events.payment_records.convert');

});
