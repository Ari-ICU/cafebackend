<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users,email|max:255',
                'password' => 'required|string|min:8|max:255',
                
            ]);
          
            if ($validator->fails()) {
                Log::warning('Validation error during login', ['errors' => $validator->errors()]);
                return response()->json([
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            Log::info('Login attempt', ['email' => $request->email, 'ip' => $request->ip()]);

            $credentials = $request->only('email', 'password');

            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
                    return response()->json([
                        'message' => 'Invalid email or password.',
                    ], 401);
                }
            } catch (JWTException $e) {
                Log::error('Could not create token', ['error' => $e->getMessage()]);
                return response()->json([
                    'message' => 'Could not create token.',
                ], 500);
            }

            $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

            Log::info('User logged in', ['email' => $user->email, 'user_id' => $user->id]);

            return response()->json([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                    'token' => $token,
                ],
                'message' => 'Login successful.',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Login error', ['error' => $e->getMessage(), 'email' => $request->email]);
            return response()->json([
                'message' => 'Failed to process login request.',
            ], 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            Log::info('User logged out', ['user_id' => JWTAuth::user()?->id]);

            return response()->json([
                'message' => 'Logged out successfully.',
            ], 200);
        } catch (JWTException $e) {
            Log::error('Logout error', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Failed to log out.',
            ], 500);
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $user = JWTAuth::user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return response()->json([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                ],
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Fetch user error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to fetch user data.'], 500);
        }
    }


}