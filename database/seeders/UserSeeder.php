<?php

namespace Database\Seeders;

use Hash;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUserData = [
            'name' => 'Admin',
            'username' => 'admin01',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456789'),
        ];

        $emailToCheck = $adminUserData['email'];

        $existingUser = User::where('email', $emailToCheck)->first();

        if (!is_null($existingUser)) {
            Log::info('Default admin user already exists, skipping creation.', [
                'email' => $emailToCheck,
                'user_id' => $existingUser->id ?? 'N/A'
            ]);

            return;
        }

        try {
            $newUser = User::create($adminUserData);

            Log::info('Default admin user created successfully.', [
                'email' => $newUser->email,
                'user_id' => $newUser->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create default admin user.', [
                'email' => $emailToCheck,
                'error' => $e->getMessage(),
                'exception' => get_class($e)
            ]);
        }
    }

}
