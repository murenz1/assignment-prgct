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
        // First, drop foreign keys that reference users.id
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->listTableForeignKeys('projects');
                if (in_array('projects_user_id_foreign', $foreignKeys)) {
                    $table->dropForeign('projects_user_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->listTableForeignKeys('tasks');
                if (in_array('tasks_user_id_foreign', $foreignKeys)) {
                    $table->dropForeign('tasks_user_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->listTableForeignKeys('role_user');
                if (in_array('role_user_user_id_foreign', $foreignKeys)) {
                    $table->dropForeign('role_user_user_id_foreign');
                }
            });
        }
        
        // Modify the users table to use string IDs
        Schema::table('users', function (Blueprint $table) {
            // Change the ID column to string type
            $table->string('id', 36)->change();
        });
        
        // Update related tables to use string IDs
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'user_id')) {
                    $table->string('user_id', 36)->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
        
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (Schema::hasColumn('tasks', 'user_id')) {
                    $table->string('user_id', 36)->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
        
        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                if (Schema::hasColumn('role_user', 'user_id')) {
                    $table->string('user_id', 36)->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // First, drop foreign keys that reference users.id
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->listTableForeignKeys('projects');
                if (in_array('projects_user_id_foreign', $foreignKeys)) {
                    $table->dropForeign('projects_user_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->listTableForeignKeys('tasks');
                if (in_array('tasks_user_id_foreign', $foreignKeys)) {
                    $table->dropForeign('tasks_user_id_foreign');
                }
            });
        }
        
        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = $this->listTableForeignKeys('role_user');
                if (in_array('role_user_user_id_foreign', $foreignKeys)) {
                    $table->dropForeign('role_user_user_id_foreign');
                }
            });
        }
        
        // Revert the users table to use integer IDs
        Schema::table('users', function (Blueprint $table) {
            // Change the ID column back to bigInteger type
            $table->bigInteger('id')->autoIncrement()->change();
        });
        
        // Update related tables to use integer IDs
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
        
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (Schema::hasColumn('tasks', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
        
        if (Schema::hasTable('role_user')) {
            Schema::table('role_user', function (Blueprint $table) {
                if (Schema::hasColumn('role_user', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->change();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                }
            });
        }
    }
    /**
     * Get the list of foreign keys for a table
     *
     * @param string $table
     * @return array
     */
    protected function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        
        $foreignKeys = [];
        
        try {
            $foreignKeys = array_map(function($key) {
                return $key->getName();
            }, $conn->listTableForeignKeys($table));
        } catch (\Exception $e) {
            // Table might not exist or have no foreign keys
        }
        
        return $foreignKeys;
    }
};
