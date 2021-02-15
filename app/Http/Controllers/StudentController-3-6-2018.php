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
use App\FeeFrequency;
use App\FeeStructure;
use App\FeeSummary;
use App\Feedback;
use App\Gallery;
use App\Holiday;
use App\Homework;
use App\Leave;
use App\Library;
use App\NotificationType;
use App\Post;
use App\Report;
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

class StudentController extends Controller
{
    protected $user;
    
    public function __construct()
    {
        if(\Auth::check())
        {
            $this->user = \Auth::user();
    
            $classes = addClass::where('school_id', $this->user->school_id)->get();
            $sessions = Session::where('active', 1)->where('school_id', $this->user->school_id)->get();
            $castes = Caste::where('school_id', $this->user->school_id)->get();
            $religions = Religion::where('school_id', $this->user->school_id)->get();
            $buses = Bus::where('school_id', $this->user->school_id)->get();
            $school_image = School::where('id', \Auth::user()->school_id)->first();

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

            $userplans=[];
            
            if(!$school_image->userplan)
            {
                $school_image->userplan='Basic';
            }

            if($school_image->userplan){
                    
                    $userplandetail= \DB::table('schooluser_plan')->where($school_image->userplan, 1)->select('Modules')->get();
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
              
                 if($userplansadded)
                     {
                        foreach ($userplansadded as $key => $value) {
                            array_push($userplans, $userplansadded[$key]->Modules);
                        }
                     }
            }


            \View::share(compact('classes', 'sessions', 'castes', 'religions', 'groups', 'buses', 'school_image', 'roler','userplans'));
        }
    }

    public function masterStudent()
    {
        return view('users.students.student');
    }

    public function postStopRoutes()
    {
        $bus_id = \Request::get('bus_id');
        $stops = BusStop::where('bus_id', $bus_id)->get();
        return $stops;
    }

   /*
    Updated by priya
    public function getStudents()
    {
        $class = \Request::get('class');
        $section = \Request::get('section');
        if($class and $section)
        {
            $classData = addClass::where('id', $class)->first();
            $sectionData = Section::where('id', $section)->where('class_id', $class)->first();
            $students = Students::where('student.class_id', $class)
                        ->where('student.section_id', $section)
                        ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                        ->leftJoin('users', 'student.user_id', '=', 'users.id')
                        ->select
                        (
                            'student.id',
                            'student.name',
                            'student.roll_no',
                            'student.registration_no',
                            'student.gender',
                            'parent.mobile as parent_contact_no',
                            'users.username',
                            'users.hint_password as hint_password',
                            \DB::RAW("(select username from users where id=parent.user_id) as parent_username"),
                            \DB::RAW("(select hint_password from users where id=parent.user_id) as parent_hint_password"),
                            'student.avatar'
                        )
                        ->where('student.school_id', $this->user->school_id)
                        ->get();
        }
        else
        {
            $students = Students::leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                        ->leftJoin('users', 'student.user_id', '=', 'users.id')
                        ->select
                        (
                            'student.id',
                            'student.name',
                            'student.roll_no',
                            'student.registration_no',
                            'student.parent_id',    
                            'student.gender',
                            'parent.mobile as parent_contact_no',
                            'parent.user_id',
                            'users.username',
                            'users.hint_password as hint_password',
                            \DB::RAW("(select username from users where id=parent.user_id) as parent_username"),
                            \DB::RAW("(select hint_password from users where id=parent.user_id) as parent_hint_password"),
                            'student.avatar'
                        )
                        ->where('student.school_id', $this->user->school_id)
                        ->get();
            $sectionData = '';
            $classData = '';
        }
        return view('users.students.list', compact('students', 'sectionData', 'classData'));
    }*/

     public function getStudents()
    {
        $class = \Request::get('class');
        $section = \Request::get('section');
        if($class and $section)
        {
            $classData = addClass::where('id', $class)->first();
            $sectionData = Section::where('id', $section)->where('class_id', $class)->first();
            $students = Students::where('student.class_id', $class)
                ->where('student.section_id', $section)
                ->join('class', 'student.class_id', '=', 'class.id')//updated
                ->join('section', 'student.section_id', '=', 'section.id')//updated
                ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                ->leftJoin('users', 'student.user_id', '=', 'users.id')
                ->select
                (
                    'student.*',
                    'parent.mobile as parent_contact_no',
                    'users.username',
                    'class.class',
                    'section.section',
                    'parent.name as father',
                    'parent.mother',
                    'parent.address',
                    'users.hint_password as hint_password',
                    \DB::RAW("(select username from users where id=parent.user_id) as parent_username"),
                    \DB::RAW("(select hint_password from users where id=parent.user_id) as parent_hint_password"),
                    'student.avatar'
                )
                ->where('student.school_id', $this->user->school_id)
                ->get();
                $currentDate = date("d-m-Y_H_i_s");

        \Excel::create("studentExport_".$currentDate, function($excel) use ($students, $sectionData, $classData)
        {

            $excel->sheet('Excel sheet', function($sheet) use ($students, $sectionData, $classData) {
                $sheet->loadView('users.students.studentDetailedReport')->with('students', $students)->with('sectionData', $sectionData)->with('classData', $classData);
                $sheet->setOrientation('portrait');
            });
        })->store('xls', storage_path('studentDetailedReport'));

        $fileURL = storage_path() . "/studentDetailedReport/studentExport_" . $currentDate . '.xls';
        \Session::put('getReportUrl', $fileURL);
        
        }
        /*else
        {
            $students = Students::leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                ->join('class', 'student.class_id', '=', 'class.id')//updated
                ->join('section', 'student.section_id', '=', 'section.id')//updated
                ->leftJoin('users', 'student.user_id', '=', 'users.id')
                ->select
                (
                    'student.*',
                    'parent.mobile as parent_contact_no',
                    'users.username',
                    'class.class',
                    'section.section',
                    'parent.name as father',
                    'parent.mother',
                    'parent.user_id',
                    'users.username',
                    'parent.address',
                    'users.hint_password as hint_password',
                    \DB::RAW("(select username from users where id=parent.user_id) as parent_username"),
                    \DB::RAW("(select hint_password from users where id=parent.user_id) as parent_hint_password"),
                    'student.avatar'
                )
                ->where('student.school_id', $this->user->school_id)
                ->get();
            $sectionData = '';
            $classData = '';
        }*/

        return view('users.students.list', compact('students', 'sectionData', 'classData'));
    }

    public function viewStudent($id)
    {
        $student = Students::where('student.id', $id)
                    ->leftJoin('session', 'student.session_id', '=', 'session.id')
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')
                    ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                    ->leftJoin('caste', 'student.caste_id', '=', 'caste.id')
                    ->leftJoin('religion', 'student.religion', '=', 'religion.id')
                    ->leftJoin('bus', 'student.bus_id', '=', 'bus.id')
                    ->select
                    (
                        'student.id',
                        'session.session',
                        'class.class',
                        'section.section',
                        'section.subjects',
                        'caste.caste',
                        'religion.religion',
                        'student.blood_group',
                        'bus.bus_no',
                        'bus.route',
                        'parent.state',
                        'parent.city',
                        'parent.address',
                        'parent.pin_code',
                        'parent.avatar as parent_image',
                        'parent.name as father_name',
                        'parent.mother as mother_name',
                        'parent.email as parent_email',
                        'parent.mobile as parent_contact_no',
                        'parent.father_occupation',
                        'parent.mother_occupation',
                        'student.registration_no',
                        'student.roll_no',
                        'student.name',
                        'student.dob',
                        'student.date_of_admission',
                        'student.date_of_joining',
                        'student.gender',
                        'student.nationality',
                        'student.contact_no',
                        'student.email',
                        'student.previous_school',
                        'student.avatar',
                        'student.documents',
                        'student.aadhar_no',
                        'student.emi_no',
                        'student.rte'
                    )
                    ->first();
        $subjects = Subject::whereIn('id', json_decode($student->subjects))->select('subject')->get();
        $subs = [];
        foreach($subjects as $subject)
        {
            $subs[] = $subject->subject;
        }
        $allsubs = implode(", ", $subs);
        //return view('users.students.view', compact('student', 'allsubs'));
        /*updated 17-1-2018*/
        $currentSchool = \DB::table('school')->where('id',\Auth::user()->school_id)->first();

        return view('users.students.view', compact('student', 'allsubs','currentSchool'));
    }

    public function postStudent(Students $student)
    {
        $input = \Request::all();
        // dd($input);
        $userError = [
                'session_id' => 'Session',
                'registration_no' => 'Registration No',
                'class' => 'Class',
                'section' => 'Section',
                'bus_id' => 'Bus',
                'roll_no' => 'Roll No',
                'date_of_admission' => 'Date Of Admission',
                'date_of_joining' => 'Date Of Joining',
                'name' => 'Student Name',
                'gender' => 'Gender',
                'caste' => 'Caste',
                'dob' => 'Date Of Birth',
                'blood_group' => 'Blood Group',
                'religion' => 'Religion',
                'contact_no' => 'Contact Number',
                'address' => 'Address',
                'avatar' => 'Avatar',
                'father_name' => 'Father Name',
                'mother_name' => 'Mother Name',
                'parent_contact_no' => 'Parent Contact Number'
            ];
        $validator = \Validator::make($input, [
                'session_id' => 'required|numeric',
                'registration_no' => 'required',
                'class' => 'required|numeric',
                'section' => 'required|numeric',
                'roll_no' => 'required|numeric',
                'date_of_admission' => 'required|date|date_format:Y-m-d',
                'date_of_joining' => 'required|date|date_format:Y-m-d',
                'name' => 'required',
                'gender' => 'required',
                'caste' => 'required|numeric',
                'dob' => 'required|date|date_format:Y-m-d',
                'blood_group' => 'required',
                'religion' => 'required',
                'father_name' => 'required',
                'mother_name' => 'required',
                'parent_contact_no' => 'required|regex:/^\d{10}$/'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return Redirect::back()->withErrors($validator)->withInput($input);
        return $student->doPostStudent($input, $this->user);
    }

    public function editStudent($id)
    {
        $student = Students::where('student.id', $id)
                    ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                    ->select
                    (
                        'student.id', 
                        'student.aadhar_no',
                        'student.emi_no',
                        'student.rte',
                        'student.session_id',
                        'student.class_id', 
                        'student.section_id',
                        'student.caste_id',
                        'student.blood_group',
                        'student.religion',
                        'student.bus_id',
                        'student.bus_stop_id',
                        'student.registration_no',
                        'student.roll_no',
                        'student.name',
                        'student.dob',
                        'student.date_of_admission',
                        'student.date_of_joining',
                        'student.gender',
                        'student.nationality',
                        'student.contact_no',
                        'student.email',
                        'student.previous_school',
                        'student.avatar',
                        'student.pick_time',
                        'student.drop_time',
                        'student.documents',
                        'parent.state',
                        'parent.name as father_name',
                        'parent.mother as mother_name',
                        'parent.mobile as parent_contact_no',
                        'parent.email as parent_email',
                        'parent.father_occupation',
                        'parent.mother_occupation',
                        'parent.city',
                        'parent.address',
                        'parent.pin_code',
                        'parent.avatar as parent_image'
                    )
                    ->first();
        $sections = Section::where('class_id', $student->class_id)->get();
        $stops = BusStop::where('bus_id', $student->bus_id)->get();
        return view('users.students.edit', compact('student', 'sections', 'stops'));
    }

    public function deleteStudent($id)
    {
       $student = Students::where('id', $id)->first();
       $fee_structure=\DB::table('fee_structure')
            ->where('fee_structure.school_id','=',\Auth::user()->school_id)
            ->where('fee_structure.class_id','=',$student->class_id)
            // ->where('fee_structure.payment_type','=','MONTHLY')
            ->join('session', 'fee_structure.school_id', '=','session.school_id')
            ->where('session.active','=','1')
            ->select('fee_structure.class_id','fee_structure.id as feeid','session.id as sessid','fee_structure.payment_type','fee_structure.amount')
            ->get();
        $totalamt=0;
        $feeids=array();
        if(!empty($fee_structure)){
            foreach($fee_structure as $key){
                array_push($feeids,$key->feeid);
                if($key->payment_type=="MONTHLY"){
                   $totalamt=$totalamt+($key->amount*12);
                 }
                else{
                    $totalamt=$totalamt+$key->amount;
                }
            }
            $payment_list=\DB::table('payment')
                ->whereIn('fee_id',$feeids)
                ->where('school_id','=',\Auth::user()->school_id)
                ->where('student_id','=',$id)->get();
            $paid=0;
             $deduction=0;
            if(!empty($payment_list)){
                foreach($payment_list as $pay_key){
                    $paid=$pay_key->amount+$paid;
                    $deduction=$pay_key->concession+$deduction;
                }
                $totalpaid=$paid+$deduction;
            }else{
                $totalpaid=0;
            }
        }
        else{
            $totalamt=0;
             $totalpaid=0;
        }
        $check_lib=\DB::table('issue')->where('student_id','=',$id)
                ->where('return_flag','=','0')->count();
        if($check_lib==0){
            if($totalpaid>=$totalamt){
               
                \DB::table('users')->where('id', $student->user_id)->delete();
                Students::where('id', $id)->delete();
                $input['success'] = 'Student is deleted successfully';
                return \Redirect::back()->withInput($input);
            }else{
                $input['error'] = 'Student has payment due';
                return \Redirect::back()->withInput($input);
            }
        }else{
            $input['error'] = 'Student has return Book';
            return \Redirect::back()->withInput($input);
            
        }
    }

    public function updateStudent(Students $student)
    {
        $input = \Request::all();
        // dd($input);
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
                'parent_contact_no' => 'Parent Contact Number',
                'address' => 'Address',
                'avatar' => 'Avatar',
                'father_name' => 'Father Name',
                'mother_name' => 'Mother Name',
                'parent_email' => 'Parent Email'
            ];
        $validator = \Validator::make($input, [
                'session_id' => 'required|numeric',
                'registration_no' => 'required',
                'class' => 'required|numeric',
                'section' => 'required|numeric',
                'roll_no' => 'required|numeric',
                'name' => 'required',
                'gender' => 'required',
                'caste' => 'required|numeric',
                'blood_group' => 'required',
                'religion' => 'required',
                'father_name' => 'required',
                'mother_name' => 'required',
                'parent_contact_no' => 'required|regex:/^\d{10}$/',
                'parent_email' => 'required|email'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return Redirect::back()->withErrors($validator)->withInput($input);
        return $student->doUpdateStudent($input, $this->user);   
    }

    public function searchStudent()
    {
        $search = \Request::get('search');
        $student = Students::where('registration_no', 'LIKE', '%'.$search.'%')->where('school_id', $this->user->school_id)->get();
        return $student;
    }

    public function getHomeworkByStudent(Homework $homework, $flag, $id, $date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
        {
            return $homework->doGetHomeworkByStudent($this->user, $id, $date);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm. Year is according to Current Session']);
        }
    }

    public function getAttendanceByStudent(Attendance $attendance, $flag)
    {
        return $attendance->doGetAttendanceByStudent($this->user);
    }

    

    public function getAttendanceByDate(Attendance $attendance, $platform, $id, $date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
        {
            return $attendance->doGetAttendanceByDate($this->user, $id, $date);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
        }
    }
    
    public function getTimeTableByStudent(TimeTable $time, $platform)
    {
    	return $time->doGetTimeTableByStudent($this->user, $platform);
    }

   
}
