<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PanicAPIController;
use App\Http\Controllers\API\AuthApiController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['middleware' => ['cors', 'json.response']], function () {

    Route::post('login', [AuthApiController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('panic/create', [PanicAPIController::class, 'store'])->name('panic.create');

        Route::post('panic/cancel', [PanicAPIController::class, 'cancel'])->name('panic.cancel');


        Route::get('panic/get', [PanicAPIController::class, 'getHistory'])->name('panic.get');
    });
});