<?php

use App\Livewire\Dashboard;
use App\Livewire\Dashboard\CrearProducto;
use App\Livewire\Dashboard\Productos;
use App\Livewire\Login;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', function () {
    return view('welcome');
});

// Login page
Route::get('/login', Login::class)->name('login');

// Dashboard routes
Route::group(['middleware' => 'auth', 'prefix' => 'dashboard'], function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/productos', Productos::class)->name('dashboard.productos');
    Route::get('/productos/crear', CrearProducto::class)->name('dashboard.productos.crear');
});
