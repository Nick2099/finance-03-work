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
        $this->createUsers();

        $this->createCollections();

        $this->createGroups();

        $this->createSubgroups();
    }

    private function getGroupId(string $groupName, int $collectionId): ?int
    {
        $group = Group::where('name', $groupName)
            ->where('collection_id', $collectionId)
            ->first();

        return $group ? $group->id : null;
    }

    private function createUsers(): void
    {
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
            'demo' => true, // true = demo user, false = normal user
            'currency' => 'USD',
            'created_at' => now(),
            'updated_at' => now(),
            'email_verified_at' => now(),
            'collection_id' => 1,
            'twofa' => 0, // 0 = disabled, 1 = enabled
        ]);
        // User::factory(10)->create();
    }

    private function createCollections(): void
    {
        Collection::factory()->create([
            'name' => 'Default collection',
            'description' => 'This is the default english collection.',
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
    }

    private function createGroups(): void
    {
        // Create groups for the default english collection with ID 1
        $collection = 1;
        // State - type 0
        Group::factory()->create([
            'name' => 'State',
            'description' => 'All kinds of state, for example bank accounts, cash, etc.',
            'type' => 0, // state
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Income - type 1
        Group::factory()->create([
            'name' => 'Income',
            'description' => 'All kinds of income, for example salary, gifts, etc.',
            'type' => 1, // income
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // Correction - type 3
        Group::factory()->create([
            'name' => 'Correction',
            'description' => 'Difference between state and expenses/income, for example when you forgot to add an expense or income.',
            'type' => 3, // correction
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Expenses - all of them are type 2
        Group::factory()->create([
            'name' => 'Food',
            'description' => 'All kinds of food expenses, for example groceries, restaurants, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Housing',
            'description' => 'All kinds of housing expenses, for example rent, mortgage, utilities, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Utilities',
            'description' => 'All kinds of utility expenses, for example electricity, water, gas, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Clothes',
            'description' => 'All kinds of clothing expenses, for example clothes, shoes, accessories, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Subscriptions',
            'description' => 'All kinds of entertainment and subscription expenses, for example streaming services, internet, mobile phone plans etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Hygiene',
            'description' => 'All kinds of hygiene, makeup and personal care expenses, for example cosmetics, toiletries, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Health',
            'description' => 'All kinds of health and medicine expenses, for example doctor visits, medications, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Group::factory()->create([
            'name' => 'Car',
            'description' => 'All kinds of car and transport expenses, for example fuel, public transport, etc.',
            'type' => 2, // expense
            'privacy' => 0, // public
            'collection_id' => $collection,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createSubgroups(): void
    {
        $groupId = $this->getGroupId('State', 1);
        Subgroup::factory()->create([
            'name' => 'state.bank-account-1',
            'description' => 'This is the bank account subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'state.bank-account-2',
            'description' => 'This is the bank account subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'state.cash',
            'description' => 'This is the cash subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'state.cash-savings',
            'description' => 'This is the cash subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $groupId = $this->getGroupId('Income', 1);
        Subgroup::factory()->create([
            'name' => 'income.salary',
            'description' => 'This is the salary subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'income.minijob',
            'description' => 'This is the minijob subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'income.other-income',
            'description' => 'This is the other-income subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $groupId = $this->getGroupId('Correction', 1);
        Subgroup::factory()->create([
            'name' => 'correction.correction',
            'description' => 'This is the correction subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Food', 1);
        Subgroup::factory()->create([
            'name' => 'food.groceries',
            'description' => 'This is the groceries subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'food.sweets',
            'description' => 'This is the sweets subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'food.takeaway',
            'description' => 'This is the takeaway subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'food.restaurants',
            'description' => 'This is the restaurants subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'food.meal-at-work',
            'description' => 'This is the meal at work subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Housing', 1);
        Subgroup::factory()->create([
            'name' => 'housing.credit',
            'description' => 'This is the credit subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'housing.rent',
            'description' => 'This is the rent subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'housing.mortgage',
            'description' => 'This is the mortgage subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'housing.taxes',
            'description' => 'This is the taxes subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'housing.insurance',
            'description' => 'This is the insurance subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'housing.maintenance',
            'description' => 'This is the maintenance and repairs subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Utilities', 1);
        Subgroup::factory()->create([
            'name' => 'utilities.trash',
            'description' => 'This is the trash and recycling subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.heating',
            'description' => 'This is the heating subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.cooling',
            'description' => 'This is the heating subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.electricity',
            'description' => 'This is the electricity subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.gas',
            'description' => 'This is the gas subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.water',
            'description' => 'This is the water subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.sewage',
            'description' => 'This is the sewage subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'utilities.hot-water',
            'description' => 'This is the hot water subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Clothes', 1);
        Subgroup::factory()->create([
            'name' => 'clothes.clothes',
            'description' => 'This is the clothes subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'clothes.shoes',
            'description' => 'This is the shoes subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'clothes.accessories',
            'description' => 'This is the accessories subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Subscriptions', 1);
        Subgroup::factory()->create([
            'name' => 'subscriptions.internet',
            'description' => 'This is the internet subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'subscriptions.mobile',
            'description' => 'This is the mobile phone subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'subscriptions.tv',
            'description' => 'This is the television subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'subscriptions.streaming',
            'description' => 'This is the streaming services subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'subscriptions.gaming',
            'description' => 'This is the gaming subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'subscriptions.newspaper',
            'description' => 'This is the gaming subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'subscriptions.other',
            'description' => 'This is the subscriptions subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Hygiene', 1);
        Subgroup::factory()->create([
            'name' => 'hygiene.personal',
            'description' => 'This is the personal care subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'hygiene.cosmetics',
            'description' => 'This is the cosmetics subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'hygiene.hairdresser',
            'description' => 'This is the hairdresser subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'hygiene.cleaning',
            'description' => 'This is the cleaning products subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'hygiene.other',
            'description' => 'This is the other hygiene subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Health', 1);
        Subgroup::factory()->create([
            'name' => 'health.medicines',
            'description' => 'This is the medicines subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'health.doctor',
            'description' => 'This is the doctor visits subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'health.dentist',
            'description' => 'This is the dentist visits subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'health.insurance',
            'description' => 'This is the health insurance subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'health.other',
            'description' => 'This is the other health subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $groupId = $this->getGroupId('Car', 1);
        Subgroup::factory()->create([
            'name' => 'car.credit',
            'description' => 'This is the car credit subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.fuel',
            'description' => 'This is the car fuel subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.insurance',
            'description' => 'This is the car insurance subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.parts',
            'description' => 'This is the car parts subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.repairs',
            'description' => 'This is the car repairs subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.parking',
            'description' => 'This is the car parking subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.tolls',
            'description' => 'This is the car tolls subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.wash',
            'description' => 'This is the car wash subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Subgroup::factory()->create([
            'name' => 'car.other',
            'description' => 'This is the car other subgroup.',
            'privacy' => 0, // public
            'group_id' => $groupId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
