<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/railways', [AuthController::class, 'railways']);
Route::get('/organizations', [AuthController::class, 'organizations']);


Route::post('/send-message', [AuthController::class, 'send_message']);


Route::post('/send/token', [AuthController::class, 'send_token']);

Route::post('/addreception', [AuthController::class, 'addreception']);


Route::post('/add-member', [AuthController::class, 'addmember']);
Route::put('/update-member/{chat_id}', [AuthController::class, 'updatemember']);


Route::post('/reply/message/{message_id}', [AuthController::class, 'reply_message']);
Route::get('/results', [AuthController::class, 'results']);


Route::get('/history/{chat_id}', [AuthController::class, 'information']);


Route::get('/check/{pinfl}', [AuthController::class, 'checkCadryExodim']);