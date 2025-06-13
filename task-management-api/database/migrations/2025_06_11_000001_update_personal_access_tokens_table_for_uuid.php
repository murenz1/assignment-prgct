<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First drop the existing morphs columns
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Drop the foreign key constraint if it exists
            $table->dropMorphs('tokenable');
        });

        // Then recreate them with string type for UUID compatibility
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Use uuidMorphs instead of morphs to support UUID foreign keys
            $table->uuidMorphs('tokenable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to integer IDs
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropMorphs('tokenable');
        });

        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->morphs('tokenable');
        });
    }
};
