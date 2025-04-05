<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/transactions', [\App\Http\Controllers\Transaction\TransactionController::class, 'index'])->middleware(['auth'])->name('transactions.index');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
