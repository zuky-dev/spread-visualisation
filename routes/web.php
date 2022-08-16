<?php

use App\Http\Controllers\ViewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('orderbook', [ViewController::class, 'orderbook'])->name('orderbook.index');

Route::get('{any?}', [ViewController::class, 'redirect'])
    ->where('any', '.*');
