<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\RandomNumberGenerator;

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post("/sendCodeReset", [\App\Http\Controllers\UserController::class, "SendEmailResetPassword"]);
Route::post('/passreset', [\App\Http\Controllers\UserController::class, 'passReset'])->name('reset.password');

Route::prefix('user')->group(function (){
    Route::post('set', [\App\Http\Controllers\UserController::class, 'set']);
    Route::middleware('jwt.auth')->get('get', [\App\Http\Controllers\UserController::class, 'me']);
    Route::post('validate-password', [\App\Http\Controllers\UserController::class, 'verificarSenha'])->middleware('jwt.auth');
    Route::post('update', [\App\Http\Controllers\UserController::class, 'update'])->middleware('jwt.auth');
    Route::post('get-profile', [\App\Http\Controllers\UserController::class, 'getProfile'])->middleware('jwt.auth');
});

Route::prefix('category')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\CategoryController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\CategoryController::class, 'get']);
    Route::post('update', [\App\Http\Controllers\CategoryController::class, 'update']);
});

Route::prefix('property')->middleware('jwt.auth')->group(function (){
    Route::post('set', [\App\Http\Controllers\PropertyController::class, 'set']);
    Route::get('get', [\App\Http\Controllers\PropertyController::class, 'get']);
    Route::get('getRegion', [\App\Http\Controllers\PropertyController::class, 'getRegion']);
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
    Route::post('set', [\App\Http\Controllers\EmissionController::class, 'sephpt']);
    Route::get('get', [\App\Http\Controllers\EmissionController::class, 'get']);
    Route::get('getList', [\App\Http\Controllers\EmissionController::class, 'getList']);
    Route::post('update', [\App\Http\Controllers\EmissionController::class, 'update']);
    Route::get('delete', [\App\Http\Controllers\EmissionController::class, 'deleteFonteEmissao']);
});

Route::prefix('import')->middleware('jwt.auth')->group(function () {
    Route::post('set', [\App\Http\Controllers\ImportController::class, 'importXlsx']);
});

Route::post('vincula', [\App\Http\Controllers\ImportController::class, 'openLink']);
Route::post('vincula-dados', [\App\Http\Controllers\ImportController::class, 'dadosUnidade']);
Route::get('get-dados', [\App\Http\Controllers\ImportController::class, 'listDadosImportados'])->middleware('auth.admin');
Route::get('get-dados-importes', [\App\Http\Controllers\ImportController::class, 'listaImportes'])->middleware('auth.admin');
Route::get('get-dados-linha', [\App\Http\Controllers\ImportController::class, 'getDadosLinhaImporte'])->middleware('auth.admin');
