<?php

use App\Http\Controllers\EstimateController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\AddressController;
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

Route::get('/estimate', [EstimateController::class, 'execute']);



Route::post('/uploadCSV', [FileController::class, 'uploadFile']);
Route::delete('/uploadCSV', [FileController::class, 'deleteFile']);

Route::get('/getCoords', [AddressController::class, 'sendAddressRequest']);
