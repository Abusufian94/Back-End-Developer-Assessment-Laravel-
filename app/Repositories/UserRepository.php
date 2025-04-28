<?php

namespace App\Repositories;

use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @return array
     * @throws ApiException
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        try {
            $user->assignRole($data['role']);
        } catch (\Exception $e) {
            throw new ApiException(
                message: 'Failed to assign role.',
                error: 'role_assignment_failed',
                code: 400,
                details: ['reason' => $e->getMessage()]
            );
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Log in a user.
     *
     * @param array $credentials
     * @return array
     * @throws ApiException
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw new ApiException(
                message: 'Invalid credentials.',
                error: 'invalid_credentials',
                code: 401,
                details: []
            );
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    /**
     * Log out a user.
     *
     * @param User $user
     * @return void
     */
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}