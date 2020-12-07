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

// SETTINGS

Route::get('/admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');

// GROUPS

Route::get('/admin/groups', [\App\Http\Controllers\Admin\GroupsController::class, 'index'])->name('admin.groups');

Route::get('/admin/APIgroups', [\App\Http\Controllers\Admin\API\APIGroupsController::class, 'getGroups'])->name('api.groups');
Route::post('/admin/APIgroups/store', [\App\Http\Controllers\Admin\API\APIGroupsController::class, 'store'])->name('admin.groups.store');
Route::post('/admin/APIgroups/edit', [\App\Http\Controllers\Admin\API\APIGroupsController::class, 'edit'])->name('admin.groups.edit');
Route::post('/admin/APIgroups/delete', [\App\Http\Controllers\Admin\API\APIGroupsController::class, 'destroy'])->name('admin.groups.delete');

// USERS

Route::get('/admin/users', [\App\Http\Controllers\Admin\UsersController::class, 'index'])->name('admin.users');

Route::get('/admin/APIusers/users/{group?}', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'getUsers'])->name('api.users');
Route::get('/admin/APIusers/licenses/{group?}', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'getLicenses'])->name('api.users.licenses');
Route::post('/admin/APIusers/store', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'storeUser'])->name('admin.users.store');
Route::post('/admin/APIusers/delete', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'deleteUser'])->name('admin.users.delete');
Route::post('/admin/APIusers/edit', [\App\Http\Controllers\Admin\API\APIUsersController::class, 'editUser'])->name('admin.users.edit');

Route::post('/admin/users/importExcel', [\App\Http\Controllers\Admin\UsersController::class, 'importExcel'])->name('admin.users.importExcel');

// TRUCKS

Route::get('/admin/trucks', [\App\Http\Controllers\Admin\TrucksController::class, 'index'])->name('admin.trucks');

Route::get('/admin/APItrucks/trucks/{group?}/{type?}', [\App\Http\Controllers\Admin\API\APITrucksController::class, 'getTrucks'])->name('api.trucks');
Route::get('/admin/APItrucks/expirations/{group?}/{type?}', [\App\Http\Controllers\Admin\API\APITrucksController::class, 'getExpirations'])->name('api.trucks.expirations');
Route::post('/admin/APItrucks/store', [\App\Http\Controllers\Admin\API\APITrucksController::class, 'store'])->name('admin.trucks.store');
Route::post('/admin/APItrucks/edit', [\App\Http\Controllers\Admin\API\APITrucksController::class, 'edit'])->name('admin.trucks.edit');
Route::post('/admin/APItrucks/delete', [\App\Http\Controllers\Admin\API\APITrucksController::class, 'destroy'])->name('admin.trucks.delete');
