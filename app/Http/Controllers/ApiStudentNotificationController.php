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

class ApiStudentNotificationController extends Controller
{
    protected $user;
    protected $student;

    function __construct() {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            $this->student = Students::where('user_id', \Auth::user()->id)->first();
        } catch (\Exception$e) {
            
        }
    }

    public function getClassID($platform,$student_id)
    {
        if(!$student_id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);

        $stuObj = \DB::table('student')->where('id', $student_id)->first();
        return \api::success(['data' => $stuObj->class_id]);
    }

    public function getNotificationCount($platform,$class_id,$student_id)
    {
        if(!$class_id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $particularUserNotificationCount = 0;
        $finalNotificationCount = 0;
        $totalNotificationsCount = \DB::table('students_notification')->where('class_id', $class_id)->count();
        $notifications = \DB::table('students_notification')->where('class_id', $class_id)->get();
        foreach ($notifications as $notification) {
            $explodeArray = explode(",", $notification->seen_users_id);
            if(in_array($student_id,$explodeArray)){            
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

        $notifications = \DB::table('students_notification')->where('class_id', $class_id)->get();
        return \api::success(['data' => $notifications]);
    }

    public function getNotificationDetails($platform,$id,$student_id)
    {
        if(!$id)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);

        $notificationDetails = \DB::table('students_notification')->where('id', $id)->first();
        $explodeArray = explode(",", $notificationDetails->seen_users_id);
        if(!in_array($student_id,$explodeArray)){            
            array_push($explodeArray, $student_id);
        }
        $implodeArray = implode(",", $explodeArray);
        \DB::table('students_notification')->where('id', $id)->update(['seen_users_id' => $implodeArray]);
        return \api::success(['data' => $notificationDetails]);
    }
}