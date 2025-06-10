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

            if (!$token = JWTAuth::attempt($credentials)) {
                Log::warning('Login failed: Invalid credentials', ['email' => $request->email]);
                return response()->json(['message' => 'Invalid email or password.'], 401);
            }

            $user = User::whereRaw('LOWER(email) = ?', [strtolower($request->email)])->first();

            // Set HTTP-only cookie
            return response()->json([
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                    ],
                    'token' => $token, // Optional: return token in body
                ],
                'message' => 'Login successful.',
            ], 200)->cookie('auth_token', $token, 60, null, null, true, true, false, 'Strict');
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
            // Try to get token from cookie or Authorization header
            $token = $request->cookie('auth_token') ?: $request->bearerToken();
            if (!$token) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();
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
    public function refresh(Request $request): JsonResponse
    {
        try {
            // Manually extract token from Authorization header
            $authHeader = $request->header('Authorization');
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                Log::warning('No valid Authorization header provided for token refresh', [
                    'header' => $authHeader,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['message' => 'Authorization header missing or invalid.'], 401);
            }

            $token = str_replace('Bearer ', '', $authHeader);
            if (empty($token)) {
                Log::warning('Empty token in Authorization header', [
                    'header' => $authHeader,
                    'ip' => $request->ip(),
                ]);
                return response()->json(['message' => 'Token is empty.'], 401);
            }

            // Set the token explicitly for JWTAuth
            JWTAuth::setToken($token);

            // Attempt to refresh the token
            $newToken = JWTAuth::refresh();
            $user = JWTAuth::setToken($newToken)->authenticate();

            Log::info('Token refreshed successfully', [
                'user_id' => $user?->id,
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'data' => ['token' => $newToken],
                'message' => 'Token refreshed successfully.',
            ], 200);
        } catch (TokenExpiredException $e) {
            Log::error('Token refresh failed: Token expired', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Token has expired and cannot be refreshed.'], 401);
        } catch (TokenBlacklistedException $e) {
            Log::error('Token refresh failed: Token blacklisted', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Token is blacklisted.'], 401);
        } catch (TokenInvalidException $e) {
            Log::error('Token refresh failed: Token invalid', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Token is invalid.'], 401);
        } catch (JWTException $e) {
            Log::error('Token refresh error', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'stack' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to refresh token.'], 401);
        }
    }

}