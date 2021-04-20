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

// USER SIDE

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

// HOME (LOAD TRIP)

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/home/autocomplete', [App\Http\Controllers\HomeController::class, 'autocomplete'])->name('autocomplete');
Route::post('/home/tripmerci', [App\Http\Controllers\HomeController::class, 'tripMerci'])->name('tripmerci');
Route::post('/home/tripofficina', [App\Http\Controllers\HomeController::class, 'tripOfficina'])->name('tripofficina');
Route::post('/home/tripvuoto', [App\Http\Controllers\HomeController::class, 'tripVuoto'])->name('tripvuoto');

// DOCUMENT

Route::get('/documents', [App\Http\Controllers\DocumentsController::class, 'index'])->name('documents');
Route::get('/documents/getDocuments', [App\Http\Controllers\DocumentsController::class, 'getDocuments'])->name('getDocuments');
Route::post('/documents/store', [App\Http\Controllers\DocumentsController::class, 'store'])->name('document.store');
Route::post('/documents/upload', [App\Http\Controllers\DocumentsController::class, 'upload'])->name('document.upload');
Route::get('/documents/download/{id?}', [App\Http\Controllers\DocumentsController::class, 'download'])->name('document.download');

// REPORT CRASH

Route::get('/report-crash', [App\Http\Controllers\CrashController::class, 'index'])->name('crash');
Route::post('/report-crash/upload', [App\Http\Controllers\CrashController::class, 'upload'])->name('crash.upload');
Route::post('/report-crash/store', [App\Http\Controllers\CrashController::class, 'store'])->name('crash.store');

// ADMIN SIDE

// SETTINGS

Route::get('/admin/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('admin.settings');
Route::post('/admin/settings/setLang', [\App\Http\Controllers\Admin\SettingsController::class, 'setLang'])->name('admin.lang');
Route::post('/admin/settings/psw', [\App\Http\Controllers\Admin\SettingsController::class, 'pswChange'])->name('admin.psw.change');
Route::post('/admin/settings/company', [\App\Http\Controllers\Admin\SettingsController::class, 'companyChange'])->name('admin.company.change');

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


// MAINTENANCES

Route::get('/admin/maintenance', [\App\Http\Controllers\Admin\MaintenancesController::class, 'index'])->name('admin.maint');

Route::get('/admin/APIMaintAlreadyDone/maint/{dateTo?}/{dateFrom?}', [\App\Http\Controllers\Admin\API\APIMaintAlreadyDoneController::class, 'getMaint'])->name('api.maint');
Route::post('/admin/APIMaintAlreadyDone/store', [\App\Http\Controllers\Admin\API\APIMaintAlreadyDoneController::class, 'store'])->name('admin.maint.store');
Route::post('/admin/APIMaintAlreadyDone/edit', [\App\Http\Controllers\Admin\API\APIMaintAlreadyDoneController::class, 'edit'])->name('admin.maint.edit');
Route::post('/admin/APIMaintAlreadyDone/delete', [\App\Http\Controllers\Admin\API\APIMaintAlreadyDoneController::class, 'destroy'])->name('admin.maint.delete');
Route::get('/admin/APIMaintStillToDo/maint', [\App\Http\Controllers\Admin\API\APIMaintStillToDoController::class, 'getMaint'])->name('api.maintStill');
Route::post('/admin/APIMaintStillToDo/store', [\App\Http\Controllers\Admin\API\APIMaintStillToDoController::class, 'store'])->name('admin.maintStill.store');
Route::post('/admin/APIMaintStillToDo/edit', [\App\Http\Controllers\Admin\API\APIMaintStillToDoController::class, 'edit'])->name('admin.maintStill.edit');
Route::post('/admin/APIMaintStillToDo/delete', [\App\Http\Controllers\Admin\API\APIMaintStillToDoController::class, 'destroy'])->name('admin.maintStill.delete');
Route::post('/admin/APIMaintStillToDo/confirm', [\App\Http\Controllers\Admin\API\APIMaintStillToDoController::class, 'confirm'])->name('admin.maintStill.confirm');

// TRIPS

Route::get('/admin/trips', [\App\Http\Controllers\Admin\TripsController::class, 'index'])->name('admin.trips');

Route::get('/admin/APITrips/trips/{dateTo?}/{dateFrom?}', [\App\Http\Controllers\Admin\API\APITripsController::class, 'getTrips'])->name('api.trips');
Route::post('/admin/APITrips/trips/autocompletecity', [\App\Http\Controllers\Admin\API\APITripsController::class, 'autocompleteCity'])->name('autocomplete.city');
Route::post('/admin/APITrips/trips/store', [\App\Http\Controllers\Admin\API\APITripsController::class, 'store'])->name('api.trips.store');
Route::post('/admin/APITrips/trips/delete', [\App\Http\Controllers\Admin\API\APITripsController::class, 'delete'])->name('api.trips.delete');
Route::post('/admin/APITrips/trips/edit', [\App\Http\Controllers\Admin\API\APITripsController::class, 'edit'])->name('api.trips.edit');

// DOCUMENTS

Route::get('/admin/documents', [\App\Http\Controllers\Admin\DocumentsController::class, 'index'])->name('admin.documents');

Route::get('/admin/APIDocuments/documents/get', [\App\Http\Controllers\Admin\API\APIDocumentsController::class, 'getDocuments'])->name('api.documents');
Route::post('/admin/APIDocuments/documents/store', [App\Http\Controllers\Admin\API\APIDocumentsController::class, 'store'])->name('api.document.store');
Route::post('/admin/APIDocuments/documents/upload', [App\Http\Controllers\Admin\API\APIDocumentsController::class, 'upload'])->name('api.document.upload');
Route::get('/admin/APIDocuments/documents/download/{id?}', [App\Http\Controllers\Admin\API\APIDocumentsController::class, 'download'])->name('api.document.download');
Route::post('/admin/APIDocuments/documents/delete', [\App\Http\Controllers\Admin\API\APIDocumentsController::class, 'delete'])->name('api.documents.delete');
Route::post('/admin/APIDocuments/documents/edit', [\App\Http\Controllers\Admin\API\APIDocumentsController::class, 'edit'])->name('api.documents.edit');

// REPORT CRASH

Route::get('/admin/crashes', [App\Http\Controllers\Admin\CrashController::class, 'index'])->name('admin.crash');

Route::get('/admin/APICrash/crashes/get', [\App\Http\Controllers\Admin\API\APICrashController::class, 'getCrashes'])->name('api.crash');
Route::get('/admin/APICrash/crashes/download/{id?}', [\App\Http\Controllers\Admin\API\APICrashController::class, 'download'])->name('api.crash.download');
Route::post('/admin/APICrash/crashes/upload', [\App\Http\Controllers\Admin\API\APICrashController::class, 'upload'])->name('api.crash.upload');
Route::post('/admin/APICrash/crashes/store', [\App\Http\Controllers\Admin\API\APICrashController::class, 'store'])->name('api.crash.store');
Route::post('/admin/APICrash/crashes/delete', [\App\Http\Controllers\Admin\API\APICrashController::class, 'delete'])->name('api.crash.delete');
