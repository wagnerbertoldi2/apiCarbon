ht<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
//Route::post('/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset')->name('password.update');

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

Route::prefix('emissionFactor')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\EmissionFactorController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\EmissionFactorController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\EmissionFactorController::class, 'update']);
});

Route::prefix('emissionSource')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\EmissionSourceController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\EmissionSourceController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\EmissionSourceController::class, 'update']);
});

Route::prefix('period')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\PeriodController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\PeriodController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\PeriodController::class, 'update']);
});

Route::prefix('emission')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\EmissionController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\EmissionController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\EmissionController::class, 'update']);
});
