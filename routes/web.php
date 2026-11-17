<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use App\Livewire\Dashboard;
use App\Livewire\Dashboard\Productos;

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
});