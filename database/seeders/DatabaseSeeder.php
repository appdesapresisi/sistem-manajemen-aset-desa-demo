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
        $this->call([
            DemoDataSeeder::class,
            NotificationSeeder::class,
            KondisiAsetDemoSeeder::class,
            RuanganInventarisDemoSeeder::class,
            ActivityLogSeeder::class,
            ExportLogSeeder::class,
        ]);
    }
}
