<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use Inertia\Inertia;
use App\Http\Controllers\burgerController;

// Routes protégées avec "auth"

    Route::get('/burgers', [burgerController::class, 'index']); // Accessible à tous les utilisateurs authentifiés
    Route::get('/burgers/{id}', [burgerController::class, 'show']); // Accessible à tous les utilisateurs authentifiés

    // Routes réservées aux administrateurs
    Route::post('/burgers', [burgerController::class, 'store']) ->name("storeBurger"); // Créer un burger
    Route::put('/burgers/{id}', [burgerController::class, 'update']); // Mettre à jour un burger
    Route::delete('/burgers/{id}', [burgerController::class, 'destroy']); // Supprimer un burger



Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');



Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', RoleMiddleware::class,'verified'])->name('dashboard');

Route::get('dashboard/burgers', function () {
    return Inertia::render('burgers/Burger');
})->middleware(['auth', RoleMiddleware::class,'verified'])->name('dashboard_burgers');

//Route::get('dashboard/burgers/add', function () {
//    return Inertia::render('Burgers/addBurger');
//})->middleware(['auth', RoleMiddleware::class,'verified'])->name('dashboard_burgers');
//
//Route::get('dashboard/burgers/list', function () {
//    return Inertia::render('Burgers/listBurger');
//})->middleware(['auth', RoleMiddleware::class,'verified'])->name('dashboard_burgers');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
