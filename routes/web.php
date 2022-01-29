<?php

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

Route::namespace('App\Http\Infrastructure\PdfFile\WebPage')
    ->group(function () {
        Route::redirect('/pdfs', '/');
        Route::redirect('/home', '/');
        Route::get('/', 'PdfFileController@index')->name('home');
        Route::post('/pdfs', 'PdfFileController@store');
    });

Route::namespace('App\Http\Infrastructure\Note\API')
    ->prefix('api')
    ->group(function () {
        Route::get('/note', 'NoteController@index');
        Route::post('/note', 'NoteController@store');
    });
