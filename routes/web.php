<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\DashboardController;

// auto direct to login page if not logged in
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/pricing', [PlanController::class, 'index'])->middleware(['auth', 'verified'])->name('pricing');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/pricing', [PlanController::class, 'index'])->name('pricing.index');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
    Route::post('/plans/{plan}/checkout', [PlanController::class, 'checkout'])->name('checkout');

    Route::get('/subscription/{subscription}/success', [SubscriptionController::class, 'updateSubscriptionStatus'])->name('subscription.success');
});

require __DIR__.'/auth.php';
