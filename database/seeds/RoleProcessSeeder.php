<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleProcessSeeder extends Seeder
{
    private $items = [
        [
            'name_en' => 'Decision Maker',
            'name_th' => 'ผู้มีอำนาจตัดสินใจ',
        ],
        [
            'name_en' => 'Co-Decision Maker',
            'name_th' => 'ผู้ร่วมตัดสินใจ',
        ],
        [
            'name_en' => 'Advisor Function',
            'name_th' => 'ผู้ให้คำแนะนำ',
        ],     
        [
            'name_en' => 'Not Involved',
            'name_th' => 'ไม่มีส่วนร่วมในการตัดสินใจ',
        ]
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->items as $key => $value) {
            DB::table('role_process')->insert([
                'name_en'    => $value["name_en"],
                'name_th'    => $value["name_th"],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
