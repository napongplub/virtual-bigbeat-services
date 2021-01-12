<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobLevelSeeder extends Seeder {
    private $items = [
        [
            'name_en' => 'C-level (CEO, CIO,CTO,COO, etc)',
            'name_th' => 'ผู้บริหารระดับสูง',
        ],
        [
            'name_en' => 'Consultant',
            'name_th' => 'ที่ปรึกษา',
        ],
        [
            'name_en' => 'Department Head (HOD) ',
            'name_th' => 'หัวหน้าแผนก',
        ],
        [
            'name_en' => 'Owner',
            'name_th' => 'เจ้าของกิจการ',
        ],
        [
            'name_en' => 'Senior Management (MD,ED, etc) ',
            'name_th' => 'ฝ่ายบริหารระดับอาวุโส',
        ],
     
        [
            'name_en' => 'Senior Manager, Manager',
            'name_th' => 'ผู้จัดการอาวุโส ผู้จัดการ',
        ],
        [
            'name_en' => 'Senior Executive, Executive ',
            'name_th' => 'เจ้าหน้าที่อาวุโส เจ้าหน้าที่ทั่วไป',
        ],
        [
            'name_en' => 'Others Please specify',
            'name_th' => 'อื่นๆ โปรดระบุ',
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            DB::table('job_level')->insert([
                'name_en'    => $value["name_en"],
                'name_th'    => $value["name_th"],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
