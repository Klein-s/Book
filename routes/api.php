<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function (){

    //获取书架
    Route::get('bookShelf',[\App\Http\Controllers\API\BookController::class,'getBookShelf']);
    Route::get('books',[\App\Http\Controllers\API\BookController::class, 'index']);
    Route::get('books/{cid}/{bid}',[\App\Http\Controllers\API\BookController::class, 'getBook']);
    Route::get('books/{cid}/{bid}/{num}',[\App\Http\Controllers\API\BookController::class, 'getBookInfo']);
    Route::get('catalog/{cid}/{bid}',[\App\Http\Controllers\API\BookController::class, 'getCatalog']);
    Route::get('search/{name}/',[\App\Http\Controllers\API\BookController::class, 'searchBook']);
});
