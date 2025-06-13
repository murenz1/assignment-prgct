<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First, truncate the personal_access_tokens table to remove any existing tokens
        // This is safe because tokens can be regenerated
        DB::table('personal_access_tokens')->truncate();
        
        // Modify the tokenable_id column to be a string type to support UUIDs
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Change the tokenable_id column from unsignedBigInteger to string
            $table->string('tokenable_id', 36)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Truncate the table first to avoid conversion errors
        DB::table('personal_access_tokens')->truncate();
        
        // Change back to unsignedBigInteger
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tokenable_id')->change();
        });
    }
};
