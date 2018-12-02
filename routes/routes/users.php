<?php
/**
 * Author: Xavier Au
 * Date: 2/12/2018
 * Time: 1:10 PM
 */

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('users/ldap', UsersController::class . "@ldap")
     ->name('users.ldap');
Route::get('users/search', UsersController::class . "@search")
     ->name('users.search');
Route::resource('users', UsersController::class);