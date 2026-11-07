<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Optional sample cakes: php artisan db:seed --class=CakecraftDemoSeeder
     */
    public function run()
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CakecraftDemoSeeder::class,
        ]);
    }
}
