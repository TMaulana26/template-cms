<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menuItems = [
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'url' => '/dashboard',
            ],
            [
                'title' => 'Menu',
                'route' => 'menu',
                'url' => '/menu',
            ],
        ];

        foreach ($menuItems as $itemData) {
            $existingMenuItem = Menu::where('title', $itemData['title'])->first();
    
            if (is_null($existingMenuItem)) {
                try {
                    Menu::create($itemData);
    
                } catch (\Exception $e) {
                    Log::error('Failed to create menu item "' . $itemData['title'] . '": ' . $e->getMessage());
                }
            } else {
                Log::info('Menu item already exists, skipping creation: ' . $itemData['title']);
            }
        }
    }
}
