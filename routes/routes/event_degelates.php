<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:52 PM
 */

use App\Enums\DelegateDuplicationStatus;
use App\Http\Controllers\DelegatesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get("delegates/import_template",
    DelegatesController::class . "@template")
     ->name("delegates.import_template");

Route::get("events/{event}/delegates/import",
    DelegatesController::class . "@getImport")
     ->name("events.delegates.import");
Route::post("events/{event}/delegates/import",
    DelegatesController::class . "@postImport");


Route::post('events/{event}/delegates/{delegate}/duplicated',
    function (Request $request, \App\Event $event, \App\Delegate $delegate) {
        $delegate = $event->delegates()->find($delegate->id);
        if ($request->ajax() and $delegate) {
            $delegate = $event->delegates()->find($delegate->id);
            $delegate->is_duplicated = ($delegate->is_duplicated === DelegateDuplicationStatus::DUPLICATED) ?
                DelegateDuplicationStatus::NO :
                DelegateDuplicationStatus::DUPLICATED;
            $delegate->save();

            $delegate->fresh();

            return response()->json([
                'status'     => 'completed',
                'duplicated' => ($delegate->is_duplicated === DelegateDuplicationStatus::DUPLICATED)
            ]);
        }

        return response();

    });

Route::get("events/{event}/delegates/new",
    DelegatesController::class . "@new")
     ->name("events.delegates.new");
Route::post("events/{event}/delegates/new/import",
    DelegatesController::class . "@importNew")
     ->name("events.delegates.new.import");
Route::get("events/{event}/delegates/new/import",
    DelegatesController::class . "@getImportNew")
     ->name("events.delegates.new.import");
Route::post("events/{event}/delegates/new/import",
    DelegatesController::class . "@importNew");
Route::get("events/{event}/delegates/new/export",
    DelegatesController::class . "@exportNew")
     ->name("events.delegates.new.export");
Route::get("events/{event}/delegates/duplicates",
    DelegatesController::class . "@duplicates")
     ->name("events.delegates.duplicates");

Route::get("events/{event}/delegates/export",
    DelegatesController::class . "@export")
     ->name("events.delegates.export");

Route::get("events/{event}/delegates/search",
    DelegatesController::class . "@search")
     ->name("events.delegates.search");
Route::post("events/{event}/delegates/search",
    DelegatesController::class . "@searchDuplicate");
Route::resource("events.delegates", DelegatesController::class);
