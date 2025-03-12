<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/sync', [RecordController::class, 'syncToGoogleSheets']);
Route::get('/fetch/{count?}', [RecordController::class, 'fetchFromGoogleSheets']);

