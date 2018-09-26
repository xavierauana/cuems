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


//
//$record = DB::table('activities')
//            ->where('args', '{"campaign_id":"47"}')
//            ->where('email', 'fatkhalid13@gmail.com')
//            ->first();
//
//dd($record);
//
//function total($campaignId): int {
//    return DB::table('email_list_recipients')
//             ->where('email_list_id', $campaignId)
//             ->count();
//}
//
//function sendGridCampaign($campaignId): array {
//    $activities = DB::table('activities')
//                    ->where([
//                        ['event', '=', 'delivered'],
//                        ['args', '=', '{"campaign_id":"' . $campaignId . '"}'],
//                    ])
//                    ->pluck('email');
//
//    return array_count_values($activities->toArray());
//}
//
//function delivered(): array {
//    $activities = DB::table('activities')
//                    ->where([
//                        ['event', '=', 'delivered'],
//                        ['args', '=', '{"campaign_id":"47"}'],
//                    ])
//                    ->pluck('email');
//
//    return array_count_values($activities->toArray());
//}
//
//function unsent($campaignId): array {
//
//    $activities = DB::table('activities')
//                    ->select('email')
//                    ->distinct()
//                    ->pluck('email');
//
//    $emails = DB::table('email_list_recipients')
//                ->where('email_list_id', $campaignId)
//                ->pluck('email');
//
//    return array_diff(array_map('strtolower',$emails->toArray()), array_map('strtolower',$activities->toArray()));
//}
//
//function duplicate(): array {
//
//    $duplicates = [];
//
//    $activities = DB::table('activities')
//                    ->where([
//                        ['event', '=', 'delivered'],
//                        ['args', '=', '{"campaign_id":"47"}'],
//                    ])
//                    ->pluck('email');
//
//    $array = array_count_values($activities->toArray());
//
//    foreach ($array as $key => $value) {
//        if ($value > 1) {
//            $duplicates[$key] = $value;
//        }
//    }
//
//    return $duplicates;
//
//}
//
//function bounce(): array {
//
//    $bounce = DB::table('activities')
//                ->where([
//                    ['event', '=', 'bounce'],
//                    ['args', '=', '{"campaign_id":"47"}'],
//                ])
//                ->pluck('email');
//
//    return array_count_values($bounce->toArray());
//}
//
//function unique_open(): array {
//
//    $unique_open = DB::table('activities')
//                     ->where([
//                         ['args', '=', '{"campaign_id":"47"}'],
//                         ['event', '=', 'open'],
//                         ['is_unique', '=', 'TRUE'],
//                     ])
//                     ->pluck('email');
//
//    return $unique_open;
//}
//
//var_dump(total(19));
//
//$unsentEmails = unsent(19);
//
//dd($unsentEmails);
//$deliveredEmails = delivered();
//
////var_dump(count($deliveredEmails));
////var_dump(count($unsentEmails));
////var_dump(count(duplicate()));
////var_dump(count(bounce()));
////var_dump(count(sendGridCampaign(47)));
//
////$sum = total(19) - count(delivered()) - count(duplicate()) - count(bounce());
//
//dd(array_values($unsentEmails));
////dd($sum, count(unsent(19)));
//$haystack = array_keys($deliveredEmails);
//foreach (array_values($unsentEmails) as $check) {
//    if (in_array($check, $haystack)) {
//        throw new Exception("incorrect");
//    }
//}
//dd("done");

use App\Contracts\PaymentServiceInterface;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DelegateRolesController;
use App\Http\Controllers\DelegatesController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\TalksController;
use App\Http\Controllers\TicketsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Auth::routes(['verify' => true]);
});

Route::post('delegates',
    function (Request $request, PaymentServiceInterface $service) {
        $service->charge($request->get('token'), 10000);
    });

Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', DashboardController::class . "@index")
         ->name('dashboard');

    Route::resource("events", EventsController::class);
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

    Route::resource('roles', DelegateRolesController::class);

});
