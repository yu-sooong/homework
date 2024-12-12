<?php

use App\Http\Controllers\API\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/orders', [OrderController::class, 'store']);

Route::get('/orders/{id}', [OrderController::class, 'show']);
