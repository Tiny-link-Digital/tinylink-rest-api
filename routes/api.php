<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;
use App\Http\Controllers\UserController;

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

Route::prefix('url')->group(function () {
    Route::post('/create', [UrlController::class, 'create']);
    Route::get('/list', [UrlController::class, 'list']);
    Route::get('/{hash}', [UrlController::class, 'get']);
});

Route::post('user/signin', [UserController::class, 'signIn']);

# Quando uma rota não é encontrada, enviar esse fallback ao cliente.
Route::fallback(function(){
    return response()->json([
        'message' => 'A rota solicitada não foi encontrada.'], 404);
});
