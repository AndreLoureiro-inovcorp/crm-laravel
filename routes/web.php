<?php

use App\Http\Controllers\EntityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\DealController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('entities', EntityController::class);
    Route::resource('people', PersonController::class);
   
    Route::resource('deals', DealController::class);
    Route::patch('deals/{deal}/stage', [DealController::class, 'updateStage'])
        ->name('deals.updateStage');
});

require __DIR__.'/settings.php';
