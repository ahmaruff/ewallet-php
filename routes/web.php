<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/transactions', [\App\Http\Controllers\Transaction\TransactionController::class, 'index'])->middleware(['auth'])->name('transactions.index');


Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:admin'],
    'as' => 'admin.'
], function() {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
