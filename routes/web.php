<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ADMIN SIDE

// USERS

Route::get('/admin/users', [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('admin.users');

Route::get('/admin/APIusers', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'getUsers'])->name('api.users');
Route::post('/admin/APIusers/store', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'storeUser'])->name('admin.users.store');
Route::post('/admin/APIusers/delete', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'deleteUser'])->name('admin.users.delete');
Route::post('/admin/APIusers/edit', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'editUser'])->name('admin.users.edit');

Route::post('/admin/users/importExcel', [\App\Http\Controllers\Admin\UsersController::class, 'importExcel'])->name('admin.users.importExcel');
