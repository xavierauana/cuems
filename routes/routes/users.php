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
Route::post('users/ldap', UsersController::class . "@addLdapUser");
Route::get('users/search', UsersController::class . "@search")
     ->name('users.search');
Route::put('/users/restore/{user}', UsersController::class . "@restore")
     ->name('users.restore');
Route::resource('users', UsersController::class);