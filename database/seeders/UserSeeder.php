<?php

namespace Database\Seeders;

use App\Auth\Models\Address;
use App\Auth\Services\UserService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = UserService::createUser(
            [
                'name' => 'Sai Lin Htut',
                'email' => 'sailinhtut76062@gmail.com',
                'password' => Hash::make('asdf1234'),
                'email_verified_at' => now()
            ]
        );

        $this->command->info('User Data Seeded Successfully.');

        $admin = UserService::createUser(
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('superadmin'),
                'role_id' => 3, // Admin Role
                'email_verified_at' => now()
            ]
        );

        $this->command->info('Admin Data Seeded Successfully.[superadmin@gmail.com:superadmin]');

        Address::create([
            'user_id' => $user->id,
            'label' => 'Home',
            'recipient_name' => $user->name,
            'phone' => $user->phone_one ?? fake()->phoneNumber(),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'Myanmar',
            'latitude' => fake()->latitude(16.5, 20.0),
            'longitude' => fake()->longitude(94.0, 98.0),
            'is_default_shipping' => false,
            'is_default_billing' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        Address::create([
            'user_id' => $user->id,
            'label' => 'Office',
            'recipient_name' => $user->name,
            'phone' => $user->phone_one ?? fake()->phoneNumber(),
            'street_address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'Myanmar',
            'latitude' => fake()->latitude(16.5, 20.0),
            'longitude' => fake()->longitude(94.0, 98.0),
            'is_default_shipping' => true,
            'is_default_billing' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Address Data Seeded Successfully.');
    }
}
