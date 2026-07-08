<?php

use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MessageController::class, 'index']);
Route::post('/check', [MessageController::class, 'check'])->name('check');
Route::get('/delete/{id}', [MessageController::class, 'delete'])->name('delete');
Route::get('/admin/moderation', [MessageController::class, 'moderationIndex'])->name('moderation.index');
Route::post('/admin/moderation/words', [MessageController::class, 'storeBadWord'])->name('moderation.words.store');