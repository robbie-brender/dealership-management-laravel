<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run our custom seeders in the correct order
        $this->call([
            TenantSeeder::class,     // First create tenant
            DealershipSeeder::class, // Then create dealership
            UserSeeder::class,       // Then create users
        ]);
    }
}
