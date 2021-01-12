<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FindAboutSeeder extends Seeder {
    private $items = [
        [
            'name_en' => 'Email',
            'name_th' => 'อีเมล',
        ],
        [
            'name_en' => 'Facebook',
            'name_th' => 'เฟสบุ๊ก',
        ],
        [
            'name_en' => 'Twitter',
            'name_th' => 'ทวิตเตอร์',
        ],
        [
            'name_en' => 'Linkedin',
            'name_th' => 'ลิงค์อิน',
        ],
        [
            'name_en' => 'Google',
            'name_th' => 'กูเกิล',
        ],
        [
            'name_en' => 'Website',
            'name_th' => 'เว็บไซต์',
        ],
        [
            'name_en' => 'Online News',
            'name_th' => 'ข่าวออนไลน์',
        ],
        [
            'name_en' => 'Magazine Advertisement',
            'name_th' => 'โฆษณาจากนิตยสาร',
        ],
        [
            'name_en' => 'Press Release',
            'name_th' => 'ข่าวประชาสัมพันธ์',
        ],
        [
            'name_en' => 'Colleague',
            'name_th' => 'เพื่อนร่วมงาน',
        ],
        [
            'name_en' => 'Invitation from Organizer',
            'name_th' => 'การเรียนเชิญจากผู้จัดงาน',
        ],
        [
            'name_en' => 'Invitation from Association and Organization',
            'name_th' => ' การเรียนเชิญจากหน่วยงานและสมาคม',
        ],
        [
            'name_en' => 'Others (Please specify)',
            'name_th' => 'อื่นๆ (โปรดระบุ)',
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            DB::table('find_about_bct')->insert([
                'name_en'    => $value["name_en"],
                'name_th'    => $value["name_th"],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
