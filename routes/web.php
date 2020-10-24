<?php

use App\Http\Controllers\OrderController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api/cms/v1')->name('api.cms.v1')->group(function () {
    Route::prefix('orders')->name('.orders')->group(function () {
        Route::get('', [OrderController::class, 'show'])->name('.show');
        Route::post('', [OrderController::class, 'store'])->name('.store');
    });
});
