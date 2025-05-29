<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Collection;
use App\Models\Group;
use App\Models\Subgroup;
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
            'name' => 'Default collection',
            'description' => 'This is the default collection.',
            'type' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Collection::factory()->create([
            'name' => 'Custom Collection',
            'description' => 'This is a custom collection.',
            'type' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'johndoe@example.com',
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
            'collection_id' => 1,
        ]);

        Group::factory()->create([
            'name' => 'Income',
            'description' => 'All kinds of income, for example salary, gifts, etc.',
            'type' => 1, // income
            'privacy' => 0, // public
            'collection_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Food',
            'description' => 'All kinds of food expenses, for example groceries, restaurants, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Housing',
            'description' => 'All kinds of housing expenses, for example rent, mortgage, utilities, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Utilities',
            'description' => 'All kinds of utility expenses, for example electricity, water, gas, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Clothes',
            'description' => 'All kinds of clothing expenses, for example clothes, shoes, accessories, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Internet & Co.',
            'description' => 'All kinds of entertainment and subscription expenses, for example streaming services, internet, mobile phone plans etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Subgroup::factory()->create([
            'name' => 'Salary',
            'description' => 'This is the salary subgroup.',
            'privacy' => 0, // public
            'group_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Minijob',
            'description' => 'This is the minijob subgroup.',
            'privacy' => 0, // public
            'group_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Groceries',
            'description' => 'This is the groceries subgroup.',
            'privacy' => 0, // public
            'group_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Sweets',
            'description' => 'This is the sweets subgroup.',
            'privacy' => 0, // public
            'group_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Takeaway',
            'description' => 'This is the takeaway subgroup.',
            'privacy' => 0, // public
            'group_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Restaurants',
            'description' => 'This is the restaurants subgroup.',
            'privacy' => 0, // public
            'group_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Credit',
            'description' => 'This is the credit subgroup.',
            'privacy' => 0, // public
            'group_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Rent',
            'description' => 'This is the rent subgroup.',
            'privacy' => 0, // public
            'group_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Texes',
            'description' => 'This is the taxes subgroup.',
            'privacy' => 0, // public
            'group_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Insurance',
            'description' => 'This is the insurance subgroup.',
            'privacy' => 0, // public
            'group_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Maintenance & Repairs',
            'description' => 'This is the maintenance and repairs subgroup.',
            'privacy' => 0, // public
            'group_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Trash & Recycling',
            'description' => 'This is the trash and recycling subgroup.',
            'privacy' => 0, // public
            'group_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Heating & Cooling',
            'description' => 'This is the heating and cooling subgroup.',
            'privacy' => 0, // public
            'group_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Electricity',
            'description' => 'This is the electricity subgroup.',
            'privacy' => 0, // public
            'group_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Gas',
            'description' => 'This is the gas subgroup.',
            'privacy' => 0, // public
            'group_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Water',
            'description' => 'This is the water subgroup.',
            'privacy' => 0, // public
            'group_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Clothes',
            'description' => 'This is the clothes subgroup.',
            'privacy' => 0, // public
            'group_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Shoes',
            'description' => 'This is the shoes subgroup.',
            'privacy' => 0, // public
            'group_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Accessories',
            'description' => 'This is the accessories subgroup.',
            'privacy' => 0, // public
            'group_id' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Internet',
            'description' => 'This is the internet subgroup.',
            'privacy' => 0, // public
            'group_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Mobile Phone',
            'description' => 'This is the mobile phone subgroup.',
            'privacy' => 0, // public
            'group_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Streaming Services',
            'description' => 'This is the streaming services subgroup.',
            'privacy' => 0, // public
            'group_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'Subscriptions',
            'description' => 'This is the subscriptions subgroup.',
            'privacy' => 0, // public
            'group_id' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    // User::factory(10)->create();
}
