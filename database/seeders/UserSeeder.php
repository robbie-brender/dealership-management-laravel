<?php

namespace Database\Seeders;

use App\Models\Dealership;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test dealership
        $dealership = Dealership::where('name', 'Test Dealership')->first();
        
        if ($dealership) {
            // Create admin user
            User::create([
                'name' => 'Test Admin',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'dealership_id' => $dealership->id,
                'role' => 'admin',
            ]);
            
            // Create agent user
            User::create([
                'name' => 'Test Agent',
                'email' => 'agent@example.com',
                'password' => Hash::make('password'),
                'dealership_id' => $dealership->id,
                'role' => 'agent',
            ]);
        }
    }
}
