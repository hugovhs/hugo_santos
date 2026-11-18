<?php

use App\Livewire\Home\DetalleProducto;
use App\Livewire\Home\Index;
use App\Livewire\Home\ListaProductos;
use App\Livewire\Dashboard;
use App\Livewire\Dashboard\Categorias;
use App\Livewire\Dashboard\CrearProducto;
use App\Livewire\Dashboard\EditarProducto;
use App\Livewire\Dashboard\Productos;
use App\Livewire\Login;

use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', Index::class)->name('home.index');
Route::get('/productos', ListaProductos::class)->name('home.productos');
Route::get('/productos/{producto}', DetalleProducto::class)->name('home.productos.detalle');

// Login page
Route::get('/login', Login::class)->name('login');

// Dashboard routes
Route::group(['middleware' => 'auth', 'prefix' => 'dashboard'], function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/productos', Productos::class)->name('dashboard.productos');
    Route::get('/productos/crear', CrearProducto::class)->name('dashboard.productos.crear');
    Route::get('/productos/{producto}/editar', EditarProducto::class)->name('dashboard.productos.editar');
    Route::get('/categorias', Categorias::class)->name('dashboard.categorias');
});
