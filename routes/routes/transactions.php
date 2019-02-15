<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get("/events/{event}/transactions/search",
TransactionController::class . "@search")->name('events.transactions.search');
Route::resource("events.transactions", TransactionController::class);
