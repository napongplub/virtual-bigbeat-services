<?php

use Illuminate\Database\Seeder;

class PrefixNameSeeder extends Seeder
{
    private $items = [
        [
            "name_en" => 'Mr.',
            'name_th' => 'นาย'
        ],
        [
            'name_en' => 'Mrs.',
            'name_th' => 'นาง',
        ],
        [
            'name_en' => 'MS.',
            'name_th' => 'นางสาว'
        ],
        [
            'name_en' => 'Doctor',
            'name_th' => ' ดร.',
        ],
   
       
        
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            DB::table('prefix_name')->insert([
                'name_en'       => $value['name_en'],
                'name_th'       => $value['name_th'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

    }
}
