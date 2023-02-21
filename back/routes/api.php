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
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('files', \App\Http\Controllers\FileController::class);
    Route::apiResource('sales', \App\Http\Controllers\SaleController::class);
    Route::post('getSales', [\App\Http\Controllers\SaleController::class,'getSales']);
    Route::put('userResetPassword/{id}', [\App\Http\Controllers\UserController::class,'userResetPassword']);
    Route::post('logout', [\App\Http\Controllers\UserController::class,'logout']);
    Route::post('me', [\App\Http\Controllers\UserController::class,'me']);
});
