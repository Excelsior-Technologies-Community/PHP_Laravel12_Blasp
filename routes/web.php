<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

Route::get('/', [MessageController::class, 'index']);
Route::post('/check', [MessageController::class, 'check'])->name('check');

// DELETE ROUTE
Route::get('/delete/{id}', [MessageController::class, 'delete'])->name('delete');