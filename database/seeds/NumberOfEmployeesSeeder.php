<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NumberOfEmployeesSeeder extends Seeder {
    private $items = [
        '1 to 20',
        '21 to 50',
        '51 to 100',
        '101 to 250',
        '501 to 1,000',
        '1,001 to 5,000',
        '5,001 to 10,000',
        'More than 10,000',
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            DB::table('number_of_employees')->insert([
                'name' => $value,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
