<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobFunctionSeeder extends Seeder {
    private $items = [
        [
            'name_en' => 'Academia / Educator',
            'name_th' => 'สถาบันการศึกษา/นักศึกษา',
        ],
        [
            'name_en' => 'Brand Management',
            'name_th' => 'ฝ่ายบริหารจัดการแบรนด์',
        ],
        [
            'name_en' => 'Digital Media',
            'name_th' => 'ฝ่ายการสื่อดิจิทัล',
        ],
        [
            'name_en' => 'Entrepreneur/Startup/SMEs',
            'name_th' => 'ผู้ประกอบการสตาร์ทอัพและเอสเอ็มอี',
        ],
        [
            'name_en' => 'Finance / Admin / Human Resource',
            'name_th' => 'ฝ่ายการเงิน/ ธุรการ/ บุคคล',
        ],
        [
            'name_en' => 'Investor/Venture Capital',
            'name_th' => 'นักลงทุน/ หน่วยงานเกี่ยวกับการลงทุน',
        ],
        [
            'name_en' => 'Marketing, Business Development & Sales Department',
            'name_th' => 'พนักงานฝ่ายการตลาด พัฒนาธุรกิจและฝ่ายขาย',
        ],
        [
            'name_en' => 'MIS / IT / Network',
            'name_th' => 'ฝ่ายจัดการข้อมูลสารสนเทศ/เทคโนโลยีสารสนเทศและฝ่ายเครือข่าย',
        ],
        [
            'name_en' => 'Operation',
            'name_th' => 'ฝ่ายการดำเนินงาน',
        ],
        [
            'name_en' => 'Product Development / Innovation',
            'name_th' => 'ฝ่ายพัฒนาสินค้าและนวัตกรรม',
        ],
        [
            'name_en' => 'Programming / Content Development',
            'name_th' => 'โปรแกรมเมอร์/ ฝ่ายพัฒนาเนื้อหา',
        ],
        [
            'name_en' => 'Purchasing / Procurement / Buying',
            'name_th' => 'ฝ่ายจัดซื้อ',
        ],
        [
            'name_en' => 'Research & Development / Consultancy / Legal',
            'name_th' => 'งานวิจัยและพัฒนา/ให้คำแนะนำและปรึกษา/',
        ],
        [
            'name_en' => 'Technical/Engineering',
            'name_th' => 'ฝ่ายเทคนิคและวิศวกรรม',
        ],
        [
            'name_en' => 'Testing / Quality Assurance',
            'name_th' => 'ฝ่ายทดสอบและประกันคุณภาพ',
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
            DB::table('job_function')->insert([
                'name_en'    => $value["name_en"],
                'name_th'    => $value["name_th"],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
