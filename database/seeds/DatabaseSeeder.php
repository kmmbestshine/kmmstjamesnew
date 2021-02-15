<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	/*updated 2-6-2018 by priya*/

       /*Update Session_id in class*/
     //   DB::table('class')->where('school_id','=',1)->update(['session_id' =>1]);
      //  DB::table('class')->where('school_id','=',2)->update(['session_id' =>2]);

        /*Update Session_id in class*/
      //  DB::table('section')->where('school_id','=',1)->update(['session_id' =>1]);
      //  DB::table('section')->where('school_id','=',2)->update(['session_id' =>2]);

        /*Update Session_id in class*/
      //  DB::table('teacher')->where('school_id','=',1)->update(['session_id' =>1]);
      //  DB::table('teacher')->where('school_id','=',2)->update(['session_id' =>2]);
		
		 DB::table('users')->insert(
		[
		'id'=> 1,
		'type'=>'admin',
		'username'=>'admin',
	    'password'=>'$2y$10$nkgimqNPHySbMFPHvx7LluSqt.V9124XnjW4CtPWN1k8Wnunj5Ifi',
		'remember_token'=>'OBOHLBRNdGQVluOtUUp8Wsnl6fK30V5CbuzI4iwy5PvYjewo9K'
		]);
		
	    DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'EXPENDITURE',
		'Basic'=>0,
		'Standard'=>0,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'FURNITURE',
		'Basic'=>0,
		'Standard'=>0,
		'Premium'=>1,
		]); 
		
		//delete version table records
        // DB::table('version')->truncate();
        /*DB::table('version')->insert([
         'version' =>'1.0.3' 
		 ]);*/
		 
		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'DASHBOARD',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'MASTER',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'EMPLOYEE',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'STUDENT',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'ATTENDANCE',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'NOTIFICATION',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'TIMETABLE',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'USERROLE',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=>'DATA MANAGER',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'EXPORT MANAGER',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'HOMEWORK',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'POST',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'GALLERY',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'KNOWLEDGE BANK',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'VOICE SMS',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'TRANSPORT',
		'Basic'=>0,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'FEES',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'RESULT',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'TEXT SMS',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'REPORT',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'UPGRADE',
		'Basic'=>0,
		'Standard'=>0,
		'Premium'=>0,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'LIBRARY',
		'Basic'=>0,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'PAYMENT GATEWAY',
		'Basic'=>0,
		'Standard'=>0,
		'Premium'=>0,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'PAYROLL',
		'Basic'=>0,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'SYLLABUS',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'BACKUP',
		'Basic'=>0,
		'Standard'=>0,
		'Premium'=>0,
		]);

		DB::table('schooluser_plan')->insert(
		[
		'Modules'=> 'TEACHER WEB APP',
		'Basic'=>1,
		'Standard'=>1,
		'Premium'=>1,
		]);
		
		 
		  DB::table('month')->insert([
                ['id' => '1','month' => 'January'],
                ['id' => '2','month' => 'February'],
                ['id' => '3','month' => 'March'],
                ['id' => '4','month' => 'April'],
                ['id' => '5','month' => 'May'],
                ['id' => '6','month' => 'June'],
                ['id' => '7','month' => 'July'],
                ['id' => '8','month' => 'August'],
                ['id' => '9','month' => 'September'],
                ['id' => '10','month' => 'October'],
                ['id' => '11','month' => 'November'],
                ['id' => '12','month' => 'December']
            ]); 
    }
}