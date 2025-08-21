<?php

namespace Database\Seeders;

use App\Models\Dealership;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dealership::create([
            'name' => 'Test Dealership',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'state' => 'TS',
            'zip_code' => '12345',
            'phone' => '555-123-4567',
            'email' => 'test@dealership.com',
            'website' => 'https://testdealership.com',
            'tenant_id' => 'test-dealership',
        ]);
    }
}
