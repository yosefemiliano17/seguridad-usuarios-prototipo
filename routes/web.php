<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControladorIniciarSesionLayer;

Route::get('/login', [ControladorIniciarSesionLayer::class, 'create'])->name('login');

Route::post('/login', [ControladorIniciarSesionLayer::class, 'store'])->name('login.store');

Route::post('/logout', [ControladorIniciarSesionLayer::class, 'destroy'])->name('logout');

Route::get('/dashboard', [ControladorIniciarSesionLayer::class, 'dashboard'])
    ->name('dashboard');