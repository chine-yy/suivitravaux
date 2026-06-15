<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IaChat\IaChatController;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// IA Chat - Query Database endpoint (pour que l'IA puisse interroger la DB)
Route::post('/ia-chat/query-database', [IaChatController::class, 'queryDatabase'])
    ->name('api.ia-chat.query');
