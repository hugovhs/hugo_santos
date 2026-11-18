<?php

use App\Http\Controllers\Api\PagosController;
use Illuminate\Support\Facades\Route;

Route::post('/pagos', [PagosController::class, 'store']);
