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
use App\Services\CreateTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

$test = function () {
    /** @var CreateTicketService $service */
    $service = app(CreateTicketService::class);

    $transaction = \App\Delegate::whereRegistrationId(8)->first()
                                ->transactions()->first();

    return $service->setPageSize('a4')
                   ->setOrientation('portrait')
                   ->createPDF($transaction);

    dd($result);
};

$sendNotification = function () {
    /** @var \App\Notification $notification */
    $notification = \App\Notification::find(1);

    $delegate = \App\Delegate::whereEventId(1)
                             ->whereEmail('xavier.au@anacreation.com')->first();

    $event = Event::find($notification->event_id);

    for ($i = 0; $i < 10; $i++) {
        $email = "test_user_{$i}@gmail.com";
        $data = [
            'prefix'          => 'Mr.',
            'first_name'      => 'Xavier',
            'last_name'       => 'Au',
            'is_male'         => true,
            'email'           => $email,
            'mobile'          => 66281556,
            'position'        => "Developer",
            'department'      => "IT",
            'institution'     => "A & A Creation Co.",
            'address_1'       => "Address 1",
            'address_2'       => "Address 2",
            'address_3'       => "Address 3",
            'country'         => "Hong Kong",
            'registration_id' => $i,
            'event_id'        => $notification->event_id,
        ];

        $delegate = $event->delegates()->create($data);

        dispatch(new \App\Jobs\SendNotification($notification, $delegate));
    }
};

Route::get('test_event', function () use ($sendNotification) {
    $sendNotification();

    return "Done";
});


Route::get('testpdf', function () use ($test) {

    $headers = [
        "Content-Type"        => [
            "application/pdf"
        ],
        "Content-Disposition" => [
            "attachment; filename = \"test.pdf\""
        ],
    ];

    $path = public_path('test.pdf');

    file_put_contents($path, $test());

    return response()->file($path, $headers);

    return response()->streamDownload(function () use ($test) {
        return $test();
    }, 'test.pdf');
});


Route::get('/test_record', function (Request $request) {
    if ($request->get('from') === "xavier") {
        if ($invoiceId = $request->get('invoice_id')) {
            $records = DB::table('payment_records')
                         ->where('invoice_id', $invoiceId)->get();
        } else {
            $records = DB::table('payment_records')
                         ->get();
        }

        return view("record", compact('records'));
    }

    return response('notmessage');
});

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

Route::group(['middleware' => 'auth'], function () {

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
    Route::get('/events/{event}/talks', TalksController::class . "@all")
         ->name('events.talks.all');
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

    // Check in
    @include('routes/checkin.php');
});
