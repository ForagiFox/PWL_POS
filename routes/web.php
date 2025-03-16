<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
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

Route::get('/', [WelcomeController::class,'index']);

Route::group(['prefix' => 'user'], function (){
    Route::get('/',[UserController::class, 'index']);
    Route::post('/list',[UserController::class, 'list']);
    Route::get('/create',[UserController::class, 'create']);
    Route::post('/',[UserController::class, 'store']);
    Route::get('/create_ajax',[UserController::class, 'create_ajax']);
    Route::post('/ajax',[UserController::class, 'store_ajax']);
    Route::get('/{id}',[UserController::class, 'show']);
    Route::get('/{id}/edit',[UserController::class, 'edit']);
    Route::put('/{id}',[UserController::class, 'update']);
    Route::get('/{id}/edit_ajax',[UserController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax',[UserController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax',[UserController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[UserController::class, 'delete_ajax']);
    Route::delete('/{id}',[UserController::class, 'destroy']);
});

Route::group(['prefix' => 'level'], function (){
    Route::get('/',[LevelController::class, 'index']);
    Route::post('/list',[LevelController::class, 'list']);
    Route::get('/create',[LevelController::class, 'create']);
    Route::post('/',[LevelController::class, 'store']);
    Route::get('/create_ajax',[LevelController::class, 'create_ajax']);
    Route::get('/{id}',[LevelController::class, 'show']);
    Route::get('/{id}/edit',[LevelController::class, 'edit']);
    Route::put('/{id}',[LevelController::class, 'update']);
    Route::post('/ajax',[LevelController::class, 'store_ajax']);
    Route::get('/{id}/edit_ajax',[LevelController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax',[LevelController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax',[LevelController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[LevelController::class, 'delete_ajax']);
    Route::delete('/{id}',[LevelController::class, 'destroy']);
});

Route::group(['prefix' => 'kategori'], function (){
    Route::get('/',[CategoryController::class, 'index']);
    Route::post('/list',[CategoryController::class, 'list']);
    Route::get('/create',[CategoryController::class, 'create']);
    Route::post('/',[CategoryController::class, 'store']);
    Route::get('/create_ajax',[CategoryController::class, 'create_ajax']);
    Route::get('/{id}',[CategoryController::class, 'show']);
    Route::get('/{id}/edit',[CategoryController::class, 'edit']);
    Route::put('/{id}',[CategoryController::class, 'update']);
    Route::post('/ajax',[CategoryController::class, 'store_ajax']);
    Route::get('/{id}/edit_ajax',[CategoryController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax',[CategoryController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax',[CategoryController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[CategoryController::class, 'delete_ajax']);
    Route::delete('/{id}',[CategoryController::class, 'destroy']);
});

Route::group(['prefix' => 'supplier'], function (){
    Route::get('/',[SupplierController::class, 'index']);
    Route::post('/list',[SupplierController::class, 'list']);
    Route::get('/create',[SupplierController::class, 'create']);
    Route::post('/',[SupplierController::class, 'store']);
    Route::get('/{id}',[SupplierController::class, 'show']);
    Route::get('/{id}/edit',[SupplierController::class, 'edit']);
    Route::put('/{id}',[SupplierController::class, 'update']);
    Route::post('/ajax',[UserController::class, 'store_ajax']);
    Route::get('/create_ajax',[UserController::class, 'create_ajax']);
    Route::get('/{id}/edit_ajax',[UserController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax',[UserController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax',[UserController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[UserController::class, 'delete_ajax']);
    Route::delete('/{id}',[SupplierController::class, 'destroy']);
});

Route::group(['prefix' => 'barang'], function (){
    Route::get('/',[BarangController::class, 'index']);
    Route::post('/list',[BarangController::class, 'list']);
    Route::get('/create',[BarangController::class, 'create']);
    Route::post('/',[BarangController::class, 'store']);
    Route::get('/{id}',[BarangController::class, 'show']);
    Route::get('/{id}/edit',[BarangController::class, 'edit']);
    Route::put('/{id}',[BarangController::class, 'update']);
    Route::post('/ajax',[UserController::class, 'store_ajax']);
    Route::get('/create_ajax',[UserController::class, 'create_ajax']);
    Route::get('/{id}/edit_ajax',[UserController::class, 'edit_ajax']);
    Route::put('/{id}/update_ajax',[UserController::class, 'update_ajax']);
    Route::get('/{id}/delete_ajax',[UserController::class, 'confirm_ajax']);
    Route::delete('/{id}/delete_ajax',[UserController::class, 'delete_ajax']);
    Route::delete('/{id}',[BarangController::class, 'destroy']);
});
