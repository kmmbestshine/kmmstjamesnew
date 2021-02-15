<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

use Validator, Redirect, Auth, api;

use App\addClass;
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

class ParentController extends Controller
{	
	protected $user;
    
    function __construct(){
        try{
            $this->user= JWTAuth::parseToken()->authenticate();
        }
        catch(\Exception$e){}
    }

    public function getStudentsByParent(StuParent $parent, $platform)
    {
        return $parent->doGetStudentsByParent($this->user, $platform);
    }

    public function postLeaveByParent(Request $request, Leave $leave, $flag)
    {
        $userError = ['student_id' => 'Student Id', 'leave_from' => 'Leave From Date in dd-mm-yyyy', 'leave_to' => 'Leave To Date in dd-mm-yyyy', 'request_by' => 'Request By', 'status' => 'Status'];
        $validator = \Validator::make($request->all(), [
                'student_id'=>'required',
                'leave_from'=>'required|date',
                'leave_to'=>'required|date',
                'request_by'=>'required',
                'status' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $leave->doPostLeaveByParent($this->user, $request);
    }
    
    public function getHomeworkByParent(StuParent $parent, $platform, $id, $date)
    {
    	//return $parent->doGetHomeworkByParent($this->user, $platform, $id, $date);
    	if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
		{
		    $sessionDate = $date.'-'.date('Y');
            $stu = Students::where('id', $id)->first();
		    if(!$stu)
		    	return \api::notValid(['errorMsg' => 'Student Id is Invalid']);
            // dd($stu);
	    	    $homework = \DB::table('homework')->where('homework.class_id', $stu->class_id)
	    					->where('homework.section_id', $stu->section_id)
	    					->where('homework.date', 'LIKE', '%'.$sessionDate.'%')
	    					->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
	    					->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
	    					->select('homework.id', 'homework.description', 'homework.image', 'subject.subject', 'homework.date', 'teacher.name as teacherName')
	    					->get();
	    	    $data = array(
				    'name' => $stu->name,
				    'roll_no' => $stu->roll_no,
	    	    	'homework' => $homework
	    	    		);
	        if(!$homework)
	           	return \api::notValid(['data' => 'No Rows Found!!!']);
		    return \api(['data' => $data]);
		}
		else
		{
		     return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
		}
    }

    public function getAttendanceByParent(Attendance $attendance, $platform, $id, $date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
        {
            $logged_parent = \DB::table('parent')->where('user_id', $this->user->id)->first();
            if(!$logged_parent)
                return \api::notValid(['errorMsg' => 'Parent is Invalid']); 
            $student = \DB::table('student')->where('id', $id)->where('parent_id', $logged_parent->id)->first();
            if(!$student)
                return \api::notValid(['errorMsg' => 'Invalid Parameter']);
            $sessionDate = $date.'-'.date('Y');
            $atten = Attendance::where('student_id', $student->id)->where('date', $sessionDate)
                    ->leftJoin('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                    ->select('attendance.id', 'attendance.attendance', 'attendance.remarks', 'attendance.date', 'teacher.name as teacherName')
                    ->orderBy('attendance.id', 'DESC')
                    ->get();
            if(!$atten)
                return \api::notValid(['errorMsg' => 'Attendance Not Found']);
            return \api(['data' => $atten]);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
        }
    }
    
    public function getFeedbackByParent($platform, $id)
    {
    	$student = \DB::table('student')->where('id', $id)->first();
    	if(!$student)
                return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    	$feedbacks = Feedback::where('student_id', $student->id)->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')->orderBy('feedback.id', 'DESC')
    			->select('feedback.id', 'teacher.name as teacherName', 'feedback.feedback', 'feedback.date')
    			->get();
    	return \api::success(['data' => $feedbacks]);
    }
    
    public function getTimeTableByParent($platform, $id)
    {
    	$student = \DB::table('student')->where('id', $id)->first();
    	if(!$student)
                return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    	$timeTables = TimeTable::where('time-table.class_id', $student->class_id)->where('time-table.section_id', $student->section_id)
    			->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
    			->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
    			->select
    			(
    				'time-table.id',
    				'subject.subject',
    				'teacher.name as teacherName',
    				'time-table.period',
    				'time-table.start_time',
    				'time-table.end_time',
    				'time-table.day'
    			)
    			->orderBy('time-table.id', 'ASC')
    			->get();
    	return \api::success(['data' => $timeTables]);
    }
}