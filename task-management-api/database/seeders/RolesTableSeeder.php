<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin role
        Role::create([
            'name' => 'admin',
            'description' => 'Administrator with full access to all features'
        ]);
        
        // Create user role
        Role::create([
            'name' => 'user',
            'description' => 'Regular user with limited access'
        ]);
        
        // Create manager role
        Role::create([
            'name' => 'manager',
            'description' => 'Project manager with access to manage multiple projects'
        ]);
    }
}
