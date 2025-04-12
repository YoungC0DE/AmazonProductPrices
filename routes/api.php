<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get("/ping", function () {
    return 'sim';
});


Route::prefix('ticket')
    ->namespace('App\Http\Controllers')
    ->group(function () {
        Route::post('create', 'TicketController@create')->middleware('limitRate:create');
        Route::get('get-all', 'TicketController@getAll')->middleware('limitRate:getAll');
        Route::get('{ticketId}', 'TicketController@get')->middleware('limitRate:get');
    });