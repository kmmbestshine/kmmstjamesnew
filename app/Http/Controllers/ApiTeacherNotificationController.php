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

class ApiTeacherNotificationController extends Controller
{
    protected $user;

    function __construct() {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception$e) {
            
        }
    }

    public function getClassID($platform,$teacher_id)
    {
        if(!$teacher_id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);

        $teacherObj = \DB::table('teacher')->where('id', $teacher_id)->first();
        return \api::success(['data' => $teacherObj->class]);
    }    

    public function getNotificationCount($platform,$class_id,$teacher_id)
    {   
        if(!$teacher_id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);

        $particularUserNotificationCount = 0;
        $finalNotificationCount = 0;
        $totalNotificationsCount = \DB::table('teachers_notification')->where('class_id', $class_id)->count();
        $notifications = \DB::table('teachers_notification')->where('class_id', $class_id)->get();
        foreach ($notifications as $notification) {
            $explodeArray = explode(",", $notification->seen_users_id);
            if(in_array($teacher_id,$explodeArray)){            
                $particularUserNotificationCount++;
            }       
        }
        $finalNotificationCount = $totalNotificationsCount - $particularUserNotificationCount;        
        return \api::success(['data' => $finalNotificationCount]);        
    }

    public function getNotification($platform,$class_id)
    {
        if(!$class_id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);

        $notifications = \DB::table('teachers_notification')->where('class_id', $class_id)->get();
        return \api::success(['data' => $notifications]);
    }

    public function getNotificationDetails($platform,$id,$teacher_id)
    {
        if(!$id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        
        $notificationDetails = \DB::table('teachers_notification')->where('id', $id)->first();

        $explodeArray = explode(",", $notificationDetails->seen_users_id);
        if(!in_array($teacher_id,$explodeArray)){            
            array_push($explodeArray, $teacher_id);
        }
        $implodeArray = implode(",", $explodeArray);        
        \DB::table('teachers_notification')->where('id', $id)->update(['seen_users_id' => $implodeArray]);        
        return \api::success(['data' => $notificationDetails]);
    }
}