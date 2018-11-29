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
use Illuminate\Http\Request;
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

// JETCO Payment
Route::post('token', PaymentController::class . "@token");

Route::post("paymentCallBack", PaymentController::class . "@paid")
     ->name('paymentCallBack');

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});

Route::post('delegates', PaymentController::class . "@pay");

Route::group(/**
 *
 */
    ['middleware' => 'auth'], function () {

    Route::get('/dashboard', DashboardController::class . "@index")
         ->name('dashboard');

    // Event and detail
    Route::get("events /{
        event}/details", EventsController::class . "@details")
         ->name('events.details');
    Route::resource("events", EventsController::class);

    // Event delegates
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

    // Session and talks
    Route::resource("events . sessions", SessionsController::class);
    Route::resource("events . sessions . talks", TalksController::class);


    // Tickets
    Route::get("events /{
        event}/tickets / import",
        TicketsController::class . "@getImport")->name('events.tickets.import');
    Route::post("events /{
        event}/tickets / import",
        TicketsController::class . "@postImport");
    Route::resource("events . tickets", TicketsController::class);

    // Transaction
    Route::resource("events . transactions", TransactionController::class);

    // Event Notification
    Route::get("events /{
        event}/notifications / import",
        NotificationsController::class . "@getImport")
         ->name('events.notifications.import');
    Route::post("events /{
        event}/notifications / import",
        NotificationsController::class . "@postImport");
    Route::resource("events . notifications", NotificationsController::class);

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
    Route::resource('events.expenses', ExpensesController::class);
    Route::get('expenses/{expense}/files/{fileName}',
        function (Expense $expense, string $fileName) {
            if ($file = $expense->files()->wherePath("files / " . $fileName)
                                ->first()) {
                $path = storage_path('app/' . $file->path);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $path);

                return response()->download($path, $file->paht, [
                    'content-type'   => $mime,
                    'content-length' => filesize($path),
                ]);
            }
        });
    Route::delete("events /{
        event}/expenses /{
        expense}/files /{
        file}",
        function (
            Request $request, Event $event, Expense $expense,
            ExpenseMedium $file
        ) {
            if ($file = $expense->files()->find($file->id)) {
                $file->delete();
            }

            return $request->ajax() ?
                response()->json(['status' => 'completed']) :
                redirect()->back()->withStatus('Attachment deleted.');
        })
         ->name('expenses.media.destroy');

    Route::resource('vendors', VendorsController::class);
    Route::resource('expense_categories', ExpenseCategoriesController::class);

    //Upload file
    Route::post('/files', function (Request $request) {

        $path = $request->file('file')->store('files');

        return response()->json(['path' => $path]);

    });

});
