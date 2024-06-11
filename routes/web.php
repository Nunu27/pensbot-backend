<?php

use App\Http\Controllers\Scrapers\PrestasiScraper;
use App\Http\Controllers\Scrapers\PMBScraper;
use Illuminate\Support\Facades\Route;


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

Route::get('/', function () {
    return response()->json([
        'success' => true,
        'message' => "PENSBot Server"
    ]);
});

// 1c
Route::get('/prestasi', [PrestasiScraper::class, 'prestasi']);

// 3b

// 3c
Route::get('/snbp', [PMBScraper::class, 'snbp']);
Route::get('/simandiri', [PMBScraper::class, 'simandiri']);
Route::get('/snbt', [PMBScraper::class, 'snbt']);
