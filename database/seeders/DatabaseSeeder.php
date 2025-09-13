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

        $this->createPaymentMethods();
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
            'demo' => false, // true = demo user, false = normal user
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
        $groups = [
            ['name' => 'state', 'type' => 0],
            ['name' => 'income', 'type' => 1],
            ['name' => 'correction', 'type' => 3],
            ['name' => 'food', 'type' => 2],
            ['name' => 'housing', 'type' => 2],
            ['name' => 'utilities', 'type' => 2],
            ['name' => 'clothes', 'type' => 2],
            ['name' => 'subscriptions', 'type' => 2],
            ['name' => 'hygiene', 'type' => 2],
            ['name' => 'health', 'type' => 2],
            ['name' => 'car', 'type' => 2],
            ['name' => 'education', 'type' => 2],
            ['name' => 'hobby', 'type' => 2],
            ['name' => 'sport', 'type' => 2],
            ['name' => 'fun', 'type' => 2],
            ['name' => 'other', 'type' => 2],
        ];
        foreach ($groups as $group) {
            Group::factory()->create([
                'name' => $group['name'],
                'description' => $group['name'] . '-desc.',
                'type' => $group['type'],
                'privacy' => 0, // public
                'collection_id' => $collection,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createSubgroups(): void
    {
        $subgroups = [
            // group_name, subgroup_name
            ['state', 'state.bank-account-1'],
            ['state', 'state.bank-account-2'],
            ['state', 'state.cash'],
            ['state', 'state.cash-savings'],
            ['income', 'income.salary'],
            ['income', 'income.minijob'],
            ['income', 'income.other-income'],
            ['correction', 'correction.correction'],
            ['food', 'food.groceries'],
            ['food', 'food.sweets'],
            ['food', 'food.takeaway'],
            ['food', 'food.restaurants'],
            ['food', 'food.meal-at-work'],
            ['housing', 'housing.credit'],
            ['housing', 'housing.rent'],
            ['housing', 'housing.mortgage'],
            ['housing', 'housing.taxes'],
            ['housing', 'housing.insurance'],
            ['housing', 'housing.maintenance'],
            ['utilities', 'utilities.trash'],
            ['utilities', 'utilities.heating'],
            ['utilities', 'utilities.cooling'],
            ['utilities', 'utilities.electricity'],
            ['utilities', 'utilities.gas'],
            ['utilities', 'utilities.water'],
            ['utilities', 'utilities.sewage'],
            ['utilities', 'utilities.hot-water'],
            ['utilities', 'utilities.additional-cost'],
            ['clothes', 'clothes.clothes'],
            ['clothes', 'clothes.shoes'],
            ['clothes', 'clothes.accessories'],
            ['subscriptions', 'subscriptions.internet'],
            ['subscriptions', 'subscriptions.mobile'],
            ['subscriptions', 'subscriptions.tv'],
            ['subscriptions', 'subscriptions.streaming'],
            ['subscriptions', 'subscriptions.gaming'],
            ['subscriptions', 'subscriptions.newspaper'],
            ['subscriptions', 'subscriptions.other'],
            ['hygiene', 'hygiene.personal'],
            ['hygiene', 'hygiene.cosmetics'],
            ['hygiene', 'hygiene.hairdresser'],
            ['hygiene', 'hygiene.cleaning'],
            ['hygiene', 'hygiene.other'],
            ['health', 'health.medicines'],
            ['health', 'health.doctor'],
            ['health', 'health.dentist'],
            ['health', 'health.insurance'],
            ['health', 'health.other'],
            ['car', 'car.credit'],
            ['car', 'car.fuel'],
            ['car', 'car.insurance'],
            ['car', 'car.parts'],
            ['car', 'car.repairs'],
            ['car', 'car.parking'],
            ['car', 'car.tolls'],
            ['car', 'car.wash'],
            ['car', 'car.other'],
            ['education', 'education.after-school-care'],
            ['education', 'education.babysitting'],
            ['education', 'education.day-care'],
            ['education', 'education.tuition'],
            ['education', 'education.books'],
            ['education', 'education.supplies'],
            ['education', 'education.technology'],
            ['education', 'education.uniforms'],
            ['education', 'education.school-bus'],
            ['education', 'education.transport'],
            ['education', 'education.lunch'],
            ['education', 'education.snacks'],
            ['education', 'education.trips'],
            ['education', 'education.accomodation'],
            ['education', 'education.intership'],
            ['education', 'education.other'],
            ['hobby', 'hobby.music'],
            ['hobby', 'hobby.travel'],
            ['hobby', 'hobby.books'],
            ['hobby', 'hobby.games'],
            ['hobby', 'hobby.pets'],
            ['hobby', 'hobby.other'],
            ['fun', 'fun.cinema'],
            ['fun', 'fun.events'],
            ['fun', 'fun.restaurants'],
            ['fun', 'fun.other'],
            ['sport', 'sport.membership'],
            ['sport', 'sport.equipment'],
            ['sport', 'sport.events'],
            ['sport', 'sport.other'],
            ['other', 'other.gifts'],
            ['other', 'other.charity'],
            ['other', 'other.fines'],
            ['other', 'other.other'],
            ['other', 'other.vacation'],
            ['other', 'other.tools'],
        ];
        foreach ($subgroups as [$groupName, $subgroupName]) {
            $groupId = $this->getGroupId($groupName, 1);
            Subgroup::factory()->create([
                'name' => $subgroupName,
                'description' => $subgroupName . '-desc.',
                'privacy' => 0, // public
                'group_id' => $groupId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function createPaymentMethods(): void
    {
        $paymentMethods = [
            // type, provider
            ['type' => 0, 'provider' => "Wallet", 'provider_source' => null], // cash
            ['type' => 0, 'provider' => "Home safe", 'provider_source' => null], // cash - savings
            ['type' => 1, 'provider' => 'Commerzbank', 'provider_source' => null], // bank_transfer
            ['type' => 1, 'provider' => 'Deutsche Bank', 'provider_source' => null], // bank_transfer
            ['type' => 1, 'provider' => 'Addiko Bank', 'provider_source' => null], // bank_transfer
            ['type' => 2, 'provider' => 'Mastercard', 'provider_source' => null], // credit_card
            ['type' => 2, 'provider' => 'Visa', 'provider_source' => null], // credit_card
            ['type' => 3, 'provider' => 'Revolut', 'provider_source' => null], // prepaid_card
            ['type' => 3, 'provider' => 'N26', 'provider_source' => null], // prepaid_card
            ['type' => 3, 'provider' => 'Wise', 'provider_source' => null], // prepaid_card
            ['type' => 4, 'provider' => 'PayPal', 'provider_source' => 5], // payment_provider
            ['type' => 4, 'provider' => 'Klarna', 'provider_source' => 3], // payment_provider
            ['type' => 5, 'provider' => 'Apple Pay', 'provider_source' => null], // payment_provider
            ['type' => 5, 'provider' => 'Google Pay', 'provider_source' => null], // payment_provider
            ['type' => 5, 'provider' => 'Amazon Gift Card', 'provider_source' => null], // gift_card
            ['type' => 5, 'provider' => 'Netflix Gift Card', 'provider_source' => null], // gift_card
        ];
        
        $user = User::first(); // Assuming you want to assign these to the first user
        
        foreach ($paymentMethods as $method) {
            $user->paymentMethods()->create([
                'type' => $method['type'],
                'provider' => $method['provider'],
                'provider_source' => $method['provider_source'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
