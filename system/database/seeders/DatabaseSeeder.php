<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. Every seeder here is idempotent, so this
     * can be re-run on an existing install without duplicating data.
     */
    public function run(): void
    {
        $this->call([
            // Access control and reference data first — later seeders depend on them.
            PermissionSeeder::class,
            SettingSeeder::class,
            AdminSeeder::class,
            MasterDataSeeder::class,

            // Website CMS content.
            ServiceSeeder::class,
            CompanySeeder::class,
            PortfolioSeeder::class,
            ClientSeeder::class,
            ReviewSeeder::class,
            FaqSeeder::class,
            TeamMemberSeeder::class,
            ContentBlockSeeder::class,
            MenuSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
