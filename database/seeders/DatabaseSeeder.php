<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        echo "\n";
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            BaseDataSeeder::class,
            AdminUserSeeder::class,
            DemoUserSeeder::class,
            UsersSeeder::class,
            UserRelatedSeeder::class,
        ]);
        SeedProgress::flushLine();
        echo "\n";
    }
}
