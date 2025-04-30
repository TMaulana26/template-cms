<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $baseUsername = 'tmtestuser';
        $baseEmail = 'tmtestuser';
        $baseName = 'user test';
        $password = '123456789';

        for ($i = 1; $i <= 5; $i++) {

            $username = $baseUsername . str_pad($i, 2, '0', STR_PAD_LEFT);
            $email = $baseEmail . str_pad($i, 2, '0', STR_PAD_LEFT) . '@yopmail.com';
            $name = $baseName . ' ' . $i;

            $attributes = [
                'email' => $email,
            ];

            $values = [
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
            ];

            try {
                $user = User::firstOrCreate($attributes, $values);

                if ($user->wasRecentlyCreated) {
                    Log::info('Test user created successfully.', [
                        'email' => $user->email,
                        'user_id' => $user->id
                    ]);
                } else {
                    Log::info('Test user already exists, skipping creation.', [
                        'email' => $user->email,
                        'user_id' => $user->id ?? 'N/A'
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to create or find test user.', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                    'exception' => get_class($e)
                ]);
            }
        }
    }
}
