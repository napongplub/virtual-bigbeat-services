<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call([
            Users::class,
            NatureOfBusiness::class,
            MainCategory::class,
            Subcategory::class,
            FindAboutSeeder::class,
            JobFunctionSeeder::class,
            JobLevelSeeder::class,
            NumberOfEmployeesSeeder::class,
            ReasonForAttendingSeeder::class,
            CountriesSeeder::class,
            RoleProcessSeeder::class,
            ExhibitorListSeeder::class
        ]);
    }
}
