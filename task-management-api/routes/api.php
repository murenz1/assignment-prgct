<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\API\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/health', [HealthController::class, 'check']); // Updated path to match frontend expectation
Route::get('/test/public', [TestController::class, 'publicTest']);
Route::get('/roles', [AuthController::class, 'roles']);

// Simple registration route (alternative implementation)
Route::post('/simple-register', [\App\Http\Controllers\API\SimpleRegisterController::class, 'register']);

// Test database connection
Route::get('/test-db', function() {
    try {
        // Test database connection
        $connection = DB::connection()->getPdo();
        
        // Get database name
        $database_name = DB::connection()->getDatabaseName();
        
        // Get users table structure
        $users_columns = DB::select('SHOW COLUMNS FROM users');
        
        return response()->json([
            'connection' => 'Connected successfully to database: ' . $database_name,
            'users_columns' => $users_columns
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Database connection failed: ' . $e->getMessage()
        ], 500);
    }
});

// Test user creation
Route::get('/test-create-user', function() {
    try {
        // Create a test user with UUID
        $user = new \App\Models\User();
        $user->id = (string) \Illuminate\Support\Str::uuid();
        $user->name = 'Test User ' . time();
        $user->email = 'test' . time() . '@example.com';
        $user->password = \Illuminate\Support\Facades\Hash::make('password123');
        $saved = $user->save();
        
        // Return success response
        return response()->json([
            'success' => $saved,
            'user' => $user,
            'message' => 'Test user created successfully'
        ]);
    } catch (\Exception $e) {
        // Return detailed error for debugging
        return response()->json([
            'error' => 'User creation failed: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::get('/test/protected', [TestController::class, 'protectedTest']);
    
    // Project routes
    Route::apiResource('projects', ProjectController::class);
    
    // Task routes
    Route::apiResource('tasks', TaskController::class);
});
