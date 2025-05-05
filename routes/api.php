<?php

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

Route::post('/register', \App\Http\Controllers\Api\RegisterController::class)->name('register');
Route::post('/register1', \App\Http\Controllers\Api\RegisterController::class)->name('register1');
Route::post('/login', \App\Http\Controllers\Api\LoginController::class)->name('login');
Route::middleware('auth:api')->group(function (){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::group(['prefix' => 'barang'], function () {
        Route::get('/', [\App\Http\Controllers\Api\BarangController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\BarangController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\BarangController::class, 'store']);
        Route::post('/{id}', [\App\Http\Controllers\Api\BarangController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\BarangController::class, 'destroy']);
    });

    Route::group(['prefix' =>'levels'], function ()
    {
        Route::get('/', [\App\Http\Controllers\Api\LevelController::class, 'index']);
        Route::get('/{levelModel}', [\App\Http\Controllers\Api\LevelController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\LevelController::class, 'store']);
        Route::put('/{levelModel}', [\App\Http\Controllers\Api\LevelController::class, 'update']);
        Route::delete('/{levelModel}', [\App\Http\Controllers\Api\LevelController::class, 'destroy']);
        });

    Route::group(['prefix' =>'penjualan'], function ()
    {
        Route::get('/', [\App\Http\Controllers\Api\PenjualanController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\PenjualanController::class, 'show']);
        Route::post('/', [\App\Http\Controllers\Api\PenjualanController::class, 'store']);
        Route::post('/{id}', [\App\Http\Controllers\Api\PenjualanController::class, 'update']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\PenjualanController::class, 'destroy']);
    });
});

Route::post('/logout', \App\Http\Controllers\Api\LogoutController::class)->name('logout');


