<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Subcategory extends Seeder {
    private $items = [
        [
            'name'      => 'Facilities Management',
            'main_cate' => 1,
        ],
        [
            'name'      => 'Cleaning',
            'main_cate' => 1,
        ],
        [
            'name'      => 'Security',
            'main_cate' => 1,
        ],
        [
            'name'      => 'HVAC',
            'main_cate' => 1,
        ],
        [
            'name'      => 'Building Contractors',
            'main_cate' => 1,
        ],
        [
            'name'      => 'Formwork & Scaffolding',
            'main_cate' => 1,
        ],
        [
            'name'      => 'Fire & Safety',
            'main_cate' => 1,
        ],
        [
            'name'      => 'Construction Machinery',
            'main_cate' => 2,
        ],
        [
            'name'      => 'Concrete Sector',
            'main_cate' => 2,
        ],
        [
            'name'      => 'Machine Tools & Equipment',
            'main_cate' => 2,
        ],
        [
            'name'      => 'Road, Mineral & Foundation',
            'main_cate' => 2,
        ],
        [
            'name'      => 'Consultancy',
            'main_cate' => 2,
        ],
        [
            'name'      => 'Construction Services',
            'main_cate' => 2,
        ],
        [
            'name'      => 'BIM',
            'main_cate' => 3,
        ],
        [
            'name'      => 'AR & VR',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Smart Building',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Smart Parking',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Telematics',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Project Management',
            'main_cate' => 3,
        ],

        [
            'name'      => 'Cost Control',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Fleet Logistic Management',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Maintenance System for Machinery',
            'main_cate' => 3,
        ],
        [
            'name'      => 'Stock /Inventory Tracking',
            'main_cate' => 3,
        ],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            DB::table('sub_category')->insert([
                'name'      => $value['name'],
                'main_cate' => $value['main_cate'],
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }

    }
}
