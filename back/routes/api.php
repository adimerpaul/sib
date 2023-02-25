<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::post('login', [\App\Http\Controllers\UserController::class,'login']);
Route::post('upload/{id}/{type}', [\App\Http\Controllers\UploadController::class,'upload']);
Route::apiResource('cogs', \App\Http\Controllers\CogController::class);
Route::get('certificado/{user}', [\App\Http\Controllers\CertificadoController::class,'certificado']);
Route::get('generarPdf/{mes}/{anio}', [\App\Http\Controllers\PayrollController::class,'generarPdf']);
    Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('files', \App\Http\Controllers\FileController::class);
    Route::apiResource('sales', \App\Http\Controllers\SaleController::class);
    Route::apiResource('inventories', \App\Http\Controllers\InventoryController::class);
    Route::apiResource('charges', \App\Http\Controllers\ChargeController::class);
    Route::apiResource('employees', \App\Http\Controllers\EmployeeController::class);
    Route::apiResource('payrolls', \App\Http\Controllers\PayrollController::class);
    Route::apiResource('attendance', \App\Http\Controllers\AttendanceController::class);
    Route::post('getSales', [\App\Http\Controllers\SaleController::class,'getSales']);
    Route::post('asistencia', [\App\Http\Controllers\AttendanceController::class,'asistencia']);
    Route::post('anularSale', [\App\Http\Controllers\SaleController::class,'anularSale']);
    Route::put('userResetPassword/{id}', [\App\Http\Controllers\UserController::class,'userResetPassword']);
    Route::post('logout', [\App\Http\Controllers\UserController::class,'logout']);
    Route::post('me', [\App\Http\Controllers\UserController::class,'me']);
});
