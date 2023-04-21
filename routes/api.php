ht<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);

Route::prefix('user')->group(function (){
    Route::post('set', [\App\Http\Controllers\UserController::class, 'set']);
    Route::middleware('jwt.auth')->get('get', [\App\Http\Controllers\UserController::class, 'me']);
});

Route::prefix('category')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\CategoryController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\CategoryController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\CategoryController::class, 'update']);
});

Route::prefix('property')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\PropertyController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\PropertyController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\PropertyController::class, 'update']);
});

Route::prefix('unit')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\UnitController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\UnitController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\UnitController::class, 'update']);
});
