<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:55 PM
 */

use App\Http\Controllers\SponsorsController;
use Illuminate\Support\Facades\Route;

Route::get("sponsors/csv_template",
    SponsorsController::class . "@template")
     ->name('sponsors.download_template');
Route::get("events/{event}/sponsors/import",
    SponsorsController::class . "@getImport")
     ->name('events.sponsors.import');
Route::post("events/{event}/sponsors/import",
    SponsorsController::class . "@postImport");
Route::get("events/{event}/sponsors/{sponsor}/delegates",
    SponsorsController::class . "@delegates")
     ->name('events.sponsors.delegates');
Route::get("events/{event}/sponsors/export",
    SponsorsController::class . "@allDelegatesExport")
     ->name('events.sponsors.delegates.export.all');
Route::get("events/{event}/sponsors/{sponsor}/export",
    SponsorsController::class . "@delegatesExport")
     ->name('events.sponsors.delegates.export');
Route::resource("events.sponsors", SponsorsController::class);