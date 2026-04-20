<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $data = $this->authService->register($request->validated());
            return $this->success($data, 'Account created successfully', 201);
        } catch (Exception $e) {
            return $this->error('An error occurred while creating account', 500, $e->getMessage());
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            Log::info('Login attempt', ['email' => $request->email]);
            
            $data = $this->authService->login($request->email, $request->password);
            
            Log::info('Login success', ['data' => $data]);
            
            return $this->success($data, 'Logged in successfully');
        } catch (Exception $e) {
            Log::error('Login failed', ['error' => $e->getMessage()]);
            
            return $this->error('Invalid credentials', 401);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());
        return $this->success(null, 'Logged out successfully');
    }

    public function deleteAccount(Request $request): JsonResponse
    {
        $this->authService->deleteAccount($request->user());
        return $this->success(null, 'Account deleted successfully');
    }
}
