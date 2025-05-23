<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Collection;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Collection::factory()->create([
            'name' => 'My Collection',
            'description' => 'This is a custom collection.',
            'type' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Collection::factory()->create([
            'name' => 'Public Collection',
            'description' => 'This is a public collection.',
            'type' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        User::factory()->create([
            'first_name' => 'Jon',
            'last_name' => 'Doe',
            'username' => 'jondoe2025',
            'email' => 'jondoe@example.com',
            'type' => 'basic',
            'admin_level' => 1,
            'admin_id' => 0,
            'view_level' => 0,
            'language' => 'en',
            'timezone' => 'UTC',
            'password' => bcrypt('password'),
            'demo' => false,
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
            'email_verified_at' => now(),
            'App\Models\Collection' => 1,
        ]);
    }
    
    // User::factory(10)->create();
}
