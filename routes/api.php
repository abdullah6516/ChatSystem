<?php

use App\Http\Controllers\api\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(ChatController::class)->group(
    function () {
        Route::post('/login', 'login');
    }
);

Route::controller(ChatController::class)
    ->middleware('auth:sanctum')
    ->prefix('chat')
    ->group(function () {
        Route::get('/getChat', 'getChat');
        Route::get('/getMessages/{id}', 'selectConversation');
        Route::post('/send', 'send');
        Route::post('/upload', 'upload');
        Route::put('/read/{receiver}', 'upload');
    });
