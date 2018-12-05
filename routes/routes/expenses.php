<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 12:53 PM
 */

use App\Event;
use App\Expense;
use App\ExpenseMedium;
use App\Http\Controllers\ExpenseCategoriesController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\VendorsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
Route::delete("events/{event}/expenses/{expense}/files/{file}",
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