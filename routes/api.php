<?php

use App\Http\Controllers\IsolationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['log.route:deep']], function () {
    Route::get('/healthz', [IsolationController::class, 'healthz']);
    Route::post('/init', [IsolationController::class, 'initialization']);
    Route::post('/call', [IsolationController::class, 'callProvider']);
    Route::get('/init', [IsolationController::class, 'initialization']);
    Route::get('/call', [IsolationController::class, 'callProvider']);
});

