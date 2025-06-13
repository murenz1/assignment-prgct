<?php

// Load the Laravel environment
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Starting personal_access_tokens table fix...\n";

try {
    // First, check the current schema
    echo "Current schema for personal_access_tokens table:\n";
    $columns = DB::select('SHOW COLUMNS FROM personal_access_tokens');
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Key: {$column->Key}, Default: " . 
             ($column->Default === null ? 'NULL' : $column->Default) . ")\n";
    }
    
    // Check if there are any tokens in the table
    $tokenCount = DB::table('personal_access_tokens')->count();
    echo "\nCurrent token count: {$tokenCount}\n";
    
    // Truncate the table to remove any existing tokens
    DB::statement('TRUNCATE TABLE personal_access_tokens');
    echo "Truncated personal_access_tokens table\n";
    
    // Modify the tokenable_id column to be a string
    echo "Modifying tokenable_id column to VARCHAR(36)...\n";
    DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id VARCHAR(36)');
    echo "Column modified successfully\n";
    
    // Verify the changes
    echo "\nUpdated schema for personal_access_tokens table:\n";
    $columns = DB::select('SHOW COLUMNS FROM personal_access_tokens');
    foreach ($columns as $column) {
        echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Key: {$column->Key}, Default: " . 
             ($column->Default === null ? 'NULL' : $column->Default) . ")\n";
    }
    
    echo "\nFix completed successfully!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
