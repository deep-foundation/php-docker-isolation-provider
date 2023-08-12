<?php

use App\Http\Controllers\IsolationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['log.route:deep']], function () {
    Route::get('/healthz', [IsolationController::class, 'healthz']);
    Route::post('/init', [IsolationController::class, 'initialization']);
    Route::post('/call', [IsolationController::class, 'callProvider']);
    Route::get('/init', [IsolationController::class, 'initialization']);
    Route::get('/call', [IsolationController::class, 'callProvider']);
});
