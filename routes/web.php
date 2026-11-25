<?php

use App\Http\Controllers\BiodataController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BiodataController::class, 'index'])->name('home');

Route::resource('biodata', BiodataController::class)
    ->only(['index', 'store', 'update', 'destroy'])
    ->parameters(['biodata' => 'biodata']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
