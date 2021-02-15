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
use App\MobileUser;
use DateTime;
use DatePeriod;
use DateInterval;

class ApiTeacherController extends Controller
{
    protected $user; 
    protected $teacher;

    function __construct(){
        try{
            $this->user = JWTAuth::parseToken()->authenticate();
            $this->teacher = Employee::where('user_id', \Auth::user()->id)->first();
            $this->homecontroller = HomeController;
        }
        catch(\Exception$e){}
    }
public function updatepass(){
        $request = \Request::all();
        if($request['newpass']==$request['confirmpass']){
            $user_exits=\DB::table('users')->where('id','=',$request['user_id'])
            ->select('id','hint_password')->first();
            if(count($user_exits)>0){
                if($user_exits->hint_password==$request['oldpass']){
                    $updates=\DB::table('users')->where('id', '=',$user_exits->id)
                    ->update(['password' => bcrypt($request['newpass']),'hint_password' => $request['newpass']
                            ]);
                    $msg['success'] = 'Password changed Successfully';
                    return api(['data' => $msg]);
                }
                else{
                    $errorlogin['error'] = "true";
                    $errorlogin['message'] = "Your old Password Invalid";
                    return api(['data' => $errorlogin]);
                }  
            }   
        }
        else{
            $errorlogin['error'] = "true";
            $errorlogin['message'] = "Password is Mismatch";
            return api(['data' => $errorlogin]);
        }
    }

    // public function getGrade(request $request){

    // $input = \Request::all();

    // $grade=\DB::table('grade_system')
    //     ->where('school_id','=', $this->user->school_id)
    //     ->where('exam_type_id','=',$input['id'])
    //     ->where('from_marks','<=',$input['marks'])
    //     ->where('to_marks','>=',$input['marks'])
    //     ->get();
    //     if(empty($grade))
    //     {
    //         return api(['error'=>true,'errorMsg' => 'Result not Available']);
    //     }
    //     else{
    //         return api(['data' => $grade]);
    //     }
    // }



/// changes by mari v3.. 27/09/2017

public function getGrade(request $request){
    $input = \Request::all();
        //echo $this->user;
    $grade=\DB::table('grade_system')
        ->where('school_id','=', $this->user->school_id)
        ->where('exam_type_id','=',$input['id'])
        ->where('from_marks','<=',$input['marks'])
        ->where('to_marks','>=',$input['marks'])
        ->get();        
        if(empty($grade))
        { $grade[0]=array('result' => 'Result not Available');
            return api(['data' => $grade]);
        }
        else{
            return api(['data' => $grade]);
        }
            
            
      
    }

    public function authenticate(Request $request)
    {
            // grab credentials from the request
        $credentials = $request->only('username', 'password');
        $validator = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return api()->notValid(['message'=>$validator->errors()->first()]);   
        try 
        {
            if (! $token = JWTAuth::attempt($credentials))
                return api()->notValid(['message'=>'Invalid credentials']);
        } 
        catch (JWTException $e) 
        {
            return api()->notValid(['message'=>'Something went wrong']);
        }

        if(\Auth::user()->type == 'student')
        {
            $student = Students::where('student.user_id', \Auth::user()->id)
                        ->select('id', 'name','bus_id', 'roll_no', 'avatar')
                        ->first();
            $this->updateMobileUserTable($student->id);   
              $school_image=School::where('id',\Auth::user()->school_id)->first();
          $userplans=[];
            if(!$school_image->userplan)
            {
                $school_image->userplan='Basic';
            }
            if($school_image->userplan){
                    
                    $userplandetail= \DB::table('schooluser_plan')->where($school_image->userplan, 0)->select('Modules')->get();
                     if($userplandetail)
                     {
                        foreach ($userplandetail as $key => $value) {
                            array_push($userplans, $userplandetail[$key]->Modules);
                        }
                     }
            }
            if($school_image->userplanAdded)
            {
               
                $explodearray=explode(",",$school_image->userplanAdded);
                $userplansadded=\DB::table('schooluser_plan')->whereIn('id', $explodearray)->select('Modules')->get();
               // dd($userplansadded);
                 if($userplansadded)
                     {
                        foreach ($userplansadded as $key => $value) {
                              array_splice($userplans, array_search($userplansadded[$key]->Modules, $userplans), 1);
                           
                        }

                     }
            }
            return api()->success(['token'=> $token, 'id'=> $student->id,'user_id'=>\Auth::user()->id, 'bus_id'=>$student->bus_id, 'name' => $student->name, 'roll_no' => $student->roll_no, 'role' => \Auth::user()->type,'status'=>\Auth::user()->status,'userplan'=>$userplans]);
        }
        if(\Auth::user()->type == 'drivers')
        {
            $drivers = \DB::table('driver')->where('user_id', \Auth::user()->id)->first();
            $this->updateMobileUserTable($drivers->id);   
             $school_image=School::where('id',\Auth::user()->school_id)->first();
            $userplans=[];
            if(!$school_image->userplan)
            {
                $school_image->userplan='Basic';
            }
            if($school_image->userplan){
                    
                    $userplandetail= \DB::table('schooluser_plan')->where($school_image->userplan, 0)->select('Modules')->get();
                     if($userplandetail)
                     {
                        foreach ($userplandetail as $key => $value) {
                            array_push($userplans, $userplandetail[$key]->Modules);
                        }
                     }
            }
            if($school_image->userplanAdded)
            {
               
                $explodearray=explode(",",$school_image->userplanAdded);
                $userplansadded=\DB::table('schooluser_plan')->whereIn('id', $explodearray)->select('Modules')->get();
               // dd($userplansadded);
                 if($userplansadded)
                     {
                        foreach ($userplansadded as $key => $value) {
                              array_splice($userplans, array_search($userplansadded[$key]->Modules, $userplans), 1);
                           
                        }

                     }
            }
            return api()->success(['token'=> $token, 'id'=> $drivers->id,'user_id'=>\Auth::user()->id,'bus_id'=> $drivers->bus_id, 'name' => $drivers->driver_name, 'mobile' => $drivers->driver_mobile,'school_id' => \Auth::user()->school_id, 'role' => \Auth::user()->type,'status'=>\Auth::user()->status,'userplan'=>$userplans]);
        }

        if(\Auth::user()->type == 'teacher')
        {
            $teacher = \DB::table('teacher')->where('user_id', \Auth::user()->id)->first();

            \Auth::user()->class_id = $teacher->class;
            \Auth::user()->section_id = $teacher->section;
            $feedback_count=\DB::table('feedback')->where('feedback_by','=','parent')
            ->where('view_status','=','0')
            ->where('teacher_id','=',$teacher->id)->count();
            $this->updateMobileUserTable($teacher->id);   
             $school_image=School::where('id',\Auth::user()->school_id)->first();
            $userplans=[];
            if(!$school_image->userplan)
            {
                $school_image->userplan='Basic';
            }
            if($school_image->userplan){
                    
                    $userplandetail= \DB::table('schooluser_plan')->where($school_image->userplan, 0)->select('Modules')->get();
                     if($userplandetail)
                     {
                        foreach ($userplandetail as $key => $value) {
                            array_push($userplans, $userplandetail[$key]->Modules);
                        }
                     }
            }
            if($school_image->userplanAdded)
            {
               
                $explodearray=explode(",",$school_image->userplanAdded);
                $userplansadded=\DB::table('schooluser_plan')->whereIn('id', $explodearray)->select('Modules')->get();
               // dd($userplansadded);
                 if($userplansadded)
                     {
                        foreach ($userplansadded as $key => $value) {
                              array_splice($userplans, array_search($userplansadded[$key]->Modules, $userplans), 1);
                           
                        }

                     }
            }
            return api()->success(['token'=> $token, 'id' => $teacher->id,'user_id'=>\Auth::user()->id, 'name' => $teacher->name, 'email' => $teacher->email, 'mobile' => $teacher->mobile, 'class_id' => $teacher->class,
            'section_id' => $teacher->section,'fcount'=>$feedback_count,'role' => \Auth::user()->type,'status'=>\Auth::user()->status,'userplan'=>$userplans]);
        }

        if(\Auth::user()->type == 'parent')
        {
            $parent = StuParent::where('user_id', \Auth::user()->id)->first();
            $this->updateMobileUserTable($parent->id);
            $school_image=School::where('id',\Auth::user()->school_id)->first();
            $userplans=[];
            if(!$school_image->userplan)
            {
                $school_image->userplan='Basic';
            }
            if($school_image->userplan){
                    
                    $userplandetail= \DB::table('schooluser_plan')->where($school_image->userplan, 0)->select('Modules')->get();
                     if($userplandetail)
                     {
                        foreach ($userplandetail as $key => $value) {
                            array_push($userplans, $userplandetail[$key]->Modules);
                        }
                     }
            }
            if($school_image->userplanAdded)
            {
               
                $explodearray=explode(",",$school_image->userplanAdded);
                $userplansadded=\DB::table('schooluser_plan')->whereIn('id', $explodearray)->select('Modules')->get();
               // dd($userplansadded);
                 if($userplansadded)
                     {
                        foreach ($userplansadded as $key => $value) {
                              array_splice($userplans, array_search($userplansadded[$key]->Modules, $userplans), 1);
                           
                        }

                     }
            }
            return api()->success(['token'=> $token, 'id' => $parent->id, 'user_id'=>\Auth::user()->id, 'name' => $parent->name, 'role' => \Auth::user()->type,'userplan'=>$userplans,'status'=>\Auth::user()->status]);
        }

        if(\Auth::user()->type != 'teacher' && \Auth::user()->type != 'parent' && \Auth::user()->type != 'student' && \Auth::user()->type != 'drivers')
        {
            return api::notFound(['errorMsg' => 'Invalid Role']);
        }

        return api()->success(['token'=> $token, 'id'=> \Auth::user()->id,'user_id'=>\Auth::user()->id, 'name' => $data->name, 'primary_id' => $data->id, 'email' => $data->email, 'mobile' => $data->mobile, 'role' => \Auth::user()->type]);
    }
    
    public function updateMobileUserTable($user_type_id)
    {   

       $userCount = MobileUser::where('user_type_id',$user_type_id)->count();
       // first time logged users
       if($userCount == 0){
            $mobileUser = new MobileUser();
            $mobileUser->user_type_id = $user_type_id;
            $mobileUser->logged_in_count = 1;
            $mobileUser->logged_in_date = date('Y-m-d H:i:s');
            $mobileUser->save();
       }else{
            $mobileUserObj = MobileUser::where('user_type_id',$user_type_id)->first();
            $newCount = $mobileUserObj->logged_in_count + 1;
            $mobileUserObj->logged_in_count = $newCount;
            $mobileUserObj->logged_in_date = date('Y-m-d H:i:s');
            $mobileUserObj->update();            
       }
    }

    // public function getSubjects()
    // {
    //     $section = Section::where('id', $this->teacher->section)->first();
    //     $subjects = Subject::whereIn('id', json_decode($section->subjects))->get();
    //     return api(['data' => $subjects]);
    // }
    public function getSubjects($platform,$id,$secid)
     {//change by mari 27-09-2017
        $section = Section::where('id',$secid)->first();
        $stu_count=\DB::table('student')
        ->where('section_id','=',$secid)
       // ->where('class_id','=',$this->teacher->class)
        ->count();
        $sub=json_decode($section->subjects);
       // $sss=json_decode($section->subjects);
     foreach($sub as $sub_key=>$value){
            $reslt_count=\DB::table('result')
           // ->where('class_id','=',$this->teacher->class)
            ->where('section_id','=',$secid)
            ->where('exam_type_id','=',$id)
            ->where('subject_id','=',$value)->count();
            $res_countarr[]=$reslt_count;
            if($reslt_count>0){
                if(($key = array_search($value, $sub)) !== false) {
                    unset($sub[$key]);
                    $sub= array_values($sub);
                }
            }
        }
       
        $min_max=\DB::table('exam')->where('id','=',$id)->select('pass_marks','max_marks')->first();
        $subjects = Subject::whereIn('id', $sub)->get();
        $data=array('subjects'=>$subjects,'pass_marks'=>$min_max->pass_marks,'max_marks'=>$min_max->max_marks);
       //$data=array('stu_count'=>$stu_count,'org'=>$sss,'subrr'=>$sub,'rescout'=>$res_countarr,'pass_marks'=>$min_max->pass_marks,'max_marks'=>$min_max->max_marks);
        
        
        return api(['data' =>$data]);
    }

    public function getStudents()
    {
        $students = Students::where('class_id', $this->teacher->class)
                            ->where('school_id', $this->user->school_id)
                            ->where('section_id', $this->teacher->section)
                            ->select('id', 'name', 'roll_no', 'avatar')
                            //->orderBy('student.roll_no', 'ASC')
                            ->orderBy('student.name', 'ASC')
                            ->get();
        return api(['data' => $students]);
    }

   // changes done by parthiban 19-11-2017(sunday)
    // public function getStudentsByMark($platform,$class_id,$section_id)
    // {
    //     $students = Students::where('class_id', $class_id)
    //                         ->where('school_id', $this->user->school_id)
    //                         ->where('section_id', $section_id)
    //                         ->select('id', 'name', 'roll_no', 'avatar')
    //                         ->orderBy('student.roll_no', 'ASC')
    //                         ->get();
    //     return api(['data' => $students]);
    // }

    public function getStudentsByMark()
    {
        $input = \Request::all();
        $student= Students::where('class_id', $input["class"])
            ->where('school_id', $this->user->school_id)
            ->where('section_id', $input["section"])
            ->select('id','name', 'roll_no', 'avatar')
            //->orderBy('student.roll_no', 'ASC')
            ->orderBy('student.name', 'ASC')
            ->get();
        $students=array();
        
        foreach($student as $key_stu=>$value){
            $attance=Attendance::where('class_id', $input["class"])
                ->where('student_id',$value->id)
                ->where('school_id', $this->user->school_id)
                ->where('section_id', $input["section"])
                ->where('date', date('Y-m-d',strtotime($input["date"])))
                ->where('attendance_session',$input["session"])
                ->where('attendance','!=','P')
                ->first();
            if(!empty($attance)){
                $atte="A";
            }else{
                $atte="P";
            }
            $stu_arr=array('id'=>$value->id,'name'=>$value->name,'roll_no'=>$value->roll_no,'avatar'=>$value->avatar,'attance'=>$atte);
            array_push($students,$stu_arr);
            
        }
                            
        return api(['data' =>$students]);
    }    
    
    // public function getAttendanceStudents(){
    //     $student = Students::where('class_id', $this->teacher->class)
    //                         ->where('school_id', $this->user->school_id)
    //                         ->where('section_id', $this->teacher->section)
    //                         ->select('id', 'name', 'roll_no', 'avatar')
    //                         ->orderBy('student.roll_no', 'ASC')
    //                         ->get();
    //     $students = array();
    //     foreach($student as $key=>$value){
    //         $date = new DateTime();
    //         $stu_att= Attendance::where('student_id', $value->id)
    //                 ->where('date', $date->format('Y-m-d'))->where('attendance_session',date('a'))
    //                 ->where('attendance','L')->first();
    //         if(!$stu_att){
    //             $students[]= Students::where('class_id', $this->teacher->class)
    //                         ->where('school_id', $this->user->school_id)
    //                         ->where('section_id', $this->teacher->section)
    //                         ->where('id',$value->id)
    //                         ->select('id', 'name', 'roll_no', 'avatar')
    //                         ->orderBy('student.roll_no', 'ASC')
    //                         ->first();
    //         }
    //     }
    //     return api(['data' => $students]);
    // }

    // changes done by parthiban 19-11-2017(sunday)
    // public function getAttendanceStudents(){
    //     $student= Students::where('class_id', $this->teacher->class)
    //                 ->where('school_id', $this->user->school_id)
    //                 ->where('section_id', $this->teacher->section)
    //                 ->select('id', 'name', 'roll_no','class_id','section_id','avatar')
    //                 ->orderBy('student.roll_no', 'ASC')
    //                 ->get();
    //     $students = array();
    //     $cnt=0;
    //     $date = new DateTime();
        
    //     $ses=date("a");
    //     $date_today=$date->format('Y-m-d');
        
    //     foreach($student as $key=>$value){
    //            $stu_att=\DB::table('attendance')->where('student_id','=', $value->id)
    //                 ->where('date','=',$date_today)
    //                 ->where('attendance_session','=',$ses)
    //                 ->where('attendance','=','L')
    //                 ->first();
    //             $students[$cnt]['id']=$value->id;
    //             $students[$cnt]['name']=$value->name;
    //             $students[$cnt]['roll_no']=$value->roll_no;
    //             $students[$cnt]['class_id']=$value->class_id;
    //             $students[$cnt]['section']= $value->section_id;
    //             $students[$cnt]['avatar']=  $value->avatar;
    //             if(empty($stu_att))
    //                 $students[$cnt]['attance']= "present";
    //             else
    //                 $students[$cnt]['attance']= "";
    //         $cnt++;
    //     }
    //     return api(['data' =>$students]);
    // }

    public function getAttendanceStudents($platform,$class_id,$section_id){        
        $student= Students::where('class_id', $class_id)
                    ->where('school_id', $this->user->school_id)
                    ->where('section_id', $section_id)
                    ->select('id', 'name', 'roll_no','class_id','section_id','avatar')
                    //->orderBy('student.roll_no', 'ASC')
                    ->orderBy('student.name', 'ASC')
                    ->get();

        $students = array();
        $cnt=0;
        $date = new DateTime();
        
        $ses=date("a");
        $date_today=$date->format('Y-m-d');
        
        foreach($student as $key=>$value){
               $stu_att=\DB::table('attendance')->where('student_id','=', $value->id)
                    ->where('date','=',$date_today)
                    ->where('attendance_session','=',$ses)
                    ->where('attendance','=','L')
                    ->first();
                $students[$cnt]['id']=$value->id;
                $students[$cnt]['name']=$value->name;
                $students[$cnt]['roll_no']=$value->roll_no;
                $students[$cnt]['class_id']=$value->class_id;
                $students[$cnt]['section']= $value->section_id;
                $students[$cnt]['avatar']=  $value->avatar;
                if(empty($stu_att))
                    $students[$cnt]['attance']= "present";
                else
                    $students[$cnt]['attance']= "";
            $cnt++;
        }
        return api(['data' =>$students]);
    }      

    public function getExamTypes()
    {
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        return api(['data' => $exams]);
    }
    public function getExammarks($platform,$id,$roles)
    {
        //echo $roles;
        if($roles=='parent')
        {
        $results=Result::where('student_id', $id)->where('view_status',0)->count();
        }
        else
        {
         $results=Result::where('student_id', $id)->where('view_status_s',0)->count();
        }
        return api(['data' => $results]);
    }

//    public function postAttendance(Attendance $att)
//    {
//        
//        $input = \Request::all();
//        $userError = ['attendance' => 'Attendance', 'date' => 'Date in dd-mm-yyyy'];
//        $validator = \Validator::make($input, [
//            'attendance' => 'required',
//            'date' => 'required|date_format:d-m-Y H:i'
//        ], $userError);
//        $validator->setAttributeNames($userError);
//        if($validator->fails())
//            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
//        return $att->doPostAttendanceByTeacher($input, $this->user, $this->teacher);
//    }
    public function postAttendance(Attendance $att)    {
        $input = \Request::all();
        if(empty($input))
            return api(['data' => 'Data is not valid']);
		else{
			$date=date('Y-m-d', strtotime($input['date']));
		$holiday = new Holiday();
		$is_holiday = $holiday->is_holiday($date);
		if($is_holiday) {
            return api(['data' => 'Not allowed to place attendance at holiday ']);
			}
		}
       return $att->doPostAttendanceByTeacher($input, $this->user, $this->teacher);
    }

    public function getAttendance($platform, $date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$date))
        {
            $students = \DB::table('student')->where('student.class_id', $this->teacher->class)
                    ->where('student.section_id', $this->teacher->section)
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')
                    ->select
                    (
                        'student.id', 'student.name', 'student.roll_no', 
                        'student.avatar', 'class.class','section.section'
                    )
                    ->get();
            foreach($students as $student)
            {
                $attens = Attendance::where('attendance.student_id', $student->id)->where('date', $date)
                            ->leftJoin('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                            ->select
                            (
                                'attendance.id', 'attendance.attendance',
                                'attendance.remarks', 'attendance.date',
                                'teacher.name as teacherName'
                            )->first();
                if(count($attens)>0)
                {
                    $attendances[] = array
                                (
                                    'student_id' => $student->id,
                                    'name' => $student->name,
                                    'roll_no' => $student->roll_no,
                                    'image' => $student->avatar,
                                    'class' => $student->class,
                                    'section' => $student->section,
                                    'attendance' => $attens
                                );    
                }
                else
                {
                    $attendances[] = array
                                (
                                    'student_id' => $student->id,
                                    'name' => $student->name,
                                    'roll_no' => $student->roll_no,
                                    'image' => $student->avatar,
                                    'class' => $student->class,
                                    'section' => $student->section,
                                    'attendance' => 'Attendance Not Found'
                                );
                }
                
            }
            return api(['data' => $attendances]);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
        }
    }

    // changes done by parthiban 20-11-2017
    // public function postHomeWork(Request $request, Homework $hw)
    // {
    //     $userError = ['subject_id'=>'Subject', 'description'=>'Description', 'image' => 'Image', 'date' => 'Date', 'class'=>'Class', 'section'=>'Section'];
    //     $validator = \Validator::make($request->all(), [
    //         'subject_id' => 'required',
    //         'description' => 'required',
    //         'date' => 'required|date_format:d-m-Y',
    //         'pdf' => 'mimes:pdf',
    //         'class'=>'required',
    //         'section'=>'required'
    //         ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         $homeworkExist = Homework::where('school_id', $this->user->school_id)
    //                 ->where('class_id', $request['class'])
    //                 ->where('section_id', $request['section'])
    //                 ->where('subject_id', $request['subject_id'])
    //                 ->where('date', $request['date'])
    //                 ->first();
    //         if($homeworkExist)
    //         {
    //             return \api::notValid(['errorMsg' => 'Homework already exists', 'id' => $homeworkExist->id]);
    //         }
    //         else
    //         { 
    //             if(isset($request['image']))
    //             {
    //                 define('UPLOAD_DIR', 'homework/');
    //                 $img = str_replace('data:image/jpeg;base64,', '', $request['image']);
    //                 $img = str_replace(' ', '+', $img);
    //                 $dataImg = base64_decode($img);
    //                 $file = UPLOAD_DIR . uniqid() . '.png';
    //                 $success = file_put_contents($file, $dataImg);  
    //             }
    //             else
    //             {
    //                 $file = '';
    //             }

    //             if(isset($request['pdf']))
    //             {
    //                 $pdf = $request['pdf'];
    //                 $ex = $pdf->getClientOriginalExtension();
    //                 $name = $pdf->getClientOriginalName();
    //                 $destinationPath = 'homework';
    //                 $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
    //                 $upload_pdf = $pdf->move($destinationPath, $pdfname);
    //                 $pdffile = $destinationPath.'/'.$pdfname; 

    //             }
    //             else
    //             {
    //                 $pdffile = '';
    //             }
                
    //             $id = Homework::insertGetId([
    //                     'school_id' => $this->user->school_id,
    //                     'class_id' => $request['class'],
    //                     'section_id' => $request['section'],
    //                     'subject_id' => $request['subject_id'],
    //                     'teacher_id' => $this->teacher->id,
    //                     'description' => $request['description'],
    //                     'image'=> $file,
    //                     'pdf' => $pdffile,
    //                     'date' => $request['date'],
    //                     'homework_by' => 'teacher'
    //             ]);
    //             return api(['message'=>'Homework saved successfully']);
    //         }
    //     }
    // }

    public function postHomeWork(Request $request, Homework $hw)
    {
        $userError = ['subject_id'=>'Subject', 'description'=>'Description', 'image' => 'Image', 'date' => 'Date', 'class'=>'Class', 'section'=>'Section'];
        $validator = \Validator::make($request->all(), [
            'subject_id' => 'required',
            'description' => 'required',
            'date' => 'required|date_format:d-m-Y',
            'pdf' => 'mimes:pdf',
            'class'=>'required',
            'section'=>'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            if(isset($request['image']))
            {
                define('UPLOAD_DIR', 'homework/');
                $img = str_replace('data:image/jpeg;base64,', '', $request['image']);
                $img = str_replace(' ', '+', $img);
                $dataImg = base64_decode($img);
                $file = UPLOAD_DIR . uniqid() . '.png';
                $success = file_put_contents($file, $dataImg);  
            }
            else
            {
                $file = '';
            }

            if(isset($request['pdf']))
            {
                $pdf = $request['pdf'];
                $ex = $pdf->getClientOriginalExtension();
                $name = $pdf->getClientOriginalName();
                $destinationPath = 'homework';
                $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                $upload_pdf = $pdf->move($destinationPath, $pdfname);
                $pdffile = $destinationPath.'/'.$pdfname; 

            }
            else
            {
                $pdffile = '';
            }
            
            $id = Homework::insertGetId([
                    'school_id' => $this->user->school_id,
                    'class_id' => $request['class'],
                    'section_id' => $request['section'],
                    'subject_id' => $request['subject_id'],
                    'teacher_id' => $this->teacher->id,
                    'description' => $request['description'],
                    'image'=> $file,
                    'pdf' => $pdffile,
                    'date' => $request['date'],
                    'homework_by' => 'teacher'
            ]);
            return api(['message'=>'Homework saved successfully']);
        }
    }    

    public function getHomework()
    {
        $homework = Homework::where('date', date('d-m-Y'))
                            ->where('teacher_id', $this->teacher->id)
                            ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                            ->select('homework.id', 'homework.description', 'homework.date', 'homework.image', 'homework.pdf', 'subject.id as subject_id', 'subject.subject')
                            ->get();
        return api(['data' => $homework]);
    }

    public function updateHomeWork(Request $request, Homework $hw)
    {
        $userError = ['id' => 'Homework Id', 'subject_id'=>'Subject', 'description'=>'Description', 'image'=>'Image', 'date' => 'Date'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'subject_id' => 'required',
            'description' => 'required',
            'date' => 'required|date_format:d-m-Y'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {   
            $homeworkFound = Homework::where('id', $id)->first();
            
            $homeworkExist = Homework::where('school_id', $this->user->school_id)
                    ->where('class_id', $this->teacher->class)
                    ->where('section_id', $this->teacher->section)
                    ->where('subject_id', $request['subject_id'])
                    ->where('date', $request['date'])
                    ->where('id', '!=', $request['id'])
                    ->first();
            if($homeworkExist)
            {
                return \api::notValid(['errorMsg' => 'Homework already exists', 'id' => $homeworkExist->id]);
            }
            else
            {
                if(isset($request['image']))
                {
                    define('UPLOAD_DIR', 'homework/');
                    $img = str_replace('data:image/jpeg;base64,', '', $request['image']);
                    $img = str_replace(' ', '+', $img);
                    $dataImg = base64_decode($img);
                    $file = UPLOAD_DIR . uniqid() . '.png';
                    $success = file_put_contents($file, $dataImg);    
                }
                else
                {
                    $file = $homeworkFound->image;
                }

                if(isset($request['pdf']))
                {
                    $pdf = $request['pdf'];
                    $ex = $pdf->getClientOriginalExtension();
                    $name = $pdf->getClientOriginalName();
                    $destinationPath = 'homework';
                    $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                    $upload_pdf = $pdf->move($destinationPath, $pdfname);
                    $pdffile = $destinationPath.'/'.$pdfname;
                }
                else
                {
                    $pdffile = $homeworkFound->pdf;
                }
                
                
                $id = Homework::where('id', $request['id'])->update([
                        'subject_id' => $request['subject_id'],
                        'description' => $request['description'],
                        'image'=> $file,
                        'pdf' => $pdf,
                        'date' => $request['date']
                ]);
                return api(['message'=>'Homework updated successfully']);
            }
        }
    }

    // public function postFeedBack(Feedback $feedback)
    // {
    //     $request = \Request::all();
    //     $userError = ["student_id" => 'Student Id', 'feedback'=>'Feedback', 'date' => 'Date in dd-mm-yyyy'];
    //     $validator = \Validator::make($request, [
    //             'student_id'=>'required',
    //             'feedback'=>'required',
    //             'date' => 'required|date_format:d-m-Y'
    //         ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         Feedback::insert([
    //             'student_id' => $request['student_id'],
    //             'feedback' => $request['feedback'],
    //             'date' => $request['date'],
    //             'teacher_id' => $this->teacher->id,
    //             'school_id' => $this->user->school_id,
    //             'feedback_by' => 'teacher'          
    //         ]);
    //         return api(['data'=>'Feedback Submitted Successfully']);
    //     }
    // }

    public function postFeedBack(Feedback $feedback)
    {
        $request = \Request::all();
        $userError = ["student_id" => 'Student Id', 'feedback'=>'Feedback', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($request, [
                'student_id'=>'required',
                'feedback'=>'required',
                'date' => 'required|date_format:d-m-Y'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            Feedback::insert([
                'student_id' => $request['student_id'],
                'feedback' => $request['feedback'],
                'date' => $request['date'],
                'teacher_id' => $this->teacher->id,
                'school_id' => $this->user->school_id,
                'feedback_by' => 'teacher',
                'view_status'=>'0'        
            ]);
            return api(['data'=>'Feedback Submitted Successfully']);
        }
    }

    // new action added by parthiban 19-11-2017(sunday)
    public function postCommonFeedBack(Request $request)
    {
        $value = $request->all();
        foreach ($value['selectedStudents'] as $key => $val) {
            Feedback::insert([
                'student_id' => $val,
                'feedback' => $value['feedback'],
                'date' => $value['date'],
                'teacher_id' => $this->teacher->id,
                'school_id' => $this->user->school_id,
                'feedback_by' => 'teacher',
                'view_status'=>'0'        
            ]);
        }
        return api(['data'=>'Feedback Submitted Successfully']);
    } 

    // changes done by mari
    public function getFeedBackcount($platform, $teacher_id)
    {   
        $feedback = Feedback::where('teacher_id', $teacher_id)
                            ->where('feedback_by','=','parent')
                             ->where('view_status','=','0')->count();
        return api(['data' =>$feedback]);
    }

    // changes done by mari
    public function getFeedbackCountByStudent($platform, $student_id, $teacher_id)
    {   
        $feedback = Feedback::where('teacher_id', $teacher_id)
                            ->where('student_id', $student_id)
                            ->where('feedback_by','=','parent')
                            ->where('view_status','=','0')->count();
        return api(['data' =>$feedback]);
    }


    // public function getFeedBack($platform, $student_id)
    // {
    //     $feedback = Feedback::where('feedback.teacher_id', $this->teacher->id)
    //                         ->where('feedback.student_id', $student_id)
    //                         ->where('feedback.feedback_by','=','parent')
    //                         ->leftJoin('student', 'feedback.student_id', '=', 'student.id')
    //                         ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
    //                         ->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')
    //                         ->select
    //                         (
    //                             'feedback.id', 
    //                             'teacher.name as teacherName', 
    //                             'feedback.feedback', 
    //                             'feedback.date',
    //                             'feedback.feedback_by',
    //                             'student.name as studentName',
    //                             'student.roll_no',
    //                             'feedback.created_at',
    //                             'parent.name as parent_name'
    //                         )
    //                         ->orderBy('feedback.id', 'DESC')->get();
    //     return api(['data' => $feedback]);
    // }
    public function getFeedBack($platform, $student_id)
    {//by mari for v3
        
        $timestamp = time();
        $date=array();
        for ($i = 0 ; $i < 7 ; $i++) {
            array_push($date,date('d-m-Y', $timestamp));
            $timestamp -= 24 * 3600;
            }
        $feedback = Feedback::where('feedback.teacher_id', $this->teacher->id)
                            ->where('feedback.student_id', $student_id)
                            //->where('feedback.feedback_by','=','parent')
                            ->whereIn('date',$date)
                            //->where('feedback.view_status','=','0')
                            ->leftJoin('student', 'feedback.student_id', '=', 'student.id')
                            ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                            ->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')
                            ->select
                            (
                                'feedback.id', 
                                'teacher.name as teacherName', 
                                'feedback.feedback', 
                                'feedback.date',
                                'feedback.feedback_by',
                                'student.name as studentName',
                                'student.roll_no',
                                'feedback.created_at',
                                'feedback.view_status',
                                'parent.name as parent_name'
                            )
                            ->orderBy('feedback.id', 'DESC')->get();

            $update_view=Feedback::where('feedback.teacher_id','=', $this->teacher->id)
                            ->where('feedback.student_id','=', $student_id)
                            ->where('feedback.feedback_by','=','parent')
                            ->update(['view_status'=>1]);
        return api(['data' =>$feedback]);
    }


    public function postLeaveRequest(Leave $leave)
    {
        $request = \Request::all();
        $userError = ['student_id' => 'Student Id', 'leave_from' => 'Leave From Date in yyyy-mm-dd', 'leave_to' => 'Leave To Date in yyyy-mm-dd', 'request_by' => 'Request By', 'status' => 'Status'];
        $validator = \Validator::make($request, [
                'student_id'=>'required',
                'leave_from'=>'required|date_format:Y-m-d',
                'leave_to'=>'required|date_format:Y-m-d',
                'request_by'=>'required',
                'status' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            $leave = Leave::where('student_id', $request['student_id'])->where('to_leave', $request['leave_to'])->where('from_leave', $request['leave_from'])->first();
            if($leave)
            {
                return api()->notValid(['errorMsg'=>'Leave Request is already submitted']);
            }
            else
            {
                $request['remarks'] = (isset($request['remarks']) ? $request['remarks'] : "");
                Leave::insert([
                    'student_id' => $request['student_id'],
                    'from_leave' => $request['leave_from'],
                    'to_leave' => $request['leave_to'],
                    'status' => $request['status'],
                    'by_request' => $request['request_by'],
                    'remarks' => $request['remarks'],
                    'school_id' => $this->user->school_id
                ]);
                return api(['data'=>'Leave Request is Submitted Successfully']);
            }
        }
    }
    
    public function get_inbetween_date($from, $to) {
        $date_now = new DateTime();
        $begin = new DateTime($from);
        $end = new DateTime($to);
        $holidays = \DB::table('holiday')->where('school_id', \Auth::user()->school_id)->select('date')->get();
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)->where('active', '1')->first();
        $holiday=array();
        foreach ($holidays as $key => $value) {
            $holiday [] = new DateTime($value->date);
        }
        $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end->modify('+1 day'));
        $sessionStart = new DateTime($session->fromDate);
        $sessionEnd = new DateTime($session->toDate);

        foreach ($daterange as $date) {
            if ($date > $sessionStart && $date < $sessionEnd) {

                if ($date->format('N') != 7 && !in_array($date, $holiday)) {
                    $inbetween_date[] = $date->format("Y-m-d");
                }
            }
        }
        return $inbetween_date;
    }

    // public function updateLeaveRequest()
    // {
    //     $request = \Request::all();
    //     $userError = ['id' => 'Leave Id', 'status' => 'Status'];
    //     $validator = \Validator::make($request, [
    //             'id'=>'required|numeric',
    //             'status' => 'required'
    //         ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         $request['teacher_remarks'] = (isset($request['teacher_remarks']) ? $request['teacher_remarks'] : '');
    //         if($request['status'] == "approved"){
    //             $leave =Leave::where('id', $request['id'])->first();
    //             $student = Students::where('id',$leave->student_id)->first();
    //             $dates = $this->get_inbetween_date($request['from_date'], $request['to_date']);
    //             if($request['am']){
    //                 $attendance_session[]= 'am';
    //             }
    //             if($request['pm']){
    //                 $attendance_session[]= 'pm';
    //             }
    //             if(empty($dates)){
    //                 return api(['data' => 'This is holiday']);
    //             }
    //             foreach($dates as $date){
    //                 foreach($attendance_session as $session){
    //                     Attendance::insert([
    //                         'school_id' => $this->user->school_id,
    //                         'teacher_id' => $this->teacher->id,
    //                         'class_id' => $student->class_id,
    //                         'section_id' => $student->section_id,
    //                         'student_id' => $leave->student_id,
    //                         'attendance' => 'L',
    //                         'attendance_session' => $session,
    //                         'remarks' => $request['teacher_remarks'],
    //                         'date' => $date,
    //                         'attendance_by' => 'teacher'
    //                     ]);
    //                 }
    //             }
                
    //         }
    //         Leave::where('id', $request['id'])
    //             ->update(['status' => $request['status'], 
    //                 'teacher_remarks' => $request['teacher_remarks']
    //                 ]);
    //         return api(['data' => 'Leave is updated successfully']);
    //     }
    // }

    // changes done by parthiban 19-11-2017(sunday)
    // public function updateLeaveRequest()
    // {
    //     $request = \Request::all();
    //     $userError = ['id' => 'Leave Id', 'status' => 'Status'];
    //     $validator = \Validator::make($request, [
    //             'id'=>'required|numeric',
    //             'status' => 'required'
    //         ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         $request['teacher_remarks'] = (isset($request['teacher_remarks']) ? $request['teacher_remarks'] : '');
    //         if($request['status'] == "approved"){
    //             $leave =Leave::where('id', $request['id'])->first();
    //             $student = Students::where('id',$leave->student_id)->first();
    //             $dates = $this->get_inbetween_date($request['from_date'], $request['to_date']);
    //             if($request['am']){
    //                 $attendance_session[]= 'am';
    //             }
    //             if($request['pm']){
    //                 $attendance_session[]= 'pm';
    //             }
    //             if(empty($dates)){
    //                 return api(['data' => 'This is holiday']);
    //             }
    //             foreach($dates as $date){
    //                 foreach($attendance_session as $session){
    //                     $attend_is_exist=Attendance::where('school_id','=',$this->user->school_id)
    //                     ->where('class_id','=',$student->class_id)
    //                     ->where('section_id','=',$student->section_id)
    //                     ->where('student_id','=',$leave->student_id)
    //                     ->where('attendance_session','=',$session)
    //                     ->where('date','=',$date)
    //                     ->select('id')
    //                     ->first();
    //                     if(empty($attend_is_exist)){
    //                         Attendance::insert([
    //                             'school_id' => $this->user->school_id,
    //                             'teacher_id' => $this->teacher->id,
    //                             'class_id' => $student->class_id,
    //                             'section_id' => $student->section_id,
    //                             'student_id' => $leave->student_id,
    //                             'attendance' => 'L',
    //                             'attendance_session' => $session,
    //                             'remarks' => $request['teacher_remarks'],
    //                             'date' => $date,
    //                             'attendance_by' => 'teacher'
    //                         ]);
    //                     }else{
    //                         Attendance::where('school_id','=',$this->user->school_id)
    //                     ->where('class_id','=',$student->class_id)
    //                     ->where('section_id','=',$student->section_id)
    //                     ->where('student_id','=',$leave->student_id)
    //                     ->where('attendance_session','=',$session)
    //                     ->where('date','=',$date)
    //                     ->update(['attendance' => 'L','remarks' => $request['teacher_remarks']]);
    //                     }
    //                 }
    //             }
                
    //         }
    //         Leave::where('id', $request['id'])
    //             ->update(['status' => $request['status'], 
    //                 'teacher_remarks' => $request['teacher_remarks']
    //                 ]);
    //         return api(['data' => 'Leave is updated successfully']);
    //     }
    // }

    public function updateLeaveRequest()
    {
        $request = \Request::all();
        $userError = ['id' => 'Leave Id', 'status' => 'Status'];
        $validator = \Validator::make($request, [
                'id'=>'required|numeric',
                'status' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            $request['teacher_remarks'] = (isset($request['teacher_remarks']) ? $request['teacher_remarks'] : '');
            if($request['status'] == "approved"){
                $leave =Leave::where('id', $request['id'])->first();
                $student = Students::where('id',$leave->student_id)->first();
                $dates = $this->get_inbetween_date($request['from_date'], $request['to_date']);
                if($request['am']){
                    $attendance_session[]= 'am';
                }
                if($request['pm']){
                    $attendance_session[]= 'pm';
                }
                if(empty($dates)){
                    return api(['data' => 'This is holiday']);
                }
                foreach($dates as $date){
                    foreach($attendance_session as $session){
                        $attend_is_exist=Attendance::where('school_id','=',$this->user->school_id)
                        ->where('class_id','=',$student->class_id)
                        ->where('section_id','=',$student->section_id)
                        ->where('student_id','=',$leave->student_id)
                        ->where('attendance_session','=',$session)
                        ->where('date','=',$date)
                        ->select('id')
                        ->first();
                        
                        if(empty($attend_is_exist)){
                            Attendance::insert([
                                'school_id' => $this->user->school_id,
                                'teacher_id' => $this->teacher->id,
                                'class_id' => $student->class_id,
                                'section_id' => $student->section_id,
                                'student_id' => $leave->student_id,
                                'attendance' => 'L',
                                'attendance_session' => $session,
                                'remarks' => $request['teacher_remarks'],
                                'date' => $date,
                                'attendance_by' => 'teacher'
                            ]);
                        }else{
                                Attendance::where('school_id','=',$this->user->school_id)
                                    ->where('class_id','=',$student->class_id)
                                    ->where('section_id','=',$student->section_id)
                                    ->where('student_id','=',$leave->student_id)
                                    ->where('attendance_session','=',$session)
                                    ->where('date','=',$date)
                                ->update(['attendance' => 'L','remarks' => $request['teacher_remarks']]);
                            
                        }
                    }   
                }
                
            }
            if($request['status']=='cancelled'){//for delete attendance when cancelled
                $dates = $this->get_inbetween_date($request['from_date'], $request['to_date']);
                $leave =Leave::where('id', $request['id'])->first();
                $student = Students::where('id',$leave->student_id)->first();
                //$from_date=date('Y-m-d',strtotime($request['from_date']));
                //$to_date=date('Y-m-d',strtotime($request['to_date']));
                foreach($dates as $date){
                    if($request['am']){
                        $is_taken_am=\DB::table('attendance_status')->where('school_id','=',$this->user->school_id)
                        ->where('class_id','=',$student->class_id)
                        ->where('section_id','=',$student->section_id)
                        ->where('attendance_session','=','am')
                        ->where('date','=',$date)
                        ->first();
                        if(empty($is_taken_am)){
                            Attendance::where('school_id','=',$this->user->school_id)
                            ->where('class_id','=',$student->class_id)
                            ->where('section_id','=',$student->section_id)
                            ->where('student_id','=',$leave->student_id)
                            ->where('attendance','=','L')
                            ->where('attendance_session','=','am')
                            ->where('date','=',$date)
                            ->delete();
                        }
                    //dd($dd);  
                    }
                    if($request['pm']){
                        $is_taken_pm=\DB::table('attendance_status')->where('school_id','=',$this->user->school_id)
                        ->where('class_id','=',$student->class_id)
                        ->where('section_id','=',$student->section_id)
                        ->where('attendance_session','=','pm')
                        ->where('date','=',$date)
                        ->first();
                        if(empty($is_taken_pm)){
                            Attendance::where('school_id','=',$this->user->school_id)
                                ->where('class_id','=',$student->class_id)
                                ->where('section_id','=',$student->section_id)
                                ->where('student_id','=',$leave->student_id)
                                ->where('attendance_session','=','pm')
                                ->where('attendance','=','L')
                                ->where('date','=',$date)
                            ->delete();
                        }
                        
                    }
                }
            }
            Leave::where('id', $request['id'])
                ->update(['status' => $request['status'], 
                    'teacher_remarks' => $request['teacher_remarks']
                    ]);
            return api(['data' => 'Leave is updated successfully']);
        }
    }     
    
    // changes done by parthiban 19-11-2017(sunday)
    // public function getLeaveRequests()
    // {
    //     $students = Students::where('class_id', $this->teacher->class)
    //             ->where('section_id', $this->teacher->section)
    //             ->get();
    //     foreach($students as $student)
    //     {
    //         $leaves = Leave::where('student_id', $student->id)->where('status', 'process')->select('id', 'from_leave', 'to_leave', 'status', 'by_request','remarks','teacher_remarks','attendance_session')->get();
    //         if(count($leaves)>0){
    //             foreach($leaves as $key => $value){
    //                 $value->attendance_session = json_decode($value->attendance_session);
    //             }
    //             $leaveArr[] = array
    //                             (
    //                                 'student_name' => $student->name,
    //                                 'roll_no' => $student->roll_no,     
    //                                 'leaves' => $leaves
    //                             );
    //         }
    //     }
    //     return api(['data' => $leaveArr]);
    // }

    public function getLeaveRequests()
    {
        $students = Students::where('class_id', $this->teacher->class)
                ->where('section_id', $this->teacher->section)
                ->get();
        foreach($students as $student)
        {   $leaves = Leave::where('student_id', $student->id)->where('status','!=', 'cancelled')
                ->where('from_leave','>=',date('Y-m-d'))
                // ->where('to_leave','<=',date('d-m-Y'))
                ->select('id','from_leave', 'to_leave', 'status', 'by_request','remarks','teacher_remarks','attendance_session')->get();
            if(count($leaves)>0){
                foreach($leaves as $key => $value){
                    $value->attendance_session = json_decode($value->attendance_session);
                }
                $leaveArr[] = array
                                (
                                    'student_name' => $student->name,  
                                    'roll_no' => $student->roll_no,     
                                    'leaves' => $leaves
                                );
            }
        }
        return api(['data' => $leaveArr]);
    }

    // new action added by parthiban 30-11-2017

    public function leaveRequestcount()
    {
        $students = Students::where('class_id', $this->teacher->class)
                ->where('section_id', $this->teacher->section)
                ->get();
        foreach($students as $student)
        {   $leaves = Leave::where('student_id', $student->id)->where('status','=', 'process')
                ->whereDate('from_leave','>=',date('Y-m-d'))
                //->where('to_leave','<=',date('d-m-Y'))
                ->select('id','from_leave', 'to_leave', 'status', 'by_request','remarks','teacher_remarks','attendance_session')->get();
            if(count($leaves)>0)
            {
                foreach($leaves as $key => $value){
                    $value->attendance_session = json_decode($value->attendance_session);
                }
                $leaveArr[] = array
                                (
                                    'student_name' => $student->name,  
                                    'roll_no' => $student->roll_no,     
                                    'leaves' => $leaves
                                );
            }
        }
        return api(['data' => $leaveArr]);
    }     

    // public function postResult(Result $result)
    // {   
    //     $request = \Request::all();
    //     $userError = [ 
    //             'class'=>'Class',
    //             'section'=>'Section',
    //             'exam_type_id' => 'Exam Type Id',
    //             'month' => 'Month',
    //             'subject_id' => 'Subject Id', 
    //             'student_marks' => 'Student Marks With Id',
    //             'date' => 'Date',
    //             'max_marks' => 'Maximum Marks',
    //             //'pass_marks' => 'Passing Marks'
    //     ];
    //     $validator = \Validator::make($request, [ 
    //             'class'=>'required',
    //             'section'=>'required',
    //             'exam_type_id' => 'required|numeric',
    //             'month' => 'required',
    //             'subject_id' => 'required|numeric', 
    //             'student_marks' => 'required',
    //             'date' => 'required|date_format:d-m-Y',
    //             'max_marks' => 'required|numeric',
    //             //'pass_marks' => 'required|numeric'
    //     ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         if(!is_array($request['student_marks']))
    //             return \api::notValid(['errorMsg' => 'Students Marks must be an array']);

    //         foreach($request['student_marks'] as $marks)
    //         {
    //             // dd($marks);
    //             $marksdata = $marks;
    //             // dd($marksdata);
    //             $check = Result::where('class_id', $request['class'])
    //                     ->where('section_id', $request['section'])
    //                     ->where('exam_type_id', $request['exam_type_id'])
    //                     ->where('subject_id', $result['subject_id'])
    //                     ->where('month', $request['month'])
    //                     ->where('student_id', $marksdata['student_id'])
    //                     ->first();
    //             if($check)
    //             {
    //                 Result::where('id', $check->id)->update([
    //                     'date' => $request['date'],
    //                     'max_marks' => $request['max_marks'],
    //                     'pass_marks' => '0',
    //                     'obtained_marks' => $marksdata['marks'],
    //                     'result' => $marksdata['result'],
    //                     'grade' => $marksdata['grade']
    //                 ]);
    //             }
    //             else
    //             {
    //                 Result::insert([
    //                     'class_id' => $request['class'],
    //                     'section_id' => $request['section'],
    //                     'exam_type_id' => $request['exam_type_id'],
    //                     'month' => $request['month'],
    //                     'subject_id' => $request['subject_id'],
    //                     'student_id' => $marksdata['student_id'],
    //                     'teacher_id' => $this->teacher->id,
    //                     'date' => $request['date'],
    //                     'max_marks' => $request['max_marks'],
    //                     'pass_marks' => '0',
    //                     'obtained_marks' => $marksdata['marks'],
    //                     'result' => $marksdata['result'],
    //                     'grade' => $marksdata['grade'],
    //                     'result_by' => 'teacher'
    //                 ]);
    //             }
    //         }
    //         return \api(['data' => 'Result is added successfully']);
    //     }
    // }

    // changes done by parthiban 19-11-2017(sunday)
    // public function postResult(Result $result)
    // {   
    //     $request = \Request::all();
    //     $userError = [ 
    //             'class'=>'Class',
    //             'section'=>'Section',
    //             'exam_type_id' => 'Exam Type Id',
    //             'month' => 'Month',
    //             'subject_id' => 'Subject Id', 
    //             'student_marks' => 'Student Marks With Id',
    //             'date' => 'Date',
    //             'max_marks' => 'Maximum Marks',
    //             'pass_marks' => 'Passing Marks'
    //     ];
    //     $validator = \Validator::make($request, [ 
    //             'class'=>'required',
    //             'section'=>'required',
    //             'exam_type_id' => 'required|numeric',
    //             'month' => 'required',
    //             'subject_id' => 'required|numeric', 
    //             'student_marks' => 'required',
    //             'date' => 'required|date_format:d-m-Y',
    //             'max_marks' => 'required|numeric',
    //             'pass_marks' => 'required|numeric'
    //     ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         if(!is_array($request['student_marks']))
    //             return \api::notValid(['errorMsg' => 'Students Marks must be an array']);

    //         foreach($request['student_marks'] as $marks)
    //         {
    //             $marksdata = $marks;
    //             $check = Result::where('class_id', $request['class'])
    //                     ->where('section_id', $request['section'])
    //                     ->where('exam_type_id', $request['exam_type_id'])
    //                     ->where('subject_id', $result['subject_id'])
    //                     ->where('month', $request['month'])
    //                     ->where('student_id', $marksdata['student_id'])
    //                     ->first();
    //             if($check)
    //             {
    //                 Result::where('id', $check->id)->update([
    //                     'date' => $request['date'],
    //                     'max_marks' => $request['max_marks'],
    //                     'pass_marks' => $request['pass_marks'],
    //                     'obtained_marks' => $marksdata['marks'],
    //                     'result' => $marksdata['result'],
    //                     'grade' => $marksdata['grade']
    //                 ]);
    //             }
    //             else
    //             {   $rank="";
    //                 if(isset($marksdata['rank'])&&$marksdata['rank']!='')
    //                 {
    //                     $rank=$marksdata['rank'];
    //                 }
    //             /*** updated 31-10-2017
    //               *
    //               *Result::insert([
    //                     'class_id' => $request['class'],
    //                     'section_id' => $request['section'],
    //                     'exam_type_id' => $request['exam_type_id'],
    //                     'month' => $request['month'],
    //                     'subject_id' => $request['subject_id'],
    //                     'student_id' => $marksdata['student_id'],
    //                     'teacher_id' => $this->teacher->id,
    //                     'date' => $request['date'],
    //                     'max_marks' => $request['max_marks'],
    //                     'pass_marks' => $request['pass_marks'],
    //                     'obtained_marks' => $marksdata['marks'],
    //                     'result' => $marksdata['result'],
    //                     'grade' => $marksdata['grade'],
    //                     'rank'=>$rank,
    //                     'result_by' => 'teacher'
    //                 ]);
    //                 */

    //                 $getTotalStudent =\DB::table('student')
    //                 ->where('class_id',$request['class'])
    //                 ->where('section_id',$request['section'])
    //                 ->count();

    //                 Result::insert([
    //                     'class_id' => $request['class'],
    //                     'school_id' => $this->user->school_id,
    //                     'total_students' => $getTotalStudent,
    //                     'section_id' => $request['section'],
    //                     'exam_type_id' => $request['exam_type_id'],
    //                     'month' => $request['month'],
    //                     'subject_id' => $request['subject_id'],
    //                     'student_id' => $marksdata['student_id'],
    //                     'teacher_id' => $this->teacher->id,
    //                     'date' => $request['date'],
    //                     'max_marks' => $request['max_marks'],
    //                     'pass_marks' => $request['pass_marks'],
    //                     'obtained_marks' => $marksdata['marks'],
    //                     'result' => $marksdata['result'],
    //                     'grade' => $marksdata['grade'],
    //                     'rank'=>$rank,
    //                     'result_by' => 'teacher'
    //                 ]);


    //                 /****** end *****/
    //             }
    //         }
    //         return \api(['data' => 'Result is added successfully']);
    //     }
    // }

    public function postResult(Result $result)
    {   
        $request = \Request::all();
        $userError = [ 
                'class'=>'Class',
                'section'=>'Section',
                'exam_type_id' => 'Exam Type Id',
                'month' => 'Month',
                'subject_id' => 'Subject Id', 
                'student_marks' => 'Student Marks With Id',
                'date' => 'Date',
                'max_marks' => 'Maximum Marks',
                'pass_marks' => 'Passing Marks'
        ];
        $validator = \Validator::make($request, [ 
                'class'=>'required',
                'section'=>'required',
                'exam_type_id' => 'required|numeric',
                'month' => 'required',
                'subject_id' => 'required|numeric', 
                'student_marks' => 'required',
                'date' => 'required|date_format:d-m-Y',
                'max_marks' => 'required|numeric',
                'pass_marks' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            if(!is_array($request['student_marks']))
                return \api::notValid(['errorMsg' => 'Students Marks must be an array']);
    
            foreach($request['student_marks'] as $marks)
            {
                $marksdata = $marks;
                $total_students=Students::where('class_id',$request['class'])
                 ->where('section_id',$request['section'])
                 ->count();
          
                $school_id=\DB::table('class')->where('id','=',$request['class'])->first();
                $check = Result::where('class_id', $request['class'])
                        ->where('section_id', $request['section'])
                        ->where('exam_type_id', $request['exam_type_id'])
                        ->where('subject_id', $result['subject_id'])
                        ->where('month', $request['month'])
                        ->where('student_id', $marksdata['student_id'])
                        ->first();
                if($check)
                {
                    Result::where('id', $check->id)->update([
                        'date' => $request['date'],
                        'max_marks' => $request['max_marks'],
                        'pass_marks' => $request['pass_marks'],
                        'obtained_marks' => $marksdata['marks'],
                        'result' => $marksdata['result'],
                        'grade' => $marksdata['grade']
                    ]);
                }
                else
                {   $rank="";
                    if(isset($marksdata['rank'])&&$marksdata['rank']!=''){
                        $rank=$marksdata['rank'];
                    }
                    Result::insert([
                        'class_id' => $request['class'],
                        'section_id' => $request['section'],
                        'exam_type_id' => $request['exam_type_id'],
                        'month' => $request['month'],
                        'subject_id' => $request['subject_id'],
                        'student_id' => $marksdata['student_id'],
                        'total_students'=>$total_students,
                        'school_id'=>$school_id->school_id,
                        'teacher_id' => $this->teacher->id,
                        'date' => $request['date'],
                        'max_marks' => $request['max_marks'],
                        'pass_marks' => $request['pass_marks'],
                        'obtained_marks' => $marksdata['marks'],
                        'result' => $marksdata['result'],
                        'grade' => $marksdata['grade'],
                        'rank'=>$rank,
                        'result_by' => 'teacher'
                    ]);
                }
            }
            return \api(['data' => 'Result is added successfully']);
        }
    }

    public function getResults(Result $result, $platform, $examid)
    {
        return $result->doGetResultsByTeacherAPI($this->user, $this->teacher, $examid);
    }

    public function updateResult(Result $result)
    {   
        $request = \Request::all();
        $userError = [ 
                'id' => 'Result Id',
                'exam_type_id' => 'Exam Type Id',
                'month' => 'Month Id',
                'subject_id' => 'Subject Id', 
                'student_marks' => 'Student Marks With Id',
                'date' => 'Date',
                'max_marks' => 'Maximum Marks',
                'pass_marks' => 'Passing Marks'
        ];
        $validator = \Validator::make($request, [ 
                'id' => 'required|numeric',
                'exam_type_id' => 'required|numeric',
                'month' => 'required',
                'subject_id' => 'required|numeric', 
                'student_marks' => 'required|json',
                'date' => 'required|date_format:d-m-Y',
                'max_marks' => 'required|numeric',
                'pass_marks' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $result->doPostResultByTeacher($request, $this->user, $this->teacher);
    }

    public function getStaff()
    {
        $staffs = Staff::where('school_id', $this->user->school_id)->get();
        return api(['data' => $staffs]);
    }

    public function getEmployees($platform, $id)
    {
        $teachers = Employee::where('type', $id)->get();
        return api(['data' => $teachers]);
    }
    
    public function postTimeTable(Request $request)
    {
        $userError = ['subject_id' => 'Subject', 'period' => 'Period', 'start_time' => 'Start Time', 
        'end_time' => 'End Time',  'day' => 'Day', 'teacher_id' => 'Teacher'];
        $validator = \Validator::make($request->all(), [
                'subject_id' => 'required',
                'period' => 'required',
                'day' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'teacher_id' => 'required',
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            $timeTableExists = TimeTable::where('school_id', $this->user->school_id)
                        ->where('class_id', $this->teacher->class)
                        ->where('section_id', $this->teacher->section)
                        ->where('subject_id', $request['subject'])
                        ->where('day', $request['day'])
                        ->where('period', $request['period'])
                        ->where('start_time', $request['start_time'])
                        ->first();
            $subjectExist = TimeTable::where('school_id', $this->user->school_id)
                            ->where('class_id', $this->teacher->class)
                            ->where('section_id', $this->teacher->section)
                            ->where('subject_id', $request['subject'])
                            ->where('day', $request['day'])
                            ->first();
            if($timeTableExists)
            {
                return \api::notValid(['errorMsg' => 'Time Table already exists']);
            }
            if($subjectExist)
            {
               return \api::notValid(['errorMsg' => 'Time Table already exists']);
            }
            else
            {
                    TimeTable::insert([
                    'school_id' => $this->user->school_id,
                    'class_id' => $this->teacher->class,
                    'section_id' => $this->teacher->section,
                    'subject_id' => $request['subject_id'],
                    'period' => $request['period'],
                    'day' => $request['day'],
                    'start_time' => $request['start_time'],
                    'end_time' => $request['end_time'],
                    'teacher_id' => $request['teacher_id']
                ]);
                return \api(['data' => 'Time table is added successfully']);
            }
        }
    }

    public function getNotice()
    {
        $notices = \DB::table('notice')->where('type', 'teacher')->orderBy('id', 'DESC')->get();
        return api(['data' => $notices]);
    }

    // changes done by parthiban 19-11-2017(sunday)

    // public function getTimeTables()
    // {
    //     $time = TimeTable::where('teacher_id', $this->teacher->id)
    //                     ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
    //                     ->join('class', 'time-table.class_id', '=', 'class.id')
    //                     ->join('section', 'time-table.section_id', '=', 'section.id')
    //                     ->select('time-table.id', 'time-table.period', 'time-table.day', 'time-table.start_time', 'time-table.end_time', 'subject.subject', 'class.class','section.section')
    //                     ->get();
    //     return api(['data' => $time]);
    // }

    // changes done by parthiban 17-11-2017
    public function getTimeTables()
    {
        $daysArray = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $finalArray = [];        
        $periods = ["1","2","3","4","5","6","7","8","9","10"];
        foreach ($daysArray as $k => $days) {
            foreach ($periods as $p => $period) {            
                $timeTablesForDays = TimeTable::where('teacher_id', $this->teacher->id)
                ->where('day', $days)
                ->where('period', $period)
                ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                ->join('class', 'time-table.class_id', '=', 'class.id')
                ->join('section', 'time-table.section_id', '=', 'section.id')
                ->select('time-table.id', 'time-table.period', 'time-table.day', 'time-table.start_time', 'time-table.end_time', 'subject.subject', 'class.class','section.section')
                ->first();
                $finalArray[$days][$period] = $timeTablesForDays;
            }
        }                        
        return api(['data' => $finalArray]);
    }    

    // changes done by parthiban 19-11-2017(sunday)
    // public function getSectionSubject($flag, $id)
    // {
    //     $section = \DB::table('section')->where('id', $id)->first();
    //     $subjects = \DB::table('subject')->whereIn('id', json_decode($section->subjects))->get();
    //     if(!$subjects)
    //         return api()->notFound(['errorMsg'=>'not enough subject']);
    //     return api(['data'=>$subjects]);
    // }

    public function getSectionSubject($flag, $teacher_id,$sec_id)
    {   
        $subArry = [];
        $timeTables = TimeTable::where('teacher_id', $teacher_id)->where('section_id',$sec_id)->get();      
        foreach ($timeTables as $key => $timeTable) {
            $subArry[] = $timeTable->subject_id;
        }
        $subjects = \DB::table('subject')->whereIn('id', $subArry)->get();
        if(!$subjects)
            return api()->notFound(['errorMsg'=>'not enough subject']);
        return api(['data'=>$subjects]);        
    }

    // new action added by parthiban 19-11-2017(sunday)
    public function getFeedbackStudents($platform,$class_id,$section_id)
    {   
        $students = Students::where('class_id', $class_id)
            ->where('school_id', $this->user->school_id)
            ->where('section_id', $section_id)
            ->select('id', 'name', 'roll_no', 'avatar')
            ->orderBy('student.roll_no', 'ASC')
            ->get();
        return api(['data' => $students]);
    }

    // new action added by parthiban 19-11-2017(sunday)
    public function getSubjectsForMark($platform,$teacher_id,$sec_id,$exam_id)
     {
        // $section = Section::where('id',$secid)->first();
        $stu_count=\DB::table('student')
        ->where('section_id','=',$sec_id)
       // ->where('class_id','=',$this->teacher->class)
        ->count();

        // $sub=json_decode($section->subjects);
        $subArry = [];
        $timeTables = TimeTable::where('teacher_id', $teacher_id)->where('section_id',$sec_id)->get();      
        foreach ($timeTables as $key => $timeTable) {
            $subArry[] = $timeTable->subject_id;
        }

     foreach($subArry as $sub_key=>$value){
            $reslt_count=\DB::table('result')
           // ->where('class_id','=',$this->teacher->class)
            ->where('section_id','=',$sec_id)
            ->where('exam_type_id','=',$exam_id)
            ->where('subject_id','=',$value)->count();
            $res_countarr[]=$reslt_count;
            if($reslt_count>0){
                if(($key = array_search($value, $subArry)) !== false) {
                    unset($subArry[$key]);
                    $subArry= array_values($subArry);
                }
            }
        }
       
        $min_max=\DB::table('exam')->where('id','=',$exam_id)->select('pass_marks','max_marks')->first();
        $subjects = Subject::whereIn('id', $subArry)->get();
        $data=array('subjects'=>$subjects,'pass_marks'=>$min_max->pass_marks,'max_marks'=>$min_max->max_marks);
        return api(['data' =>$data]);
    }         
}