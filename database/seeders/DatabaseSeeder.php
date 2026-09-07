<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with complete foundational and operational defaults.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            SettingsSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            PageSeeder::class,
            HomepageSectionSeeder::class,
            NavigationMenuSeeder::class,
            AdSlotSeeder::class,
        ]);
    }
}
