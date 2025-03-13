<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;

Route::get('/', [RecordController::class, 'index'])->name('home');
Route::post('/set-google-sheet-url', [RecordController::class, 'setGoogleSheetUrl'])->name('set-google-sheet-url');
Route::post('/generate-rows', [RecordController::class, 'generate'])->name('generate-rows');
Route::post('/clear-table', [RecordController::class, 'clear'])->name('clear-table');
Route::post('/store', [RecordController::class, 'store'])->name('store');
Route::get('/edit/{id}', [RecordController::class, 'edit'])->name('edit');
Route::put('/update/{id}', [RecordController::class, 'update'])->name('update');
Route::delete('/delete/{id}', [RecordController::class, 'destroy'])->name('delete');
Route::get('/sync', [RecordController::class, 'syncToGoogleSheets'])->name('sync');
Route::get('/fetch/{count?}', [RecordController::class, 'fetchFromGoogleSheets'])->name('fetch');