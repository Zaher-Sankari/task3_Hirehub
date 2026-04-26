<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BidController;
use App\Http\Controllers\Api\V1\FreelancerController;
use App\Http\Controllers\Api\V1\MetadataController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\StatsController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

// Non-auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public freelancer browsing
Route::get('/freelancers', [UserController::class, 'index']);
Route::get('/freelancers/{freelancer}', [UserController::class, 'show']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{project}', [ProjectController::class, 'show']);

// Auth routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::delete('/account', [AuthController::class, 'deleteAccount']);

    // User profile
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile/update', [UserController::class, 'update']);
    Route::post('/profile/skills', [UserController::class, 'updateSkills']);
    Route::delete('/profile/skills/{skill}', [UserController::class, 'removeSkill']);

    // Freelancer profile update
    Route::put('/freelancer/profile', [UserController::class, 'updateFreelancerProfile']);

    // Projects
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    Route::post('/projects/{project}/close', [ProjectController::class, 'markAsClosed']);

    // Bids (verified freelancers only)
    Route::middleware(['verified.freelancer'])->group(function () {
        Route::post('/bids', [BidController::class, 'store']);
    });
    Route::get('/bids/{bid}', [BidController::class, 'show']);
    Route::post('/bids/{bid}/accept', [BidController::class, 'accept']);
    Route::post('/bids/{bid}/reject', [BidController::class, 'reject']);

    // Metadata
    Route::get('/skills', [MetadataController::class, 'skills']);
    Route::get('/tags', [MetadataController::class, 'tags']);
    Route::get('/cities', [MetadataController::class, 'cities']);
    Route::get('/countries', [MetadataController::class, 'countries']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Stats (admin only)
    Route::get('/stats', [StatsController::class, 'index'])->middleware('admin');


    // Public freelancer routes (no auth required for browsing)
    Route::get('/freelancers', [FreelancerController::class, 'index']);
    Route::get('/freelancers/top-rated', [FreelancerController::class, 'topRated']);
    Route::get('/freelancers/available', [FreelancerController::class, 'available']);
    Route::get('/freelancers/search/by-skills', [FreelancerController::class, 'searchBySkills']);
    Route::get('/freelancers/{id}', [FreelancerController::class, 'show']);

    // Authenticated freelancer routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/freelancer/dashboard', [FreelancerController::class, 'dashboard']);
        // Admin only
        Route::post('/admin/freelancers/{id}/verify', [FreelancerController::class, 'verify'])
            ->middleware('admin');
    });
});
