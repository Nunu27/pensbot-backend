<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CrawlController;


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

Route::get('/', [CrawlController::class, 'index']);
Route::get('/snbp', [CrawlController::class, 'scrapSNBP']);
Route::get('/simandiri', [CrawlController::class, 'scrapSimandiri']);
Route::get('/snbt', [CrawlController::class, 'scrapSNBT']);
Route::post('/webhook', 'WebhookController@handleRequest');
