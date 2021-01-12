<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class NatureOfBusiness extends Seeder {
    private $items = [
        [
            'name_en' => 'Automotive',
            'name_th' => 'อุตสาหกรรมยานยนต์',
        ],
        [
            'name_en' => 'Agricultural',
            'name_th' => 'อุตสาหกรรมการเกษตร',
        ],
        [
            'name_en' => 'Banking & Finance',
            'name_th' => 'ธนาคารและการเงิน',
        ],
        [
            'name_en' => 'Beauty & Fashion',
            'name_th' => 'ความงามและแฟชั่น',
        ],
        [
            'name_en' => 'eCommerce',
            'name_th' => 'อีคอมเมิร์ซ',
        ],
        [
            'name_en' => 'Food & Beverage',
            'name_th' => 'อาหารและเครื่องดื่ม',
        ],
        [
            'name_en' => 'Energy',
            'name_th' => 'อุตสาหกรรมพลังงาน',
        ],
        [
            'name_en' => 'Engineering',
            'name_th' => 'วิศวกรรม',
        ],
        [
            'name_en' => 'Environment',
            'name_th' => 'สิ่งแวดล้อม',
        ],
        [
            'name_en' => 'Government',
            'name_th' => 'หน่วยงานราชการ',
        ],
        [
            'name_en' => 'Hospitality',
            'name_th' => 'อุตสาหกรรมการบริการ',
        ],
        [
            'name_en' => 'Non-Profit Organizations',
            'name_th' => 'หน่วยงานไม่แสวงหาผลกำไร',
        ],
        [
            'name_en' => 'Lighting',
            'name_th' => 'ไฟฟ้าและแสดงสว่าง',
        ],  [
            'name_en' => 'Manufacturing',
            'name_th' => 'อุตสาหกรรมการผลิต',
        ],
        [
            'name_en' => 'Media & Entertainment',
            'name_th' => 'สื่อและความบันเทิง',
        ],
        [
            'name_en' => 'Medical & Healthcare',
            'name_th' => ' การแพทย์และผลิตภัณฑ์เพื่อสุขภาพ',
        ],
        [
            'name_en' => 'Public administration',
            'name_th' => 'หน่วยงานบริหารราชการแผ่นดิน',
        ],
        [
            'name_en' => 'Retail',
            'name_th' => 'การค้าปลีก',
        ],
        [
            'name_en' => 'Research & Development Services',
            'name_th' => 'ผู้ให้บริการวิจัยและพัฒนา',
        ],
        [
            'name_en' => 'State Enterprise',
            'name_th' => 'รัฐวิสาหกิจ',
        ],
        [
            'name_en' => 'Technology & Digital',
            'name_th' => 'เทคโนโลยีและดิจิทัล',
        ],
        [
            'name_en' => 'Warehouse, Fulfillment & Logistics',
            'name_th' => 'คลังสินค้า ฟูลฟิลล์เม้นท์และการขนส่งโลจิสติกส์',
        ],
        [
            'name_en' => 'Technology & Digital',
            'name_th' => 'เทคโนโลยีและดิจิทัล',
        ],
        [
            'name_en' => 'Others Please specify ',
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
            DB::table('nature_of_business')->insert([
                'name_en' => $value["name_en"],
                'name_th' => $value["name_th"],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

    }
}
