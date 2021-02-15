<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Validator, Redirect, Auth, api;

use App\addClass;
use App\Amount;
use App\Attendance;
use App\BloodGroup;
use App\Bus;
use App\BusStop;
use App\Caste;
use App\Driver;
use App\Employee;
use App\Events;
use App\Exam;
use App\Fee;
use App\Feedback;
use App\Gallery;
use App\Holiday;
use App\Homework;
use App\Leave;
use App\Result;
use App\StuParent;
use App\Religion;
use App\School;
use App\Section;
use App\Session;
use App\Splash;
use App\Staff;
use App\Students;
use App\Subject; 
use App\TimeTable;
use App\User;


class ApiDriverController extends Controller
{
     protected $user; 
    //protected $teacher;

    function __construct(){
        try{
            $this->user = JWTAuth::parseToken()->authenticate();
            //$this->teacher = Employee::where('user_id', \Auth::user()->id)->first();
            //$this->homecontroller = HomeController;
        }
        catch(\Exception$e){}
    }
    public function  postgeolocation()
    {
        $request = \Request::all();
        //print_r($request);
        $drivers=\DB::table('driver_track')->insert(['school_id' => $request['userdetail']['school_id'],'bus_id' => $request['userdetail']['bus_id'],'driver_id' => $request['userdetail']['id'],'latitude'=>$request['lat'],'longitude'=>$request['lang'],'driver_mobile'=>$request['userdetail']['mobile'],'driver_name'=>$request['userdetail']['name']]);
    }
    public  function parentDrivermaplocation()
    {
        $request = \Request::all();
         //print_r($request);
         //whereDate('created_at', '=', date('Y-m-d'));
         $drivertrack = \DB::table('driver_track')->where('bus_id','=', $request['userbus'])->whereDate('created_at', '=', date('Y-m-d'))->orderBy('id', 'desc')->first();
         if($drivertrack != ''){
           return \api::success(['data' => $drivertrack]);  
         }
         else
         {
             $drivertrack ="fail";
             return \api::success(['data' => $drivertrack]);
         }
    }
}
