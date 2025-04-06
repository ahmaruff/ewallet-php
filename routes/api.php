<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return response()->json(
        ['user' => $request->user()]
    );
})->middleware('auth:sanctum');

Route::group([
    'prefix' => 'transactions',
    'as' => 'transactions.',
    'middleware' => ['auth:sanctum'],
], function() {
    Route::get('/get-chart',[\App\Http\Controllers\Api\Transaction\TransactionController::class, 'getTransctionChart'])->name('get-transaction-chart');
    Route::post('deposit', [\App\Http\Controllers\Api\Transaction\TransactionController::class,'deposit'])->name('deposit');
    Route::post('withdraw', [\App\Http\Controllers\Api\Transaction\TransactionController::class,'withdraw'])->name('withdraw');

});