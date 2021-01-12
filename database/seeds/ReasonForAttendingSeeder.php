<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReasonForAttendingSeeder extends Seeder {
    private $items = [
        [
            'name_en' => 'To buy / source new products / services',
            'name_th' => 'สรรหาและซื้อสินค้าบริการใหม่',
        ],
        [
            'name_en' => 'To conclude deals / sign orders with suppliers',
            'name_th' => 'เพื่อสรุปข้อเสนอทางการค้า / ลงชื่อสั่งซื้อกับซัพพลายเออร์',
        ],
        [
            'name_en' => 'To see the latest research and developments',
            'name_th' => 'เพื่อสำรวจ, ตลาดวิจัยและพัฒนา',
        ],
        [
            'name_en' => 'To network with peers / other visitors',
            'name_th' => 'เพื่อสร้างเครือข่ายทางธุรกิจกับผู้ร่วมอุตสาหกรรมฯ',
        ],
        [
            'name_en' => 'To keep up-to-date with industry trends',
            'name_th' => 'เพื่ออัพเดทแนวโน้มของอุตสาหกรรมฯ',
        ],
        [
            'name_en' => 'To learn (more) about the benefits of specific products / services',
            'name_th' => 'เพื่อเรียนรู้เกี่ยวกับสินค้าและบริการใหม่ๆ',
        ],
        [
            'name_en' => 'To sell / promote products or services to exhibitors',
            'name_th' => 'เพื่อขายและโปรโมทสินค้าไปยังผู้เข้าร่วมแสดงสินค้า',
        ],
        [
            'name_en' => 'To consider exhibiting in future years',
            'name_th' => 'เยี่ยมชมงานเพื่อการตัดสินใจเข้าร่วมแสดงสินค้าในปีหน้า',
        ],
        [
            'name_en' => 'Others (please, specify)',
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
            DB::table('reason_for_attending')->insert([
                'name_en'    => $value["name_en"],
                'name_th'    => $value["name_th"],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
