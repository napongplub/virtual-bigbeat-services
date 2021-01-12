<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ExhibitorListSeeder extends Seeder {
    private $items = [
        [
            'company'       => 'Pung That Fea Corporation',
        ],
        [
            'company'       => 'Chai Play Equipment',
        ],
        [
            'company'       => 'PPH Plus Fea Co. Ltd',
           
        ],
        [
            'company'       => 'GGtrend Group',
            
        ],
        [
            'company'       => 'FTP Custom Tailor Chang Rai',
            
        ],
        [
            'company'       => 'Passion Hometex Co. Ltd.',
            
        ],
        [
            'company'       => 'Dragon Asian Global 2010 Co. ltd',
           
        ],
        [
            'company'       => 'The Finance',
            
        ],
        [
            'company'       => 'Nont Agriculture',
            
        ],
        [
            'company'       => 'Advanced Information PCL.',
           
        ],
        [
            'company'       => 'Jerli Bucker PCL.',
  
        ],
        [
            'company'       => 'PANBU PCL.',
         
        ],

        [
            'company'       => 'Nont Agricultural',
     
        ],
        [
            'company'       => 'T&M Flavor Masker',
         
        ], [
            'company'       => 'Nopphon Ecotourism',
         
        ],
        [
            'company'       => 'test company 15',
        
        ],
        [
            'company'       => 'Saint Conan Store',
          
        ],
        [
            'company'       => 'Siam Construction Pulic',
          
        ],
        [
            'company'       => 'Miner Company',
           
        ],
        [
            'company'       => 'Ignite DNS',
       
        ],
        [
            'company'       => 'Ribova',
        
        ],
        [
            'company'       => 'Evolutrum',
           
        ],
        [
            'company'       => 'Ticky Top',
          
        ],
        [
            'company'       => 'Landscape PCL.',
         
        ],
        [
            'company'       => 'Eventido',
          
        ],
        [
            'company'       => 'SPNA',
        
        ],
        [
            'company'       => 'Cartesian',
          
        ],
        [
            'company'       => 'Cylindrical',
           
        ],

        [
            'company'       => 'SCARA',
         

        ],
        [
            'company'       => '6-Axis',
          
        ],
        [
            'company'       => 'Dynamic windy',
         
        ],

    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        foreach ($this->items as $key => $value) {
            $name  = 'test name ' . ($key + 1);
            $email = 'test' . ($key + 1) . '@gmail.com';
            DB::table('exhibitor_list')->insert([
                'name'          => $name,
                'company'       => $value["company"],
                'email'         => $email,
                'password'      => Hash::make($name),
                'created_at'    => date("Y-m-d H:i:s"),
                'updated_at'    => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
