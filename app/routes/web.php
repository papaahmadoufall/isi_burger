<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use Inertia\Inertia;
use App\Http\Controllers\burgerController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\PaymentController;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Shop route - accessible to everyone but with different behavior when authenticated
Route::get('/shop', function () {
    return Inertia::render('shop/Shop', [
        'isAuthenticated' => auth()->check(),
        'userRole' => auth()->check() ? auth()->user()->role : null,
        'canOrder' => auth()->check() && auth()->user()->hasRole('client'),
    ]);
})->name('shop');

// Routes for authenticated users
Route::middleware(['auth'])->group(function () {
    // Public burger routes
    Route::get('/burgers', [burgerController::class, 'index'])
        ->name('burgers.index');
    
    Route::get('/burgers/{id}', [burgerController::class, 'show'])
        ->name('burgers.show');

    // Client routes
    Route::middleware('checkrole:client')->group(function () {
        Route::post('/commands', [CommandController::class, 'store'])
            ->name('commands.store');
        
        // Client can view their own commands
        Route::get('/my-commands', [CommandController::class, 'myCommands'])
            ->name('commands.my');
    });

    // Admin routes
    Route::middleware('checkrole:admin')->group(function () {
        Route::get('dashboard', function () {
            return Inertia::render('Dashboard');
        })->name('dashboard');

        Route::get('dashboard/burgers', function () {
            return Inertia::render('burgers/Burger');
        })->name('dashboard_burgers');

        // Burger management
        Route::post('/burgers', [burgerController::class, 'store'])
            ->name('storeBurger');
        Route::put('/burgers/{id}', [burgerController::class, 'update'])
            ->name('burgers.update');
        Route::delete('/burgers/{id}', [burgerController::class, 'destroy'])
            ->name('burgers.destroy');

        // Payment management
        Route::get('/dashboard/payments', [PaymentController::class, 'index'])
            ->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])
            ->name('payments.store');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])
            ->name('payments.update');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])
            ->name('payments.destroy');

        // API route for refreshing payments list
        Route::get('/api/payments', [PaymentController::class, 'getAll'])
            ->name('api.payments.all');

        // Command management for admin
        Route::get('/dashboard/commands', [CommandController::class, 'index'])
            ->name('commands.index');
        Route::put('/commands/{command}', [CommandController::class, 'update'])
            ->name('commands.update');
        Route::delete('/commands/{command}', [CommandController::class, 'destroy'])
            ->name('commands.destroy');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
