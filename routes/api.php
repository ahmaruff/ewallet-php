<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group([
    'prefix' => 'transactions',
    'as' => 'transactions.',
    'middleware' => ['auth:sanctum'],
], function() {
    Route::post('deposit', [\App\Http\Controllers\Api\Transaction\TransactionController::class,'deposit'])->name('deposit');
});