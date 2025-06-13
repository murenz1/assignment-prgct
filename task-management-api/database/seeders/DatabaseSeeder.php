<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Call the roles seeder first to ensure roles exist
        $this->call(RolesTableSeeder::class);
        
        // Call the admin user seeder
        $this->call(AdminUserSeeder::class);
        
        // Optionally create some test users with the user role
        // \App\Models\User::factory(5)->create()->each(function($user) {
        //     $userRole = \App\Models\Role::where('name', 'user')->first();
        //     $user->roles()->attach($userRole);
        // });
    }
}
