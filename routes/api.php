<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BidController;
use App\Http\Controllers\Api\V1\MetadataController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
// non-auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//auth routes:
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/account', [AuthController::class, 'deleteAccount']); 
    
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update', [UserController::class, 'update']);

    //projects:
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::get('/projects/{id}', [ProjectController::class, 'show']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

    //Bids
    Route::post('/bids/{id}/accept', [BidController::class, 'accept']);
    Route::post('/bids/{id}/reject', [BidController::class, 'reject']);
    Route::post('/bids', [BidController::class, 'store']);

    //Metadata
    Route::get('/skills', [MetadataController::class, 'skills']);
    Route::get('/tags', [MetadataController::class, 'tags']);
    Route::get('/cities', [MetadataController::class, 'cities']);
    Route::get('/countries',[MetadataController::class, 'countries']);

    //Reviews
    Route::post('/projects/{id}/close', [ProjectController::class, 'markAsClosed']);
    Route::post('/reviews', [ReviewController::class, 'store']);

    //Stats
    Route::get('/stats', [StatsController::class, 'index']);
});