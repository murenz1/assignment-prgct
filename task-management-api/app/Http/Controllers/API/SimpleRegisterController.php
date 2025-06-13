<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SimpleRegisterController extends Controller
{
    /**
     * Register a new user with minimal dependencies
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            Log::info('Simple registration attempt for: ' . $request->email);
            
            // Validate request manually to avoid dependencies
            if (empty($request->name)) {
                return response()->json(['error' => 'Name is required'], 422);
            }
            
            if (empty($request->email)) {
                return response()->json(['error' => 'Email is required'], 422);
            }
            
            if (empty($request->password) || strlen($request->password) < 8) {
                return response()->json(['error' => 'Password must be at least 8 characters'], 422);
            }
            
            if ($request->password !== $request->password_confirmation) {
                return response()->json(['error' => 'Passwords do not match'], 422);
            }
            
            // Check if email is already taken
            $existingUser = User::where('email', $request->email)->first();
            if ($existingUser) {
                return response()->json(['error' => 'Email already taken'], 422);
            }
            
            Log::info('Simple validation passed');
            
            // Create user with UUID
            $user = new User();
            $user->id = (string) Str::uuid();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            
            Log::info('Simple user object created with UUID: ' . $user->id);
            
            // Save user
            $result = $user->save();
            Log::info('Simple user save result: ' . ($result ? 'SUCCESS' : 'FAILED'));
            
            if (!$result) {
                throw new \Exception('Failed to save user');
            }
            
            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            // Return success
            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token
            ]);
            
        } catch (\Exception $e) {
            Log::error('Simple registration error: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
