<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Request;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('tickets', TicketController::class);
    Route::apiResource('tickets/{ticket}/comments', CommentController::class)->shallow();
    Route::get('/tickets/{ticket}/status', [TicketController::class, 'status']);
    Route::put('/tickets/{ticket}/status', [TicketController::class, 'updateStatus']);
});