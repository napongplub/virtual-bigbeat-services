<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainCategory extends Seeder {
    private $items = [
        [
            "name_en" => 'Artificial Intelligence',
            'name_th' => 'ปัญญาประดิษฐ์'
        ],
        [
            'name_en' => 'Cyber Security',
            'name_th' => 'ความมั่นคงปลอดภัยทางไซเบอร์',
        ],
        [
            'name_en' => 'Data & Cloud',
            'name_th' => 'ดาต้าและคลาวด์'
        ],
        [
            'name_en' => 'E-commerce & Digital Marketing',
            'name_th' => 'อีคอมเมิร์ซและการตลาดดิจิทัล',
        ],
        [
            'name_en' => 'Enterprise Software',
            'name_th' => 'ซอฟต์แวร์เพื่อผู้ประกอบการ',
        ],
        [
            'name_en' => 'Smart Solutions & IoT',
            'name_th' => 'สมาร์ทโซลูชั่นส์และไอโอที',
        ],
        [
            'name_en' => '5G & Network',
            'name_th' => '5G และเครือข่าย',
        ],
       
        
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            DB::table('main_category')->insert([
                'name_en'       => $value['name_en'],
                'name_th'       => $value['name_th'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

    }
}
