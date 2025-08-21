<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Stancl\Tenancy\Database\Models\Tenant;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test tenant
        Tenant::create([
            'id' => 'test-dealership',
            'data' => [
                'name' => 'Test Dealership',
            ],
        ]);
    }
}
