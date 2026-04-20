<?php
namespace App\Services;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name'=> $data['first_name'],
                'last_name'=> $data['last_name'],
                'email'=> strtolower($data['email']),
                'password'=> $data['password'],
                'type'=> $data['type'],
                'city_id' => $data['city_id'] ?? null,
                'is_verified' => $data['type'] === 'client' ? true : false,
            ]);

            if ($user->isFreelancer()) {
                Freelancer::create([
                    'user_id'      => $user->id,
                    'availability' => 'available',
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $userData = $user->isFreelancer() 
                ? $user->load('freelancerProfile') 
                : $user;

            return [
                'user'  => $userData,
                'token' => $token,
            ];
        });
    }

    public function login(string $email, string $password): array
    {
        $user = User::where('email', strtolower($email))->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $userData = $user->isFreelancer() 
            ? $user->load('freelancerProfile') 
            : $user;

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $userData,
            'token' => $token,
        ];
    }

    public function logout($user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function deleteAccount($user): void
    {
        $user->tokens()->delete();
        $user->delete();
    }
}