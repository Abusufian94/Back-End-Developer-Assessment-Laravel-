<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Http\Resources\LogoutResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->userRepository->register($request->validated());
        return new JsonResponse([
            'data' => (new AuthResource($data))->toArray($request),
            'status' => 'success',
            'message' => 'Operation successful',
        ], 201);
    }

   
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->userRepository->login($request->only('email', 'password'));
        return new JsonResponse([
            'data' => (new AuthResource($data))->toArray($request),
            'status' => 'success',
            'message' => 'Operation successful',
        ], 200);
    }

   
    public function logout(Request $request): LogoutResource
    {
        $this->userRepository->logout($request->user());
        return new LogoutResource(null);
    }
}