<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){  
         DB::table('schooluser_plan')->insert([
        ['Modules' => 'EXPENDITURE',
            'Basic' => '0',
            'Standard' => '0',
            'Premium' => '1',
            'created_at' => '',
            'updated_at'=>''
        ],
        ['Modules' => 'FURNITURE',
            'Basic' => '0',
            'Standard' => '0',
            'Premium' => '1',
            'created_at' => '',
            'updated_at'=>''
        ]]); 
    
    }
}
