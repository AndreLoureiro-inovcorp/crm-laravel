<?php

use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProductStatsController;
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

    Route::patch('deals/{deal}/stage', [DealController::class, 'updateStage'])->name('deals.updateStage');

    Route::post('deals/{deal}/proposal/upload', [DealController::class, 'uploadProposal'])->name('deals.uploadProposal');
    Route::post('deals/{deal}/proposal/send', [DealController::class, 'sendProposal'])->name('deals.sendProposal');

    Route::post('deals/{deal}/products', [DealController::class, 'addProduct'])->name('deals.addProduct');
    Route::delete('deals/{deal}/products/{product}', [DealController::class, 'removeProduct'])->name('deals.removeProduct');

    Route::post('deals/{deal}/activities', [DealController::class, 'storeActivity'])->name('deals.storeActivity');

    Route::resource('deals', DealController::class);

    Route::resource('calendar', CalendarEventController::class);

    Route::get('products/stats', [ProductStatsController::class, 'index'])->name('products.stats');
    Route::get('products/stats/export', [ProductStatsController::class, 'export'])->name('products.stats.export');
    Route::get('products/stats/{productName}', [ProductStatsController::class, 'show'])->name('products.stats.show');
});

require __DIR__.'/settings.php';
