<?php

// Load the Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting database test...\n";

try {
    // Test database connection
    $connection = DB::connection()->getPdo();
    echo "Connected to database: " . DB::connection()->getDatabaseName() . "\n";
    
    // Check users table structure
    echo "Users table structure:\n";
    $columns = DB::select('SHOW COLUMNS FROM users');
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Key: {$column->Key}, Default: " . 
             ($column->Default === null ? 'NULL' : $column->Default) . ")\n";
    }
    
    // Try to create a user directly
    echo "\nTrying to create a test user...\n";
    $user = new App\Models\User();
    $user->id = (string) \Illuminate\Support\Str::uuid();
    $user->name = 'Test User ' . time();
    $user->email = 'test' . time() . '@example.com';
    $user->password = \Illuminate\Support\Facades\Hash::make('password123');
    
    // Dump the user object before saving
    echo "User object before save:\n";
    var_dump($user->toArray());
    
    // Try to save and report result
    $result = $user->save();
    echo "User save result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    if ($result) {
        echo "User created with ID: {$user->id}\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "Test completed.\n";
