<?php

use App\Http\Controllers\AutomationRuleController;
use App\Http\Controllers\CalendarEventController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ProductStatsController;
use App\Http\Controllers\PublicFormController;
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

    Route::get('automations', [AutomationRuleController::class, 'index'])->name('automations.index');
    Route::post('automations', [AutomationRuleController::class, 'store'])->name('automations.store');
    Route::patch('automations/{automationRule}/toggle', [AutomationRuleController::class, 'toggle'])->name('automations.toggle');
    Route::delete('automations/{automationRule}', [AutomationRuleController::class, 'destroy'])->name('automations.destroy');

    Route::get('forms', [PublicFormController::class, 'index'])->name('forms.index');
    Route::get('forms/create', [PublicFormController::class, 'create'])->name('forms.create');
    Route::post('forms', [PublicFormController::class, 'store'])->name('forms.store');
    Route::get('forms/{form}', [PublicFormController::class, 'show'])->name('forms.show');
    Route::patch('forms/{form}/toggle', [PublicFormController::class, 'toggle'])->name('forms.toggle');
    Route::delete('forms/{form}', [PublicFormController::class, 'destroy'])->name('forms.destroy');

    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('chat/send', [ChatController::class, 'send'])->name('chat.send');
    Route::delete('chat/clear', [ChatController::class, 'clear'])->name('chat.clear');
});

Route::get('form/{id}', [PublicFormController::class, 'showPublic'])->name('forms.public');
Route::post('form/{id}/submit', [PublicFormController::class, 'submit'])->name('forms.submit');

require __DIR__.'/settings.php';
