<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Get all available roles
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function roles()
    {
        $roles = Role::select('id', 'name')->get();
        return response()->json($roles);
    }
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            Log::info('Registration attempt for email: ' . $request->email);
            
            // Validate request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);
            
            Log::info('Validation passed, creating user');
            
            // Create user with basic info and UUID - using the exact same approach that worked in our test endpoint
            $user = new User();
            $user->id = (string) \Illuminate\Support\Str::uuid(); // Generate UUID for ID
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            
            Log::info('User object created with UUID: ' . $user->id);
            
            // Try to save the user and catch any database exceptions
            try {
                $result = $user->save();
                Log::info('User save result: ' . ($result ? 'SUCCESS' : 'FAILED'));
                
                if (!$result) {
                    throw new \Exception('Failed to save user to database');
                }
            } catch (\Exception $dbException) {
                Log::error('Database error during user save: ' . $dbException->getMessage());
                throw $dbException; // Re-throw to be caught by outer try-catch
            }
            
            Log::info('Generating token for user');
            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            Log::info('Registration successful for: ' . $user->email);
            // Create response with token in body
            $response = response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ], 201);
            
            // Also set cookies for the token
            $response->cookie('token', $token, 60*24*7, '/', null, false, false); // 7 days, not secure, not httpOnly for JS access
            $response->cookie('auth', 'true', 60*24*7, '/', null, false, false);
            
            Log::info('Set cookies for token in registration response');
            
            return $response;
        } catch (\Exception $e) {
            // Log detailed error
            Log::error('Registration error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return detailed error for debugging
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Login user and create token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            Log::info('Login attempt for email: ' . $request->email);
            
            // Validate request
            $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            
            Log::info('Login validation passed');
            
            // Check if user exists first
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                Log::info('Login failed: User not found with email: ' . $request->email);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid login credentials',
                ], 401);
            }
            
            Log::info('User found with ID: ' . $user->id);
            
            // Verify password manually to avoid any Auth facade issues
            if (!Hash::check($request->password, $user->password)) {
                Log::info('Login failed: Invalid password for user: ' . $request->email);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid login credentials',
                ], 401);
            }
            
            Log::info('Password verified successfully');
            
            // Generate token
            try {
                $token = $user->createToken('auth_token')->plainTextToken;
                Log::info('Token generated successfully');
            } catch (\Exception $tokenError) {
                Log::error('Token generation error: ' . $tokenError->getMessage());
                throw $tokenError;
            }
            
            // Don't load roles relationship if it doesn't exist yet
            try {
                $userData = $user->toArray();
                Log::info('Login successful for: ' . $request->email);
                
                // Create response with token in body
                $response = response()->json([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user' => $userData,
                    'token' => $token,
                ]);
                
                // Also set cookies for the token
                $response->cookie('token', $token, 60*24*7, '/', null, false, false); // 7 days, not secure, not httpOnly for JS access
                $response->cookie('auth', 'true', 60*24*7, '/', null, false, false);
                
                Log::info('Set cookies for token in response');
                
                return $response;
            } catch (\Exception $relationError) {
                Log::error('Error loading user data: ' . $relationError->getMessage());
                
                // Return basic user data without roles
                $response = response()->json([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'user' => $user,
                    'token' => $token,
                ]);
                
                // Also set cookies for the token
                $response->cookie('token', $token, 60*24*7, '/', null, false, false); // 7 days, not secure, not httpOnly for JS access
                $response->cookie('auth', 'true', 60*24*7, '/', null, false, false);
                
                Log::info('Set cookies for token in response (fallback path)');
                
                return $response;
            }
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ], 500);
        }
    }

    /**
     * Logout user (revoke token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        try {
            return response()->json([
                'user' => $request->user()
            ]);
        } catch (\Exception $e) {
            Log::error('User profile error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Update user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $request->user();
            
            // Validate the request
            $validatedData = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'current_password' => 'required_with:password|string',
                'password' => 'sometimes|string|min:8|confirmed',
            ]);
            
            // Update name if provided
            if (isset($validatedData['name'])) {
                $user->name = $validatedData['name'];
            }
            
            // Update password if provided
            if (isset($validatedData['password'])) {
                // Verify current password
                if (!Hash::check($validatedData['current_password'], $user->password)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Current password is incorrect',
                    ], 422);
                }
                
                $user->password = Hash::make($validatedData['password']);
            }
            
            $user->save();
            
            // Reload the user with roles
            $user->load('roles');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'user' => $user,
            ]);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
