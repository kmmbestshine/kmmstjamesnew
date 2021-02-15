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
use Carbon\Carbon;

class PrincipalController extends Controller
{
    protected $user;

    private $active_session;//updated 2-6-2018

    public function __construct()
    {
        /** @ Updated 2-6-2018 by priya @ **/
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();

            if(Auth::check())
        {
            $studentCount = Students::where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id) //2-6-2018
                ->count();
            $employeeCount = Employee::where('school_id', \Auth::user()->school_id)->count();
            $this->user = \Auth::user();

            $roler = [];
            if(Auth::user()->type == 'user_role')
            {
                $roleuser = \DB::table('user_role')->where('role_id', Auth::user()->id)->get();
                // dd($roleuser);
                
                foreach($roleuser as $role)
                {
                    array_push($roler, $role->value);
                }
            }

            view()->share(compact('studentCount', 'employeeCount', 'roler'));
        }
    }

    public function login()
    {
    	return \Redirect::route('login');
    }

    public function dashboard()
    {
        if(Auth::check())
        {
            return view('users.index');
        }
        else
        {
            return \Redirect::route('login');
        }
    }

    public function postHomeWork(Request $request, Homework $hw, $flag)
    {
        $userError = ['class_id'=>'Class', 'section_id'=>'Section', 'subject_id'=>'Subject', 'description'=>'Description', 'image'=>'Image', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($request->all(), [
            'class_id'=>'required',
            'section_id'=>'required',
            'subject_id'=>'required',
            'description'=>'required',
            'image'=>'required',
            'date' => 'required|date_format:d-m-Y'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $hw->saveHomework($flag, $this->user, $request);
    }
    
    public function updateHomeWork(Request $request, Homework $hw, $platform)
    {
    	$userError = ['id' => 'Homework Id', 'class_id'=>'Class', 'section_id'=>'Section', 'subject_id'=>'Subject', 'description'=>'Description', 'image'=>'Image', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'class_id'=>'required',
            'section_id'=>'required',
            'subject_id'=>'required',
            'description'=>'required',
            'image'=>'required',
            'date' => 'required|date_format:d-m-Y'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $hw->doUpdateHomework($platform, $this->user, $request);
    }

    public function postSplash(Request $request, Splash $splash)
    {
    	$userError = ['splash'=>'Splash Base 64 String'];
        $validator = \Validator::make($request->all(), [
            'splash'=>'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $splash->doPostSplash($request, $this->user); 
    }
    
    public function getSplash(Splash $splash)
    {
    	return $splash->doGetSplash($this->user);
    }

    public function uploadImage(Request $request, Employee $emp)
    {
        $validator = \Validator::make($request->only('image'), [
            'image' => 'required|max:8000|image'
        ]);

        if ($validator->fails())
            return api()->notValid(['message'=>$validator->errors()->first()]);
        return $emp->imageUpload($request, $this->user);
    }

    public function postEmployee(Request $request, Employee $emp, $platform)
    {
        $userError = ['type' => 'Employee Type', 'name' => 'Name', 'mobile' => 'Contact No', 'email' => 'Email', 'class' => 'Class', 'section' => 'Section', 'image' => 'Image'];
        $validator = \Validator::make($request->all(), [
                'type' => 'required',
                'name' => 'required',
                'mobile' => 'required',
                'email' => 'required',
                'class' => 'required',
                'section' => 'required' 
            ], $userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $emp->doPostEmployee($request, $this->user, $platform);
    }

    public function getEmployee(Employee $emp)
    {
        return $emp->doGetEmployee($this->user);
    }

    public function deleteEmployee(Employee $emp, $flag, $id)
    {
        return $emp->doDeleteEmployee($id);
    }

    public function updateEmployee(Request $request, Employee $emp, $platform, $id)
    {
        return $emp->doUpdateEmployee($request, $this->user, $platform, $id);
    }

    public function importEmployee(Employee $emp, $platform)
    {
        return $emp->doImportEmployee($this->user, $platform);
    }

    public function postStudent(Request $request, Students $student)
    {
        $userError = [
                'session_id' => 'Session',
                'registration_no' => 'Registration No',
                'class' => 'Class',
                'section' => 'Section',
                'bus_id' => 'Bus',
                'roll_no' => 'Roll No',
                'date_of_admission' => 'Date Of Admission and format is dd-mm-yyyy',
                'date_of_joining' => 'Date Of Joining and format is dd-mm-yyyy',
                'name' => 'Student Name',
                'gender' => 'Gender',
                'caste' => 'Caste',
                'dob' => 'Date Of Birth format is dd-mm-yyyy',
                'blood_group' => 'Blood Group',
                'religion' => 'Religion',
                'contact_no' => 'Contact Number',
                'email' => 'Email',
                'nationality' => 'Nationality',
                'state' => 'State',
                'address' => 'Address',
                'city' => 'City',
                'avatar' => 'Avatar',
                'pin_code' => 'Pin Code',
                'previous_school' => 'Previous School',
                'father_name' => 'Father Name',
                'mother_name' => 'Mother Name',
                'parent_image' => 'Parent Image',
                'father_occupation' => 'Father Occupation',
                'mother_occupation' => 'Mother Occupation',
                'parent_contact_no' => 'Parent Contact Number',
                'parent_email' => 'Parent Email'
            ];
        $validator = \Validator::make($request->all(), [
                'session_id' => 'required|numeric',
                'registration_no' => 'required',
                'class' => 'required|numeric',
                'section' =>'required|numeric',
                'roll_no' => 'required|numeric',
                'date_of_admission' => 'required|date_format:d-m-Y',
                'date_of_joining' => 'required|date_format:d-m-Y',
                'name' => 'required',
                'gender' => 'required',
                'caste' => 'required|numeric',
                'dob' => 'required|date_format:d-m-Y',
                'blood_group' => 'required|numeric',
                'religion' => 'required',
                'state' => 'required',
                'city' => 'required',
                'pin_code' => 'required|numeric',
                'father_name' => 'required',
                'mother_name' => 'required',
                'parent_contact_no' => 'required|numeric',
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return Redirect::back()->withErrors($validator);
        return $student->doPostStudent($request, $this->user, $platform);
    }

    public function getStudentSection(Students $student, $flag, $class, $section)
    {
        return $student->getStudents($class, $section);
    }

    public function updateStudent(Students $student, Request $request, $platform, $id)
    {
        return $student->doUpdateStudent($request, $this->user, $platform, $id);   
    }

    

    public function postAttendance(Request $request, Attendance $att, $flag)
    {
        $userError = ['class_id' => 'Class', 'section_id' => 'Section', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($request->all(), [
            'class_id'=>'required',
            'section_id'=>'required',
            'attendance' => 'required|json',
            'date' => 'required|date_format:d-m-Y'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $att->saveAttendance($request, $this->user);
    }

    public function feedBack(Request $request, $flag, Feedback $feed)
    {
        $userError = ["student_id"=>'Student', 'feedback'=>'Feedback', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($request->all(), [
                'student_id'=>'required',
                'feedback'=>'required',
                'date' => 'required|date_format:d-m-Y'
            ], $userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $feed->saveFeedback($request, $this->user);
    }

    public function getFeeStructure(Fee $fee, $flag, $class)
    {
        return $fee->feeGet($class, $this->user);
    }

    public function leaveRequest(Request $request, Leave $leave, $flag)
    {
        $userError = ['student_id' => 'Student Id', 'leave_from' => 'Leave From Date in dd-mm-yyyy', 'leave_to' => 'Leave To Date in dd-mm-yyyy', 'request_by' => 'Request By', 'status' => 'Status'];
        $validator = \Validator::make($request->all(), [
                'student_id'=>'required',
                'leave_from'=>'required|date_format:d-m-Y',
                'leave_to'=>'required|date_format:d-m-Y',
                'request_by'=>'required',
                'status' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $leave->postLeave($this->user, $request);
    }
    
    public function updateLeaveRequest(Leave $leave, Request $request, $platform)
    {
    	$userError = ['id' => 'Leave Id', 'status' => 'Status'];
    	$validator = \Validator::make($request->all(), [
                'id' => 'required|numeric',
                'status' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    	return $leave->doUpdateLeaveRequest($request, $this->user, $platform);
    }
    
    public function postGallery(Request $request, Gallery $gallery, $flag)
    {
    	$userError = ['images' => 'Images Base 64 String Array', 'date' => 'Date in dd-mm-yyyy'];
    	$validator = \Validator::make($request->all(), [
                'images'=>'required',
                'date' => 'required|date_format:d-m-Y',
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $gallery->doPostGallery($this->user, $request);
    }
    
    public function getGallery(Gallery $gallery, $flag)
    {
    	return $gallery->doGetGallery($this->user);
    }
    
    public function getEmployees($flag, $staffId)
    {
    	$emps = Employee::where('type', $staffId)->where('school_id', $this->user->school_id)->get();
    	return \api::success(['data' => $emps]);
    }
    
    public function postTimeTable(TimeTable $time, Request $request, $platform)
    {
    	$userError = ['class_id' => 'Class', 'section_id' => 'Section', 'subject_id' => 'Subject', 'period' => 'Period', 'start_time' => 'Start Time', 
    	'end_time' => 'End Time',  'day' => 'Day', 'teacher_id' => 'Teacher'];
    	$validator = \Validator::make($request->all(), [
                'class_id'=>'required',
                'section_id' => 'required',
                'subject_id' => 'required',
                'period' => 'required',
                'day' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'teacher_id' => 'required',
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    	return $time->doPostTimeTable($this->user, $request, $platform);
    }

    public function getTimeTables(TimeTable $time, $platform, $class, $section)
    {
        return $time->doGetTimeTables($platform, $class, $section);
    }

    public function deleteTimeTable($platform, $id)
    {
        $table = TimeTable::where('id', $id)->first();
        if(!$table)
            return \api::notFound(['errorMsg' => 'Invalid Parameter']);
        TimeTable::where('id', $id)->delete();
        return \api(['data' => 'Time Table is deleted Successfully']);
    }

    public function editTimeTable(TimeTable $time, $platform, $id)
    {
        return $time->doEditTimeTable($this->user, $platform, $id);
    }

    public function updateTimeTable(TimeTable $time, Request $request, $platform)
    {
        $userError = ['id' => 'Time Table Id', 'class_id' => 'Class', 'section_id' => 'Section', 'subject_id' => 'Subject', 'period' => 'Period', 'start_time' => 'Start Time', 
        'end_time' => 'End Time',  'day' => 'Day', 'teacher_id' => 'Teacher'];
        $validator = \Validator::make($request->all(), [
                'id' => 'required|numeric',
                'class_id'=>'required|numeric',
                'section_id' => 'required|numeric',
                'subject_id' => 'required|numeric',
                'period' => 'required',
                'day' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'teacher_id' => 'required|numeric',
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $time->doUpdateTimeTable($request, $this->user, $platform);
    }
    
    public function getLeaveRequest(Leave $leave, $flag, $class, $section, $month)
    {
    	return $leave->getLeaveRequestByParam($class, $section, $month);
    }

    public function postResult(Result $result, Request $request, $platform)
    {   
        $userError = [
                'class_id' => 'Class Id', 
                'section_id' => 'Section Id', 
                'exam_type_id' => 'Exam Type Id',
                'month_id' => 'Month Id',
                'subject_id' => 'Subject Id', 
                'student_marks' => 'Student Marks With Id',
                'date' => 'Date',
                'max_marks' => 'Maximum Marks',
                'pass_marks' => 'Passing Marks'
        ];
        $validator = \Validator::make($request->all(), [
                'class_id' => 'required|numeric', 
                'section_id' => 'required|numeric', 
                'exam_type_id' => 'required|numeric',
                'month_id' => 'required|numeric',
                'subject_id' => 'required|numeric', 
                'student_marks' => 'required|json',
                'date' => 'required|date_format:d-m-Y',
                'max_marks' => 'required|numeric',
                'pass_marks' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $result->doPostResult($request, $this->user, $platform);
    }

    public function getResults(Result $result, $platform, $class, $section)
    {
        return $result->doGetResults($platform, $class, $section);
    }

    public function deleteResult(Result $result, $platform, $id)
    {
        return $result->doDeleteResult($platform, $id);
    }

    public function editResult(Result $result, $platform, $id)
    {
        return $result->doEditResult($platform, $id);
    }

    public function updateResult(Result $result, Request $request, $platform)
    {
        $userError = [
                'id' => 'Result Id',
                'class_id' => 'Class Id', 
                'section_id' => 'Section Id', 
                'exam_type_id' => 'Exam Type Id',
                'subject_id' => 'Subject Id', 
                'student_id' => 'Student Id',
                'date' => 'Date',
                'max_marks' => 'Maximum Marks',
                'pass_marks' => 'Passing Marks',
                'obtained_marks' => 'Obtained Marks'
        ];
        $validator = \Validator::make($request->all(), [
                'id' => 'required|numeric',
                'class_id' => 'required|numeric', 
                'section_id' => 'required|numeric', 
                'exam_type_id' => 'required|numeric',
                'subject_id' => 'required|numeric', 
                'student_id' => 'required|numeric',
                'date' => 'required|date_format:d-m-Y',
                'max_marks' => 'required|numeric',
                'pass_marks' => 'required|numeric',
                'obtained_marks' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $result->doUpdateResult($request, $this->user, $platform);
    }

   
     /*public function totalAttendance(){
        $session = date("a");
        $total_student= \DB::table('student')
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->where('school_id','=',$this->user->school_id)
            ->count();                      
        $count=array();
        $clas_section=\DB::table('class')->where('class.school_id','=',$this->user->school_id)
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
                ->join('section','class.id','=','section.class_id')
                ->where('section.school_id','=',$this->user->school_id)
                ->select('class.id as class_id','section.id as section_id','class.school_id')
                ->get();
        $absent_array=array();
        $leave_array=array();
        $attendance_taken_array=array();
        foreach($clas_section as $key=>$value)
        {
            $att_is_taken=\DB::table('attendance_status')
                    ->where('date','=',date('Y-m-d'))
                    ->where('school_id','=',$this->user->school_id)
                    ->where('attendance_session','=',$session)
                    ->where('section_id','=',$value->section_id)
                    ->where('class_id','=',$value->class_id)
                    ->first();
            if(!empty($att_is_taken)){
                $time_update=$att_is_taken->created_at;
                if($att_is_taken->updated_at!=''){
                   $time_update=$att_is_taken->updated_at;
                }
                $student_cnt=\DB::table('student')
                    ->where('session_id',$this->active_session->id)//updated 2-6-2018
                    ->where('school_id','=',$this->user->school_id)
                    ->where('section_id','=',$value->section_id)
                    ->where('class_id','=',$value->class_id)
                    ->where('created_at','<',$time_update)
                    ->select('id')
                    ->get();
            }else{
                $student_cnt=\DB::table('student')
                    ->where('session_id',$this->active_session->id)//updated 2-6-2018
                ->where('school_id','=',$this->user->school_id)
                ->where('section_id','=',$value->section_id)
                ->where('class_id','=',$value->class_id)
                //->where('created_at','<',$att_is_taken->created_at)
                ->select('id')
                ->get();
            }
            $aa=array();
            if(!empty($student_cnt)){
                foreach($student_cnt as $key_stu=>$value_stu){
                        array_push($aa,$value_stu->id);
                    }
                if(!empty($att_is_taken)){
                    array_push($attendance_taken_array,count($student_cnt));
                    $absent_new=\DB::table('attendance')->where('school_id','=',$this->user->school_id)
                    ->where('attendance_session','=',$session)
                    ->where('attendance','=','A')
                    ->where('class_id','=',$value->class_id)
                    ->where('section_id','=',$value->section_id)
                    ->whereIn('student_id',$aa)
                    ->where('date','=',date('Y-m-d'))->count();
                    $leave_new=\DB::table('attendance')->where('school_id','=',$this->user->school_id)
                    ->where('attendance_session','=',$session)
                    ->where('attendance','=','L')
                    ->where('class_id','=',$value->class_id)
                    ->where('section_id','=',$value->section_id)
                    ->whereIn('student_id',$aa)
                    ->where('date','=',date('Y-m-d'))->count(); 
                    array_push($absent_array,$absent_new);
                    array_push($leave_array,$leave_new);
                }
            }
        }
        $attendance_taken=array_sum($attendance_taken_array);
       // $attendance_not_taken= $total_student - $attendance_taken;
        $leave=array_sum($leave_array);
        $absend=array_sum($absent_array);      
        $present = $attendance_taken-$leave-$absend;
        $count=(['totalStudent'=>$total_student,'apsend'=>$absend,'leave_student'=>$leave,'present'=>$present]); 
       return api(['data'=>$count]);
    }*/
    public function totalAttendance(){
        $session = date("a");
        $total_student= \DB::table('student')
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('school_id','=',$this->user->school_id)
            ->count();                      
        $count=array();
        $clas_section=\DB::table('class')->where('class.school_id','=',$this->user->school_id)
                ->where('class.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('section','class.id','=','section.class_id')
                ->where('section.school_id','=',$this->user->school_id)
                ->select('class.id as class_id','section.id as section_id','class.school_id')
                ->get();
        $absent_array=array();
        $leave_array=array();
        $attendance_taken_array=array();
        foreach($clas_section as $key=>$value)
        {
            $att_is_taken=\DB::table('attendance_status')
                    ->where('date','=',date('Y-m-d'))
                    ->where('school_id','=',$this->user->school_id)
                    ->where('attendance_session','=',$session)
                    ->where('section_id','=',$value->section_id)
                    ->where('class_id','=',$value->class_id)
                    ->first();
            if(!empty($att_is_taken)){
                $time_update=$att_is_taken->created_at;
                if($att_is_taken->updated_at!=''){
                   $time_update=$att_is_taken->updated_at;
                }
                $student_cnt=\DB::table('student')
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('school_id','=',$this->user->school_id)
                    ->where('section_id','=',$value->section_id)
                    ->where('class_id','=',$value->class_id)
                    ->where('created_at','<',$time_update)
                    ->select('id')
                    ->get();
            }else{
                $student_cnt=\DB::table('student')
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id','=',$this->user->school_id)
                ->where('section_id','=',$value->section_id)
                ->where('class_id','=',$value->class_id)
                //->where('created_at','<',$att_is_taken->created_at)
                ->select('id')
                ->get();
            }
            $aa=array();
            if(!empty($student_cnt)){
                foreach($student_cnt as $key_stu=>$value_stu){
                        array_push($aa,$value_stu->id);
                    }
                if(!empty($att_is_taken)){
                    array_push($attendance_taken_array,count($student_cnt));
                    $absent_new=\DB::table('attendance')->where('school_id','=',$this->user->school_id)
                    ->where('attendance_session','=',$session)
                    ->where('attendance','=','A')
                    ->where('class_id','=',$value->class_id)
                    ->where('section_id','=',$value->section_id)
                    ->whereIn('student_id',$aa)
                    ->where('date','=',date('Y-m-d'))->count();
                    $leave_new=\DB::table('attendance')->where('school_id','=',$this->user->school_id)
                    ->where('attendance_session','=',$session)
                    ->where('attendance','=','L')
                    ->where('class_id','=',$value->class_id)
                    ->where('section_id','=',$value->section_id)
                    ->whereIn('student_id',$aa)
                    ->where('date','=',date('Y-m-d'))->count(); 
                    array_push($absent_array,$absent_new);
                    array_push($leave_array,$leave_new);
                }
            }
        }
        $attendance_taken=array_sum($attendance_taken_array);
       // $attendance_not_taken= $total_student - $attendance_taken;
        $leave=array_sum($leave_array);
        $absend=array_sum($absent_array);      
        $present = $attendance_taken-$leave-$absend;
        $count=(['totalStudent'=>$total_student,'apsend'=>$absend,'leave_student'=>$leave,'present'=>$present]); 
       return api(['data'=>$count]);
    }
   public function totalEmployerAttendance(){
        $session = date("a");           
        $totalEmployer = \DB::table('teacher')
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('school_id','=',$this->user->school_id)
            ->count();
        $leave = \DB::table('teacher_attendance')
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('school_id','=',$this->user->school_id)
            ->where('attendance','=','L')
            ->where('date','=',date('Y-m-d'))
            ->count();
        $absend = \DB::table('teacher_attendance')
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('school_id','=',$this->user->school_id)
            ->where('attendance','=','A')
            ->where('date','=',date('Y-m-d'))
            ->count();
        $present = \DB::table('teacher_attendance')
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('school_id','=',$this->user->school_id)
            ->where('attendance','=','P')
            ->where('date','=',date('Y-m-d'))
            ->count();            
        $count=(['totalEmployer'=>$totalEmployer,'apsend'=>$absend,'leave_student'=>$leave,'present'=>$present]); 
       return api(['data'=>$count]);
    }  
    public function classAttendanceCount()
    {
       $student =\DB::table('student')
                    ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                     ->where('student.school_id', $this->user->school_id)
                     ->join('class', 'student.class_id', '=', 'class.id')
                     ->join('section','student.section_id', '=', 'section.id')
                     ->select(\DB::raw('count(*) as total_student, student.school_id'),'student.class_id','student.section_id','class.class','section.section')
                     ->groupBy('student.section_id')
                     ->get();
        //dd($date=date("a"));
        $attendancetaken=\DB::table('attendance_status')
                     ->where('school_id', $this->user->school_id)
                     // added by kumaravel 13-06-2018
                     //->where('session_id',$this->active_session->id)
                     ->where('date',date("Y-m-d"))
                     ->where('attendance_session', date("a"))
                     ->get();

        $att_present_absent=\DB::table('attendance')
                            ->where('school_id', $this->user->school_id)
                           // ->where('session_id',$this->active_session->id)
                            ->where('attendance_session', date("a"))
                            ->where('date',date("Y-m-d"))
                            ->select(\DB::raw("SUM(CASE WHEN attendance = 'A' THEN 1 ELSE 0 END) AS 'totalabsend'"),\DB::raw("SUM(CASE WHEN attendance = 'L' THEN 1 ELSE 0 END) AS 'totalleave'"),'class_id','section_id')
                            ->groupBy('section_id')
                            ->get();
                $array=array();   

if(!empty($att_present_absent)){                            
        foreach ($attendancetaken as $attendancetakenkey => $attendancetakenvalue) {
           foreach ($att_present_absent as $keys => $values) {
                if($attendancetakenvalue->class_id == $values->class_id and $attendancetakenvalue->section_id == $values->section_id)
                {

                    foreach ($student as $stu_key => $stu_value) {
                        
                        if( $values->class_id == $stu_value->class_id and  $values->section_id == $stu_value->section_id)
                            {
                                $arrayval=array();

                                if($values->totalabsend != 0)
                                {
                                $stu_value->total_student=$stu_value->total_student-$values->totalabsend;
                                }
                                if($values->totalleave != 0)
                                {
                                 $stu_value->total_student=$stu_value->total_student-$values->totalleave;   
                                }
                                $arrayval['totalStudent']=$stu_value->total_student;
                                $arrayval['present']=$stu_value->total_student;
                                $arrayval['absent']=intval($values->totalabsend);
                                $arrayval['leave']=intval($values->totalleave);
                                $arrayval['class']=$stu_value->class." ".$stu_value->section;
                                 //dd($stu_value);
                                array_push($array, $arrayval);
                            }

                    }
                }
                
            }
            
                        
        }
    }else{
          foreach ($attendancetaken as $attendancetakenkey => $attendancetakenvalue) {
                    foreach ($student as $stu_key => $stu_value) {
                        
                        if( $attendancetakenvalue->class_id == $stu_value->class_id and  $attendancetakenvalue->section_id == $stu_value->section_id)
                            {
                                $arrayval=array();

                                $arrayval['present']=$stu_value->total_student;
                                $arrayval['absent']=0;
                                $arrayval['leave']=0;
                                $arrayval['class']=$stu_value->class." ".$stu_value->section;
                                 //dd($stu_value);
                                array_push($array, $arrayval);
                            }
                    }
            }
    }
         $nottaken=array();
        //dd($student);
        if(!empty($attendancetaken)){
             
            foreach ($student as $key => $stuvalue) {
                    foreach ($attendancetaken as $att_key => $attvalue) {
                        //echo $att_value->section_id;
                        if($stuvalue->class_id == $attvalue->class_id && $stuvalue->section_id == $attvalue->section_id)
                        {
                         break;
                        }
                        else
                        {
                         $nottakenval = array();
                             $nottakenval['totalstudent']=$stuvalue->total_student;
                         $nottakenval['class']=$stuvalue->class." ".$stuvalue->section;
                             array_push($nottaken,$nottakenval);
                             //print_r($nottaken);   
                        }
                    }
            }
        }
        else
        {
            foreach($student as $stu_keys => $stu_values)
            {           $nottakenval = array();
                        $nottakenval['totalstudent']=$stu_values->total_student;
                        $nottakenval['class']=$stu_values->class." ".$stu_values->section;
                        array_push($nottaken,$nottakenval);
              } 

        }

        return api(['data'=>$array,'nottaken'=>$nottaken]);
    }
  
  /* public function classAttendanceCount()
    {
       $student =\DB::table('student')
           ->where('student.session_id',$this->active_session->id)//updated 2-6-2018
                     ->where('student.school_id', $this->user->school_id)
                     ->join('class', 'student.class_id', '=', 'class.id')
                     ->join('section','student.section_id', '=', 'section.id')
                     ->select(\DB::raw('count(*) as total_student, student.school_id'),'student.class_id','student.section_id','class.class','section.section')
                     ->groupBy('student.section_id')
                     ->get();
        //dd($date=date("a"));
        $attendancetaken=\DB::table('attendance_status')
                     ->where('school_id', $this->user->school_id)
                     ->where('date',date("Y-m-d"))
                     ->where('attendance_session', date("a"))
                     ->get();

        $att_present_absent=\DB::table('attendance')
                            ->where('school_id', $this->user->school_id)
                            ->where('attendance_session', date("a"))
                            ->where('date',date("Y-m-d"))
                            ->select(\DB::raw("SUM(CASE WHEN attendance = 'A' THEN 1 ELSE 0 END) AS 'totalabsend'"),
                                \DB::raw("SUM(CASE WHEN attendance = 'L' THEN 1 ELSE 0 END) AS 'totalleave'"),'class_id','section_id')
                            ->groupBy('section_id')
                            ->get();
                $array=array();    
 if(!empty($att_present_absent)){                         
        foreach ($attendancetaken as $attendancetakenkey => $attendancetakenvalue) {
           foreach ($att_present_absent as $keys => $values) {
                if($attendancetakenvalue->class_id == $values->class_id and $attendancetakenvalue->section_id == $values->section_id)
                {

                    foreach ($student as $stu_key => $stu_value) {
                        
                        if( $values->class_id == $stu_value->class_id and  $values->section_id == $stu_value->section_id)
                            {
                                $arrayval=array();

                                if($values->totalabsend != 0)
                                {
                                $stu_value->total_student=$stu_value->total_student-$values->totalabsend;
                                }
                                if($values->totalleave != 0)
                                {
                                 $stu_value->total_student=$stu_value->total_student-$values->totalleave;   
                                }
                                $arrayval['totalStudent']=$stu_value->total_student;
                                $arrayval['present']=$stu_value->total_student;
                                $arrayval['absent']=intval($values->totalabsend);
                                $arrayval['leave']=intval($values->totalleave);
                                $arrayval['class']=$stu_value->class." ".$stu_value->section;
                                 //dd($stu_value);
                                array_push($array, $arrayval);
                            }

                    }
                }
                
            }
        }
    }
    else
    {
         foreach ($attendancetaken as $attendancetakenkey => $attendancetakenvalue) {
                    foreach ($student as $stu_key => $stu_value) {
                        
                        if( $attendancetakenvalue->class_id == $stu_value->class_id and  $attendancetakenvalue->section_id == $stu_value->section_id)
                            {
                                $arrayval=array();

                                $arrayval['present']=$stu_value->total_student;
                                $arrayval['absent']=0;
                                $arrayval['leave']=0;
                                $arrayval['class']=$stu_value->class." ".$stu_value->section;
                                 //dd($stu_value);
                                array_push($array, $arrayval);
                            }
                    }
            }
        }
         $nottaken=array();
        //dd($student);
        if(!empty($attendancetaken)){
             
            foreach ($student as $key => $stuvalue) {
                    foreach ($attendancetaken as $att_key => $attvalue) {
                        //echo $att_value->section_id;
                        if($stuvalue->class_id == $attvalue->class_id && $stuvalue->section_id == $attvalue->section_id)
                        {
                         break;
                        }
                        else
                        {
                         $nottakenval = array();
                             $nottakenval['totalstudent']=$stuvalue->total_student;
                         $nottakenval['class']=$stuvalue->class." ".$stuvalue->section;
                             array_push($nottaken,$nottakenval);
                             //print_r($nottaken);   
                        }
                    }
            }
        }
        else
        {
            foreach($student as $stu_keys => $stu_values)
            {           $nottakenval = array();
                        $nottakenval['totalstudent']=$stu_values->total_student;
                        $nottakenval['class']=$stu_values->class." ".$stu_values->section;
                        array_push($nottaken,$nottakenval);
              } 

        }
        return api(['data'=>$array,'nottaken'=>$nottaken]);
    }*/

    public function attendanceReportStudent(Attendance $report)
    {
        return $report->singleStudentAttendance($this->user);
    }

    public function download()
    {
        return response()->download(\Session::get('attendanceUrl'));
    }

    public function classAttendanceReport(Attendance $report)
    {
        return $report->classAttendanceReports($this->user);
    }
}
