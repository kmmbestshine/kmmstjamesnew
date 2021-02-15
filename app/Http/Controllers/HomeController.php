<?php

namespace App\Http\Controllers;
use PDF;
use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator,
    Redirect,
    Auth,
    api;
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
use App\Salary;
use App\Students;
use App\Subject;
use App\TimeTable;
use App\Teacher_attendance;
use App\User;
use App\Installment;
use App\FeeStructuree;
use App\Payment;
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;
use DateTime;
use DatePeriod;
use DateInterval;
use Event;
use App\Events\SendNotification;
use File;
use DB;
use App\Events\SendSmsNotification;

class HomeController extends Controller {

    protected $user;
    private $active_session;//updated 14-4-2018
    
    public function __construct()
    {
        /** @ Updated 14-4-2018 by priya @ **/
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
        // $activeRoute = \Route::getCurrentRoute()->getAction()['as'];
        // view()->share(compact('activeRoute'));
        if (Auth::check()) {

            $this->user = \Auth::user();
            // $classes = addClass::where('school_id', \Auth::user()->school_id)->get();
            // $students = Students::where('school_id', \Auth::user()->school_id)->count();
            // $employees = Employee::where('school_id', \Auth::user()->school_id)->count();
            // $busCount = Bus::where('school_id', \Auth::user()->school_id)->count();
            // $school_image = School::where('id', \Auth::user()->school_id)->first();
            // $examtypes = Exam::where('school_id', \Auth::user()->school_id)->get();
            // $birthdays = Students::where('student.dob', 'LIKE', '%' . date('d-m') . '%')->where('student.school_id', \Auth::user()->school_id)->leftJoin('class', 'student.class_id', '=', 'class.id')->select('student.id', 'student.name', 'student.roll_no', 'class.class')->get();

            if(Auth::user()->type == 'school' || Auth::user()->type == 'user_role'){
                $classes = addClass::where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->get();
                //$students = Students::where('school_id', \Auth::user()->school_id)
                //->where('session_id',$this->active_session->id)//updated 14-4-2018
                //->count();
                 $students = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id', $this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->count();
                $male_students = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id', $this->active_session->id)//updated 14-4-2018
                ->where('gender', 'LIKE',  'm' . '%')
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->count();
                $female_students = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id', $this->active_session->id)//updated 14-4-2018
                ->where('gender', 'LIKE', 'f' . '%')
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->count();
                $tobeupdategender = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id', $this->active_session->id)//updated 14-4-2018
                ->where('gender', 'LIKE', 't' . '%')
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->count();
                /*$male_students = Students::where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)
                ->where('gender', 'LIKE',  'm' . '%')
                ->count();
                 $female_students = Students::where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)
                ->where('gender', 'LIKE', 'f' . '%')
                //updated 14-4-2018
                ->count();
                 $tobeupdategender = Students::where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)
                ->where('gender', 'LIKE', 't' . '%')
                //updated 14-4-2018
                ->count();*/
                $employees = Employee::where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 10-5-2018
                    ->count();
                $emp_dob = Employee::where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 10-5-2018
                    ->where('emp_dob','LIKE', '%' . date('d-m') . '%')
                    ->get();
                $emp_wedding = Employee::where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 10-5-2018
                    ->where('emp_wed_day','LIKE', '%' . date('d-m') . '%')
                    ->get();
               // dd($emp_dob,$emp_wedding);

                $busCount = Bus::where('school_id', \Auth::user()->school_id)->count();
                $school_image = School::where('id', \Auth::user()->school_id)->first();
                $examtypes = Exam::where('school_id', \Auth::user()->school_id)->get();
                $birthdays = Students::where('student.dob', 'LIKE', '%' . date('d-m') . '%')
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->where('student.school_id', \Auth::user()->school_id)->leftJoin('class', 'student.class_id', '=', 'class.id')->select('student.id', 'student.name', 'student.roll_no', 'class.class')->get();
            }else{
                $classes = addClass::where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->get();
                $employeeObj = Employee::where('user_id', \Auth::user()->id)
                    ->where('session_id',$this->active_session->id)//updated 10-5-2018
                    ->where('school_id', \Auth::user()->school_id)->first();
                $students = Students::where('school_id', \Auth::user()->school_id)->where('class_id', $employeeObj->class)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('section_id', $employeeObj->section)->count();
                $employees = Employee::where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 10-5-2018
                    ->where('class', $employeeObj->class)
                    ->where('section', $employeeObj->section)
                    ->count();
                $school_image = School::where('id', \Auth::user()->school_id)->first();
            }

            $roler = [];
            if (Auth::user()->type == 'user_role') {
                $roleuser = \DB::table('user_role')->where('role_id', Auth::user()->id)->get();
                //dd($roleuser);

                foreach ($roleuser as $role) {
                    array_push($roler, $role->value);
                }
            }
            $userplans=[];

            if(!$school_image->userplan)
            {
                $school_image->userplan='Basic';
            }
            //dd($userplans);
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
            //dd($userplans);
            view()->share(compact('classes','emp_dob', 'employees','male_students','tobeupdategender','female_students', 'students', 'school_image', 'birthdays', 'examtypes', 'busCount', 'abses', 'roler','userplans'));
        }
    }

    public function home() {
        dd('Home');
    }

    public function manage() {
        if (\Auth::check()) {

            return \Redirect::route('user.dashboard');
        } else {
            return view('users.login');
        }
    }

    public function treport() {
        $view = \View::make('treport');



        $html = $view->render();
        return \PDF::loadHTML($html, 'A4', 'portrait')->stream();
    }

    public function authCreate(Request $request, User $user) {
        $userError = ['username' => 'User ID', 'password' => 'Password'];
        $validator = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator);;
        return $user->createAuth($request);
    }

    public function dashboard() {
        $school_profile = \DB::table('school')->where('school.id', $this->user->school_id)->leftJoin('post', 'school.id', '=', 'post.school_id')->select('school.school_name', 'school.email', 'school.mobile', 'school.address', 'school.city', 'school.image as logo', 'post.image as post')->first();
        //dd($school_profile);
        $absents = \DB::table('teacher')->join('teacher_attendance', 'teacher.id', '=', 'teacher_attendance.employee_id')->where('teacher_attendance.date', date('d-m-Y'))
            ->where('teacher_attendance.attendance', '!=', 'P')
            ->join('class', 'teacher.class', '=', 'class.id')
            ->select('teacher.name', 'class.class', 'teacher_attendance.attendance')->get();
        $birthdays = \DB::table('student')->where('student.dob', 'LIKE', '%' . date('d-m') . '%')
        ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
        ->where('student.school_id', $this->user->school_id)
            ->join('class', 'student.class_id', '=', 'class.id')
            ->select('student.id', 'student.name', 'class.class', 'student.roll_no')->get();

        return view('users.index', compact('school_profile', 'absents', 'birthdays'));
    }

    public function master()
    {
        return view('users.master.index');
    }

    public function employee()
    {
        return view('users.employee.index');
    }

    public function students()
    {
        return view('users.students.index');
    }

    public function attendance() {
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        return view('users.attendance.attendata', compact('classes'));
    }
    public function attendanceone() {
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        return view('users.attendance.attendataone', compact('classes'));
    }

    public function teacherAttendance() {
        $attendance = \DB::table('teacher')->where('teacher.school_id', $this->user->school_id)
            ->leftJoin('teacher_attendance', 'teacher.id', '=', 'teacher_attendance.employee_id')
            ->select('teacher.id', 'teacher.name', 'teacher.mobile')
            ->get();
        foreach ($attendance as $att) {
            $atten = \DB::table('teacher_attendance')->where('employee_id', $att->id)
                ->orwhere('teacher_attendance.date', date('d-m-Y'))->first();
            $att->attendance = $atten->attendance;
            $att->in = $atten->in;
            $att->out = $atten->out;
            $att->date = $atten->date;
        }

        return view('users.attendance.teacherAttendance', compact('attendance'));
    }

    public function postAttendanceTeacher(Teacher_attendance $post) {
        $input = \Request::all();
        return $post->saveAttendance($input, $this->user);
    }

    public function userCred() {
        $input = \Request::all();
        $class = addClass::where('id', $input['class'])->first();
        if (!$class) {
            $input['error'] = 'Class is not exist';
            return Redirect::back()->withInput($input);
        }
        $section = Section::where('class_id', $input['class'])->where('id', $input['section'])->first();
        if (!$section) {
            $input['error'] = 'Section is not exist';
            return Redirect::back()->withInput($input);
        }
        return Redirect::route('user.attendata', ['class' => $input['class'], 'section' => $input['section']]);
    }


     public function userCredone() {
        $input = \Request::all();
        $class = addClass::where('id', $input['class'])->first();
        if (!$class) {
            $input['error'] = 'Class is not exist';
            return Redirect::back()->withInput($input);
        }
        $section = Section::where('class_id', $input['class'])->where('id', $input['section'])->first();
        if (!$section) {
            $input['error'] = 'Section is not exist';
            return Redirect::back()->withInput($input);
        }
        return Redirect::route('user.attendataone', ['class' => $input['class'], 'section' => $input['section']]);
    }

    /** updated 11-5-2018 by priya **/

    public function getAttenData($class,$section)
    {

        /*
         * updated 11-5-2018 by priya
         *
         * $students = Students::where('student.class_id', $class)
            ->where('student.session_id',$this->active_session->id)//updated 27-3-2018
            ->where('student.section_id', $section)
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id', 'student.name', 'student.roll_no', 'class.class', 'class.id as class_id', 'section.section', 'student.date_of_joining','section.id as section_id')
            ->orderBy('student.roll_no', 'ASC')
            ->get();
         *
         * */

        $students = \DB::table('student')
            ->where('student.session_id',$this->active_session->id)
            ->select('student.id','student.name', 'student.roll_no', 'class.class',
                'class.id as class_id', 'section.section', 'student.date_of_joining',
                'section.id as section_id')
            ->whereNotIn('student.id',function($query) use($class,$section)
            {
                $query->select('student_id')->from('attendance')
                    ->where('class_id',$class)
                    ->where('section_id',$section)
                    ->where('date', date("Y-m-d"))
                    ->where('attendance','=','L');
            })
            ->where('student.class_id',$class)
            ->where('student.section_id',$section)
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            //->orderBy('student.roll_no', 'ASC')
            ->orderBy('student.name', 'ASC')
            ->get();

        /*end*/

        $getclass = addClass::where('id', $class)->first();
        $getsection = Section::where('id', $section)->first();

        return view('users.attendance.attendata', compact('students', 'getclass', 'getsection', 'attendance'));
    }

    public function getAttenDataone($class,$section)
    {

        /*
         * updated 11-5-2018 by priya
         *
         * $students = Students::where('student.class_id', $class)
            ->where('student.session_id',$this->active_session->id)//updated 27-3-2018
            ->where('student.section_id', $section)
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id', 'student.name', 'student.roll_no', 'class.class', 'class.id as class_id', 'section.section', 'student.date_of_joining','section.id as section_id')
            ->orderBy('student.roll_no', 'ASC')
            ->get();
         *
         * */

        $students = \DB::table('student')
            ->where('student.session_id',$this->active_session->id)
            ->select('student.id','student.name', 'student.roll_no', 'class.class',
                'class.id as class_id', 'section.section', 'student.date_of_joining',
                'section.id as section_id')
            ->whereNotIn('student.id',function($query) use($class,$section)
            {
                $query->select('student_id')->from('attendance')
                    ->where('class_id',$class)
                    ->where('section_id',$section)
                    ->where('date', date("Y-m-d"))
                    ->where('attendance','=','L');
            })
            ->where('student.class_id',$class)
            ->where('student.section_id',$section)
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            //->orderBy('student.roll_no', 'ASC')
            ->orderBy('student.name', 'ASC')
            ->get();

        /*end*/

        $getclass = addClass::where('id', $class)->first();
        $getsection = Section::where('id', $section)->first();

        return view('users.attendance.attendataone', compact('students', 'getclass', 'getsection', 'attendance'));
    }

    public function postAttendance(Attendance $att)
    {
        $input = \Request::all();
        $userError = ['attendance' => 'Attendance', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($input, [
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $att->doPostAttendance($input, $this->user);
    }
    public function postAttendanceone(Attendance $att)
    {
        $input = \Request::all();
        $userError = ['attendance' => 'Attendance', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($input, [
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $att->doPostAttendanceone($input, $this->user);
    }

    public function update_student_attendance($class_id,$section_id,$session)
    {
        $date=date("d-m-Y");
        $holiday = new Holiday();
        $is_holiday = $holiday->is_holiday($date);
        $students = Students::where('student.class_id', $class_id)
            ->where('student.session_id',$this->active_session->id)//updated 27-3-2018
            ->where('student.section_id', $section_id)
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id', 'student.name',
                'student.roll_no', 'class.class',
                'class.id as class_id', 'section.section',
                'section.id as section_id')
            ->orderBy('student.roll_no', 'ASC')
            ->get();
        if($is_holiday)
        {
            $holidays="H";
        }
        else
        {
            foreach($students as $student)
            {
                $attendance = Attendance::where('school_id', Auth::user()->school_id)
                    ->where('class_id', $class_id)
                    ->where('section_id', $section_id)
                    ->where('date', date("Y-m-d"))
                    ->where('student_id',$student->id)
                    ->where('attendance_session',$session)
                    ->orderBy('id', 'ASC')
                    ->select('attendance','id','student_id','date','attendance_session')
                    ->get();
                $student->attendance = $attendance;
            }
        }

        $getclass = addClass::where('id', $class_id)
            ->where('session_id',$this->active_session->id)//updated 27-3-2018
            ->first();
        $getsection = Section::where('id', $section_id)
            ->where('session_id',$this->active_session->id)//updated 27-3-2018
            ->first();
        return view('users.attendance.attendance', compact('holidays','students', 'getclass', 'getsection', 'attendance','session'));
    }

    public function edit_student_attendance(Attendance $att)
    {
        $input = \Request::all();
        $userError = ['attendance' => 'Attendance', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($input, [
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $att->doUpdateAttendance($input, $this->user);
    }

    /*end*/

    public function viewAttendance()
    {
        $class = \Request::get('class');
        $section = \Request::get('section');
        if ($class and $section)
        {
            $classData = addClass::where('id', $class)->first();
            $sectionData = Section::where('id', $section)->first();
            $student = \DB::table('student')->where('school_id', \Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('class_id', $class)->where('section_id', $section)
                //->where('id','=',8756)
                ->get();
            $students=array();
            $date=date("d-m-Y");
            $holiday = new Holiday();
            $is_holiday = $holiday->is_holiday($date);
            foreach($student as $key=>$value)
            {
                if($is_holiday)
                {
                    $am="H";
                    $pm="H";
                }
                else
                {
                    $is_att_am=\DB::table('attendance_status')->where('class_id', $class)
                        ->where('section_id', $section)
                        ->where('date','=',date('Y-m-d'))
                        ->where('attendance_session','=','am')->first();
                    $is_att_pm=\DB::table('attendance_status')->where('class_id', $class)
                        ->where('section_id', $section)
                        ->where('date','=',date('Y-m-d'))
                        ->where('attendance_session','=','pm')->first();
                    $is_avail= strtotime($value->created_at);
                    if(!empty($is_att_am))
                    {
                        $is_atte_am_avail =strtotime($is_att_am->created_at);
                        if($is_att_am->updated_at!='')
                        {
                            $is_atte_am_avail = strtotime($is_att_am->updated_at);  
                        }
                        if($is_atte_am_avail>$is_avail)
                        {
                            $is_leave_am=Attendance::where('date', date('Y-m-d'))
                                ->where('class_id', $class)
                                ->where('section_id', $section)
                                ->where('attendance_session', 'am')
                                ->where('student_id','=',$value->id)
                                ->where('attendance','!=','p')->first();
                            $am="";
                            $pm="";
                            if(!empty($is_leave_am))
                            {
                                $am=$is_leave_am->attendance;
                                $remark_am=$is_leave_am->remarks;
                            }
                            else
                            {
                                $remark_am="";
                                $am="P";
                            }
                        }
                        else
                        {
                            $remark_am="";
                            $am="-";
                        }
                    }
                    else
                    {
                        $am="-";
                        $remark_am="";
                    }
                    if(!empty($is_att_pm))
                    {
                        $is_atte_pm_avail = strtotime($is_att_pm->created_at);
                        if($is_att_pm->updated_at!='')
                        {
                          $is_atte_pm_avail =strtotime($is_att_pm->updated_at);
                        }
                    if($is_atte_pm_avail>$is_avail)
                    {
                         $is_leave_pm=Attendance::where('date', date('Y-m-d'))
                                ->where('class_id', $class)
                                ->where('section_id', $section)
                                ->where('attendance_session', 'pm')
                                ->where('student_id','=',$value->id)
                                ->where('attendance','!=','p')->first();
                            if(!empty($is_leave_pm))
                            {
                                $pm=$is_leave_pm->attendance;
                                $remark_pm=$is_leave_pm->remarks;
                            }
                            else
                            {
                                $pm="P";
                                $remark_pm="";
                            }
                        }
                        else
                        {
                            $pm="-";
                            $remark_pm="";
                        }
                    }
                    else
                    {
                            $pm="-";
                            $remark_pm="";
                    }
                }
                array_push($students,['student_id'=>$value->id,
                    'registration_no'=>$value->registration_no,'name'=>$value->name,'roll_no'=>$value->roll_no,'am'=>$am,'pm'=>$pm,'date'=>date('Y-m-d')]);
            }
        }
        else
        {
            $classData = '';
            $sectionData = '';
            $attendances = '';
            $students=array();

        }
        return view('users.attendance.list', compact('students','classData', 'sectionData','student'));
    }
    public function feeCollectionnewfee() {
       $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        return view('users.fee_collection.sion.indexnew', compact('classes'));
    }

public function school_paymentfee(Session $session,Students $student)
    {
         $input = \Request::all();

         $reg = \Request::get('regno');
        $register_no = str_replace(".", "/", $reg);

        $studentid=$input['student'];

        //dd($input,$register_no);

        if($input['student']){

            $student = \DB::table('student')->where('id', $input['student'])->where('session_id',$this->active_session->id)->where('school_id', \Auth::user()->school_id)->first();
            //dd($student,'id');
            $reg=$student->registration_no;
             $register_no = str_replace(".", "/", $reg);
        }else{

            $student = \DB::table('student')->where('registration_no', $input['regno'])->where('session_id',$this->active_session->id)->where('school_id', \Auth::user()->school_id)->first();
            //dd($student,'reg');
            $reg=$student->registration_no;
             $register_no = str_replace(".", "/", $reg);

        }
       
       
        if ($register_no) 
        {
            $session_id = $session->get_active_session_id(Auth::user()->school_id);
            //$single_student = $student->get_student_by_regNo($register_no, Auth::user()->school_id);
            $classes = addClass::select('class')->where('id', $student->class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->first();
             $section = Section::select('section')->where('id', $student->section_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->first();
        }
        $sec =$section->section;
        $class=$classes->class;
        $studentid=$student->id;
                if($class != '0' )
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class_id',$student->class_id)
                    ->where('student_id',$studentid)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$student->class_id)
                        ->whereIn('student_id',array('0',$studentid))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$student->class_id)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getFee);
                    $amount=0;
                    $t1_feename=array();
                    $t1_amt=array();
                    $term_type=array();
                    $t1_ids=array();

                    foreach ($getFee as $amt ) {
                        $t1_feename[]=$amt->fees_name;
                        $t1_amt[]=$amt->amount;
                        $t1_totamt+=$amt->amount;
                        $term_type[]=$amt->payment_type;
                        $t1_ids[]=$amt->id;
                    }
                if(!empty($t1_ids))
                 {
                    foreach ($t1_ids as $key => $value) {
                            $all_paidamt[] = DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)->where('student_id',$studentid)
                            ->where('session_id','=',$this->active_session->id)
                            //->where('fee_id',$value)
                            ->get();
                        }
                    $allpaid_ids=array();
                    $total_paidAmt=0;
                    foreach($all_paidamt as $firstlevelids){
                        foreach($firstlevelids as $paidids) {
                            $allpaid_feeId[]=$paidids->id;
                           
                        }
                    }

                     $collection = collect($allpaid_feeId);
                    $unique_paid_id = $collection->unique()->values()->all();
                    //dd($unique_paid_id);
                    if (is_array($unique_paid_id)){
                        foreach ($unique_paid_id as $key => $value) {
                            $all_paidamt1[] = DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)->where('student_id',$studentid)
                            ->where('session_id','=',$this->active_session->id)
                            ->where('id',$value)
                            ->get();
                        }
                    }
                   
                    //dd($all_paidamt1);
                    if(!empty($all_paidamt1)){
                        foreach($all_paidamt1 as $firstlevelids){
                        foreach($firstlevelids as $paidids) {
                            $allpaid_feeId[]=$paidids->id;
                            $allpaid_feeName[]=$paidids->fee_name;
                            $allpaid_termType[]=$paidids->payment_type;
                            $allpaid_ids[]=$paidids->fee_id;
                            $allpaid_amt[]=$paidids->amount;
                            $allconcession_amt[]=$paidids->concession;
                            $allpaid_date[]=$paidids->date;
                            $allpaid_recvdby[]=$paidids->recived_by;
                            $allpaid_paymentmode[]=$paidids->payment_mode;
                            $allpaid_cheqNo[]=$paidids->cheque_no;
                            $allpaid_cheqDate[]=$paidids->cheque_date;
                            $allpaid_bankname[]=$paidids->bank_name;
                            $allpaid_onlineTfno[]=$paidids->transaction_no;
                            $allpaid_onlinebkName[]=$paidids->online_bankname;
                            $total_paidAmt+=$paidids->amount;
                            $invoiceids[]=$paidids->invoice_id;
                        }
                    }
                    }
                        
                    $collection = collect($invoiceids);
                    $unique_receipt = $collection->unique()->values()->all();

                    //dd('hi',$all_paidconcessionamt1,$unique_paid_id);
                    $allunpaid_ids= array_diff($t1_ids, $allpaid_ids);
                    if(!empty($allunpaid_ids))
                    {
                        $all_unpaidamt=array();
                    foreach ($allunpaid_ids as $key => $value) {
                            $all_unpaidamt[] = DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)->where('id',$value)->where('bus_route','=', null)->where('route_id','=', null)->get();
                        }
                        }
                        //dd('all_unpaidamt',$all_unpaidamt,$allpaid_ids,$t1_ids);
                if($all_unpaidamt != null )
                {
                    foreach ($all_unpaidamt as $key ) {
                        foreach ($key as $amt) {
                             if($amt->amount != null)
                            {
                            
                            $unpaid_feename[]=$amt->fees_name;
                            $unpaid_amt[]=$amt->amount;
                            $unpaid_totamt+=$amt->amount;
                            $unpaid_type[]=$amt->payment_type;
                            $unpaid_ids[]=$amt->id;

                             }
                            
                            
                        }
                    }
                }
                if(!empty($allunpaid_ids))
                    {
                       $all_unpaid_busamt=array();
                    foreach ($allunpaid_ids as $key => $value) {
                            $all_unpaid_busamt[] = DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)->where('id',$value)->where('bus_route','!=', null)->where('route_id','!=', null)->get();
                        }
                    }

                    if($all_unpaidamt != null )
                        {
                        foreach ($all_unpaid_busamt as $key ) {
                            foreach ($key as $amt) {
                            
                            if($amt->amount != null)
                            {
                            $unpaid_busfeename[]=$amt->fees_name;
                            $unpaid_busmt[]=$amt->amount;
                            $unpaid_bustotamt+=$amt->amount;
                            $unpaid_bustype[]=$amt->payment_type;
                            $unpaid_busids[]=$amt->id;
                            $unpaid_route[]=$amt->bus_route;
                            $unpaid_busno[]=$amt->bus_no;
                            $unpaid_boarding[]=$amt->boarding;
                            $unpaid_route_id[]=$amt->route_id;
                            $unpaid_bus_id[]=$amt->bus_id;
                            $unpaid_boarding_id[]=$amt->board_id;
                            }
                        }
                    }
                }
               // dd($all_unpaid_busamt,$unpaid_route_id);
                    //dd('all_unpaidamt',$all_unpaidamt,'unpaid_ids',$unpaid_ids,'unpaid_type',$unpaid_type,'unpaid_totamt',$unpaid_totamt,'unpaid_amt',$unpaid_amt,'unpaid_feename',$unpaid_feename);
                    //$all_ids = array_merge($allpaid_ids, $allunpaid_ids);
                    //$t1_totamt = array_sum($t1_amt);
                    $tot_bal_amt=$t1_totamt- $total_paidAmt;
                }
            }
            $school = school::where('id', Auth::user()->school_id)->first();
            return view('users.fee_collection.sion.single_school_payment', compact('allconcession_amt','unpaid_route_id','unpaid_bus_id','unpaid_boarding_id','unique_receipt','unpaid_boarding','unpaid_route','unpaid_busno','unpaid_busfeename','unpaid_busmt','unpaid_bustotamt','unpaid_bustype','unpaid_busids','unique_paid_id','invoiceids','school','sec','class','unpaid_ids','unpaid_type','unpaid_totamt','unpaid_amt','unpaid_feename','total_paidAmt','allpaid_onlinebkName','allpaid_onlineTfno','allpaid_bankname','allpaid_cheqDate','allpaid_cheqNo','allpaid_paymentmode','allpaid_recvdby','allpaid_date','allpaid_amt','allpaid_ids','allpaid_termType','allpaid_feeName','tot_bal_amt','total_paidAmt','register_no','student','t1_totamt','allpaid_ids','allunpaid_ids','t1_feename','t1_amt','term_type','t1_ids','all_ids'));
       }
        public function duplicateReceipt(Students $student) 
     {
        $input = \Request::all();
        $all_paidamt1[] = DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)->where('invoice_id',$input['recptNo'])
                            //->where('id',$value)
                            ->get();
            if(!empty($all_paidamt1)){
                        foreach($all_paidamt1 as $firstlevelids){
                        foreach($firstlevelids as $paidids) {
                            $allpaid_feeId[]=$paidids->fee_id;
                            $allpaid_feeName[]=$paidids->fee_name;
                            $allpaid_termType[]=$paidids->payment_type;
                            $allpaid_ids[]=$paidids->fee_id;
                            $allpaid_amt+=$paidids->amount;
                            $allpaid_date=$paidids->date;
                            $allpaid_recvdby=$paidids->recived_by;
                            $allpaid_paymentmode=$paidids->payment_mode;
                            $invoiceids[]=$paidids->invoice_id;
                            $reg_no=$paidids->reg_no;
                        }
                    }
                    }
            foreach ($allpaid_feeId as $key => $value) {
                            $all_paidFee[] = DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)->where('id',$value)->get();
                        }
            foreach ($all_paidFee as $key => $valu) {
                foreach ($valu as $key => $value) {
                        $term_type[]=$value->payment_type;
                        $fee_name[]=$value->fees_name;
                        $amount[]=$value->amount;
                        $tot_amt+=$value->amount;
                }
                        
                 }
                //dd($all_paidFee,$tot_amt,$amount,$fee_name);
                 $receipt_no=$input['recptNo'];
//
         $school = school::where('id', Auth::user()->school_id)->first();
    $single_student = $student->get_student_by_regNo($reg_no, Auth::user()->school_id);
    //dd('hi',$reg_no,$school->school_name);
   $classes = addClass::select('class')->where('id', $single_student->class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->first();
    $username= \DB::table('users')->where('school_id', \Auth::user()->school_id)->where('username',$allpaid_recvdby)->first();
    $teach = \DB::table('teacher')->where('user_id',$username->id)->where('school_id', $this->user->school_id)->first();
    $teachers= $teach->name;

    if(empty($teachers)){
         $school = school::where('user_id',$username->id)->where('id', $this->user->school_id)->first();
         $teachers=$school->school_name;
     }
    return view('users.fee_collection.sion.duplicatepayment_recipt', compact('allpaid_amt','fee_name','term_type','amount','teachers','tot_amt','fee_structure_Amount','allpaid_paymentmode','receipt_no','allpaid_date','concession','after_con_received_amt','conces_remarks','single_student','classes','school'));
           
     }
        public function paymentCollectionnewfee( Request $request,Session $session,Students $student) 
     {
        $t1_ids = $request->input('t1_feename');
        $tbus_ids = $request->input('busFeesname');
        $tpaidbus_ids = $request->input('busfees_ids');
        $student_details=[];
        $student_details=$request->input('student_id');
        $registration_no=$request->input('register_no');
        //dd($t1_ids,$tbus_ids,$tpaidbus_ids,$student_details,$tpaidbus_ids);
        $fees_details=array();
        if(!empty($t1_ids))
        {
        foreach($t1_ids as $id)
            {
            $fees_details[] = DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id',$id)->get();
            }
        }
        
        $payment_details = array();
        foreach($fees_details as $merfee) 
            {
            $payment_details = array_merge($payment_details, $merfee);
            }
            //dd($payment_details,'hi');
        foreach($payment_details as $amt)
                {  
                $term_type[]=$amt->payment_type;
                $class=$amt->class;
                $class_id=$amt->class_id;
                $fee_name[]=$amt->fees_name;
                $fee_amt[]=$amt->amount;
                $paid_totalAmt+=$amt->amount;
                $bus_no[]=$amt->bus_no;
                $route[]=$amt->bus_route;
                $board1[]=$amt->boarding;
                $bus_id[]=$amt->bus_id;
                $route_id[]=$amt->route_id;
                $board_id[]=$amt->board_id;
                }

        
        $single_student = $student->get_student_by_regNo($registration_no, Auth::user()->school_id);
        $classes = addClass::select('class')->where('id', $class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->first();
        //dd($payment_details,$class_id,$classes);
        $classes=$classes->class;
        $session_id = Session::where('school_id', \Auth::user()->school_id)->where('session','=',$this->active_session->session)->first();
       $session=$session_id['session'];
        $section_id=$single_student->section_id;
        $class_id=$single_student->class_id;
        $sections = Section::where('class_id', $class_id)->where('id', $section_id)->first();
        $section=$sections['section'];
        $received_by=\Auth::user()->username;
        //dd('hi',$board1,$route,$bus_no);
        return view('users.fee_collection.sion.school_selected_amount', compact('bus_id','route_id','board_id','board1','route','bus_no','tbus_ids','t1_ids','received_by','paid_totalAmt','term_type','class','session','fee_name','fee_amt','section','single_student'));
   }

    public function paymentReceivednew(Request $request,Students $student) 
     {
         $input = \Request::all();
         //dd($input,'fu');
        $idNos= $input['idNos'];
        $busidNos= $input['busidNos'];
        $conces_remarks= $input['conces_remarks'];
        $concession= $input['concession'];
        $latefees= $input['latefees'];
        $concession= $input['concession'];
        $paidAmount= $input['paidAmt'];
        $termsNo= $input['terms'];
        $feenames= $input['feenames'];
        $feeamts= $input['feeamts'];
        $name= $input['name'];
        $class_id= $input['class_id'];
        $section_id= $input['section_id'];
        $reg_no= $input['reg_no'];
        $roll_no= $input['roll_no'];
        $paydate= $input['paydate'];
        $pmMode= $input['pmMode'];
        $cheqno= $input['cheqno'];
        $cheqdate= $input['cheqdate'];
        $bank_name= $input['bank_name'];
        $onlinebank_name= $input['bank_name1'];
        $transno= $input['trans_no'];
        $revdby= $input['revdby'];
        $student_id= $input['student_id'];

        $route= $input['route'];
        $bus_no= $input['bus_no'];
        $board1= $input['board'];
        $route_id= $input['route_id'];
        $bus_id= $input['bus_id'];
        $board_id= $input['board_id'];
        //dd('route',$route_id,'bus_no',$bus_id,$board_id);
         $fees_details=array();
           $termsTypes = array();
           $feesNames = array();
           $feesAmounts = array();

    foreach($termsNo as $term)
        {
    $termsTypes[]=$term;
        }
    foreach($feenames as $names)
        {
    $feesNames[]=$names;
        }
    foreach($feesAmounts as $amounts)
        {
    $feesAmounts[]=$amounts;
        }
// To find Fee Structure Amount

    foreach($idNos as $ids)
    {
   $fees_details[] = DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id',$ids)->get();
    }
    //dd($fees_details);
     $payment_details = array();
    foreach($fees_details as $merfee)
     {
        $payment_details = array_merge($payment_details, $merfee);
    }

    foreach($payment_details as $collection)
    {
    $feeStr_Amt[]=$collection->amount;
   
    }

    $fee_structure_Amount=array_sum($feeStr_Amt);
   //dd($fee_structure_Amount);
    $session=$this->active_session->id;
// To find Paid Amount before concession


    $paidAmount_b4_concession=$paidAmount;

// To find paid amount after concession
    
    if(empty($concession))
    {
        $concession=0;
        $feeamts123[]=$paidAmount;
        $feeamts123[]+=$concession;
        $after_con_received_amt=array_sum($feeamts123);
    }
    else{
        $feeamts123[]=$paidAmount;
        $feeamts123[]+=$concession;
        $after_con_received_amt=array_sum($feeamts123);
    }

// To find payable-paid = balance ( last element of array)


 if($after_con_received_amt == $fee_structure_Amount)
    {
        $feeamounts123=$feeamts;

    }
    else
    {
        
        $balance=$fee_structure_Amount - $after_con_received_amt;
        $lastFeeAmt= last($feeamts);
        if($balance < $lastFeeAmt)
        {
             $lastPaidAmt=$lastFeeAmt - $balance;
        }
        else{
            dd('Selected Wrong Fee Structure Amount');
        }
       $feeamounts123=$feeamts;
        
        $feeamts1 = array_pop($feeamts);

        $feeamts[]=$lastPaidAmt;
        $received_amt=0;
        foreach ($feeamts as $key => $value) {
            $received_amt =$received_amt + $value;
        }
    }

//To  insert balance amount

    
    $single_student = $student->get_student_by_regNo($reg_no, Auth::user()->school_id);
   $classes = addClass::select('class')->where('id', $single_student->class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->first();
    //dd($classes);
    $lastTermTypes = last($termsTypes);
    $lastFeeName= last($feenames);
     if($lastTermTypes == null)
         {
            $lastRoute=last($route);
            $lastBusno=last($bus_no);
            $lastBoard=last($board1);
            $lastRoute_id=last($route_id);
            $lastBusno_id=last($bus_id);
            $lastBoard_id=last($board_id);

         }else{
            $lastRoute=null;
            $lastBusno=null;
            $lastBoard=null;
            $lastRoute_id=null;
            $lastBusno_id=null;
            $lastBoard_id=null;
         }
         

         if($lastTermTypes == "")
         {
            $lastTermTypes=null;
         }

         foreach ($idNos as $key => $value) {
               
               $checkFeeid = DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
               ->where('session_id',$this->active_session->id)
                ->where('student_id',$single_student->id)
               ->where('fee_id',$value)->get();
            }
            //dd($checkFeeid,count(array_filter($checkFeeid)));
 if(count(array_filter($checkFeeid)) == 0)
        {
     if($balance !='0')
    {
        $status=1;
        //dd($lastBusno,$lastRoute,$lastBoard,$lastRoute_id,$lastRoute_id,$lastBoard_id);
        
             DB::table('sionfee_structure')->insert(
                array(
                'school_id' => Auth::user()->school_id,
                'session_id' => $session,
                'class' => $classes->class,
                'class_id' => $input['class_id'],
                'student_id' => $student_id,
                'payment_type'=>$lastTermTypes,
                'bus_no' =>$lastBusno,
                'bus_route' =>$lastRoute,
                'boarding' =>$lastBoard,
                'board_id'=>$lastBoard_id,
                'bus_id'=>$lastBusno_id,
                'route_id'=>$lastRoute_id,
                'fees_name'=>$lastFeeName,
                'bal_status'=>$status,
                'amount'=>$balance,
                ));
             
              
    }
}else{
            dd("This Fees was already paid. So You can't again pay");
        }
    // To continu work
 $allpaidamt = DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('reg_no',$reg_no)->get();
 //dd($allpaidamt);
    foreach($allpaidamt as $key =>$value)
    {
        $checkfeeId=$value->fee_id;
        
        $checkfeeAmt=$value->amount;
        
    }
    
if($checkfeeId !='0'  && $checkfeeAmt !='0') 
    {
        $bal_amt=0;
    }

            
        // Invoice Id
            $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->select('school_name')->first();
            //dd($schoolname);
            $invoice_school_name=str_replace(" ","",$schoolname->school_name);
            $schoolname=substr($invoice_school_name, 0, 3);
            $check_max_invoice_no=\DB::table('invoice_nos')->whereNotNull('invoice_id')->where('school_id', \Auth::user()->school_id)->orderBy('invoice_id', 'desc')->first();
            if($check_max_invoice_no)
            {
                $schoolid=(\Auth::user()->school_id);
                $replacedata=$schoolname.'REC'.$schoolid;
                $invoiceid=str_replace($replacedata,'',$check_max_invoice_no->invoice_id)+1;
                $invoicelen=4-strlen($invoiceid);
                $finalid='';
                if($invoicelen != 0){
                    for($i=0;$i<$invoicelen;$i++)
                    {
                        if($i==0)
                        {
                             $finalid='0'.$invoiceid;   
                        }else
                        {
                            $finalid='0'.$finalid;
                        }
                    }

                }else{
                    $finalid=$invoiceid;
                }
                $request['invoice_id']=$schoolname.'REC'.\Auth::user()->school_id.$finalid;
            }
            else
            {
                $request['invoice_id']=$schoolname.'REC'.\Auth::user()->school_id.'0001';
                $invoice=$request['invoice_id'];
            }
             $invoice_ids=$request['invoice_id'];
            DB::table('invoice_nos')->insert([
                'school_id' => Auth::user()->school_id,
                'session_id' => $session,
                'class' => $classes->class,
                'class_id' => $input['class_id'],
                'student_id' => $student_id,
                'invoice_id' => $request['invoice_id']
                
                ]);

            $paid = ($bal_amt == 0 ? true : false );
//dd($input['class_id']);
             if(count(array_filter($checkFeeid)) == 0)
            {

    foreach($feeamts as $key =>$value)
            {
        $result=DB::table('sionfee_collection')->insert(
            array(
            'amount' =>$feeamts[$key],
            'school_id' => Auth::user()->school_id,
            'session_id' => $session,
            'paid' => $paid,
            'student_id' => $student_id,
            'fee_id' => $idNos[$key],
            //'busfeesid' => $busidNos[$key],
            'date' => $paydate,
            'name' => $name,
            'class' => $classes->class,
            'class_id' => $input['class_id'],
            //'section_id' => $section_id,
            'reg_no' => $reg_no,
            'roll_no' => $roll_no,
           //'balance_amount'=>$bal_amt,
            'payment_type'=>$termsNo[$key],
            'fee_name'=>$feenames[$key],
            'payment_mode' => $pmMode,
            'recived_by' => $revdby,
            'payment_detail' =>$conces_remarks,
            'concession' =>$concession,
           'transaction_no' => $transno,
           'bank_name'=> $bank_name,
           'online_bankname'=> $onlinebank_name,
           'invoice_id' => $request['invoice_id'],
           'cheque_date'=>$cheqdate,
           'cheque_no'=>$cheqno,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
                    ));
        }
}else{
        dd('This Fee was already paid. So you can not pay again');
    }
    $students = \DB::table('student')
                            ->where('student.id','=', $student_id)->join('parent', 'student.parent_id', '=', 'parent.id')
                            ->select('student.name as student_name', 'parent.mobile')
                            ->first();


                        $schoolname= \DB::table('school')
                                    ->where('user_id',\Auth::user()->id)
                                    ->first();
                        $message = urlencode('Dear Parents,Thank you for your child, ' .$students->student_name . ' School Payment Of Rs.'.$paidAmount.'/- by ' .$pmMode .' on '. $paydate . ' -By- '.$schoolname->school_name );
                         $smsusername= \DB::table('smsusers')
                                    ->where('school_id',\Auth::user()->school_id)
                                    ->select('username','password','type','smssource')
                                    ->first();
                             //dd($students,$schoolname,$message,$smsusername);
     //file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username='.$smsusername->username.'&password='.$smsusername->password.'&type=0&dlr=1&destination=91'.$students->mobile.'&source='.$smsusername->smssource.'&message='.$message);


       //dd('balance',$balance,'lastFeeAmt',$lastFeeAmt,'lastPaidAmt',$lastPaidAmt,'feeamts',$feeamts,'received_amt',$received_amt);
    $school = school::where('id', Auth::user()->school_id)->first();
    $single_student = $student->get_student_by_regNo($reg_no, Auth::user()->school_id);
   $classes = addClass::select('class')->where('id', $single_student->class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->first();
    $username= \DB::table('users')->where('school_id', \Auth::user()->school_id)->where('username',$input['revdby'])->first();
    $teach = \DB::table('teacher')->where('user_id',$username->id)->where('school_id', $this->user->school_id)->first();
    $teachers= $teach->name;
    if(empty($teachers)){
         $school = school::where('user_id',$username->id)->where('id', $this->user->school_id)->first();
         $teachers=$school->school_name;
     }
            //dd('hi');
    return view('users.fee_collection.sion.payment_recipt', compact('teachers','feeamounts123','fee_structure_Amount','balance','balanceAmount','paidAmount','invoice_ids','concession','after_con_received_amt','conces_remarks','received_amt','pmMode','feeamts','termsNo','feenames','single_student','classes','input','balance_amount','school','paydate'));
     }

   public function busdetails()
    {
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
       
      $busdetail= \DB::table('busdetails')
      ->leftjoin('route_details', 'busdetails.id', '=', 'route_details.bus_id')
      ->where('busdetails.school_id', \Auth::user()->school_id)
      ->where('busdetails.session_id',$this->active_session->id)->select('route_details.id','route_details.route_name','busdetails.bus_no','busdetails.id as busid')
      ->get();

      $buses= \DB::table('busdetails')
      ->where('school_id', \Auth::user()->school_id)
      ->where('session_id',$this->active_session->id)
      ->get();
        
        return view('users.fee_structure.sion.addbusDetails',compact('busdetail','buses'));
    }

    public function addStudentMapping()
    {
        //dd('ho');
        return view('users.fee_structure.sion.mappingindex');
    }
     
    public function busroutename()
    {

       $input = \Request::all();
        //dd($input);
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
        if($input['bus_type'] == 'other'){
            DB::table('busdetails')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'session_id'=> $session['id'],
               //'route'=> $input['routename'],
               'bus_no'=> $input['newbus_no']
                                    
                )
            );

        $max_bus_id=$busdetail=\DB::table('busdetails')->where('school_id', \Auth::user()->school_id)->where('session_id',$this->active_session->id)->max('id');

            foreach ($input['route'] as $key => $value) {
           DB::table('route_details')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'session_id'=> $session['id'],
               'route_name'=> $value,
               'bus_id'=> $max_bus_id
                                    
                )
            );
        }
        }else{

            $bus_id=$input['newbus_no'];

            foreach ($input['route'] as $key => $value) {
           DB::table('route_details')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'session_id'=> $session['id'],
               'route_name'=> $value,
               'bus_id'=> $bus_id 
                )
                 );
                }

            }
       $input['success'] = 'Bus route added successfully';
        return \Redirect::back()->withInput($input);
    }
    public function viewbusdetails()
    {

        


        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
       
      $busdetail= \DB::table('busdetails')->where('school_id', \Auth::user()->school_id)->where('session_id',$this->active_session->id)->get();
       foreach ($busdetail as $key => $value) {
        //dd($key,'hi',$value->route);
        $route[]=$value->route;
        $bus_no[]=$value->bus_no;
        $routeid[]=$value->id;
       }
       //dd('hi',$route,$routeid);
        return view('users.fee_structure.sion.view_busdetails',compact('route','bus_no','routeid'));
        
    }

    public function deletebusdetails() 
     {
        $input = \Request::all();
        
        if($input['busId']){
            $getbusid =\DB::table('route_details')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('bus_id', $input['busId'])->first();
         if(empty($getbusid))
         {
        \DB::table('busdetails')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id', $input['busId'])->delete();
         $msg['success']= 'Bus no Deleted Successfully !!!';
         return \Redirect::back()->withInput($msg);
         }else{
            $msg['error'] = 'Some Bus Routes added to this bus no . So You Can not delete this Bus No ';
            return \Redirect::back()->withInput($msg);
         }
     }else{
        $getrouteid =\DB::table('boarding')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('route_id', $input['routeId'])->first();

         if(empty($getrouteid))
         {
        \DB::table('route_details')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id', $input['routeId'])->delete();
        $msg['success']= 'Bus Route Deleted Successfully !!!';
         return \Redirect::back()->withInput($msg);
         }else{
            $msg['error'] = 'Some Boarding Points added to this bus route. So You Can not delete this This bus route ';
            return \Redirect::back()->withInput($msg);
         }
     }
     }

    public function homework() {
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        return view('users.homework.homework', compact('classes'));
    }

    public function fetchSubjects() {
        $section = \Request::get('srsection');
        $getsection = Section::where('id', $section)->first();
        $subjects = Subject::whereIn('id', json_decode($getsection->subjects))->get();
        return $subjects;
    }

    public function postHomeWork(Homework $hw) {
        $input = \Request::all();
        $userError = ['class' => 'Class', 'section' => 'Section', 'subject' => 'Subject', 'description' => 'Description', 'image' => 'Image', 'date' => 'Date', 'pdf' => 'PDF'];
        $validator = \Validator::make($input, [
            'class' => 'required|numeric',
            'section' => 'required|numeric',
            'subject' => 'required|numeric',
            'description' => 'required',
            'image' => 'mimes:jpg,jpeg,png',
            'pdf' => 'mimes:pdf',
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator)->withInput($input);
        return $hw->doPostHomework($input, $this->user);
    }

    public function getHomework(Homework $hw) {
        return $hw->doGetHomework($this->user);
    }

    public function deleteHomework($id) {
        \DB::table('homework')->where('id', $id)->delete();
        $msg['success'] = 'Success to delete Homework';
        return \Redirect::back()->withInput($msg);
    }

    public function notification() {
        $notifications = NotificationType::where('school_id', $this->user->school_id)->orderBy('id', 'DESC')->get();
        return view('users.notification.index', compact('notifications'));
    }

    public function postDeviceNotification() {
        $input = \Request::all();

        $input['date'] = date('d-m-Y', strtotime($input['date']));
        $userError = ['notification_type' => 'Notification Type', 'classes' => 'Class', 'notification_send_to' => 'Notification Send To', 'date' => 'Date'];
        $validator = \Validator::make($input, [
            'notification_type' => 'required',
            'classes' => 'required',
            'notification_send_to' => 'required',
            'date' =>
                'required|date_format:d-m-Y'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        } else {
            // send notification to teachers
            if($input['notification_send_to'] == "teacher"){
               // $teachers = Employee::whereIn('class', $input['classes'])->where('school_id', $this->user->school_id)->get();
                $teachers = Employee::where('session_id',$this->active_session->id)->where('school_id', $this->user->school_id)->get();
               // dd($teachers);
                $notification_content = \DB::table('notification_type')->where('id', $input['notification_type'])->first();
                \DB::table('notification_history')->insert([
                    'notification_type_id' => $input['notification_type'],
                    'date' => $input['date'],
                    'role_id' => $this->user->school_id,
                    'role' => 'teacher',
                    'message_type' => 'text_msg',
                    'school_id' => $this->user->school_id
                ]);

            }else{
                // send notification to students
                $students = Students::whereIn('class_id', $input['classes'])->where('school_id', $this->user->school_id)->get();

                $notification_content = \DB::table('notification_type')->where('id', $input['notification_type'])->first();
                \DB::table('notification_history')->insert([
                    'notification_type_id' => $input['notification_type'],
                    'date' => $input['date'],
                    'role_id' => $this->user->school_id,
                    'role' => 'student',
                    'message_type' => 'text_msg',
                    'school_id' => $this->user->school_id
                ]);
            }
            // send notification
            Event::fire(new SendNotification($input));

            // foreach($students as $student)
            // {
            //     $device = \DB::table('push_notification')->where('role_id', $student->id)->where('role', 'student')->first();
            //     $notification_content = \DB::table('notification_type')->where('id', $input['notification_type'])->first();
            //     // dd($device, $input);
            //     if($device and $device->device_id != '')
            //     {
            //         \DB::table('notification_history')->insert([
            //             'notification_type_id' => $input['notification_type'],
            //             'date' => $input['date'],
            //             'role_id' => $student->id,
            //             'role' => 'student',
            //             'message_type' => 'push_msg',
            //             'school_id'=>$this->user->school_id
            //         ]);
            //         $apiKey = 'AAAA8JlvU7U:APA91bF3mOJrumY6dnjOKqZ6Iy2-w4-Pn7tyDJONlt0SmuDGyCmzhsM2T7A79x4KOF1sz_voAU7cex3xH-5WO5ZMbKSkmlG4vP7vp6eOmFWH475nwaJd9AaUr62FMTQU3bL4F4VjaPDY';
            //         $client = new Client();
            //         $client->setApiKey($apiKey);
            //         $client->injectHttpClient(new \GuzzleHttp\Client());
            //         $note = new Notification($notification_content->title, $notification_content->description);
            //         $note->setIcon('notification_icon_resource_name')
            //             ->setColor('#ffffff')
            //             ->setSound('default')
            //             ->setBadge(1);
            //         $message = new Message();
            //         $message->addRecipient(new Device($device->device_id));
            //         $message->setNotification($note)
            //             ->setData(array('someId' => 111));
            //         $response = $client->send($message);
            //         // var_dump($response->getStatusCode());
            //         // dd($device, $notification_content);
            //     }
            //     else
            //     {
            //         \DB::table('notification_history')->insert([
            //             'notification_type_id' => $input['notification_type'],
            //             'date' => $input['date'],
            //             'role_id' => $student->id,
            //             'role' => 'student',
            //             'message_type' => 'text_msg',
            //             'school_id'=>$this->user->school_id
            //         ]);
            //         file_get_contents('http://103.16.101.52/sendsms/bulksms?username=hins-demo&password=123456&type=0&dlr=1&destination=91'.$student->contact_no.'&source=TSTSMS&message='.$notification_content->description);
            //     }
            // }
            // foreach($students as $student)
            // {
            //     $parent = StuParent::where('id', $student->parent_id)->where('school_id', $this->user->school_id)->first();
            //     if($parent)
            //     {
            //         $device = \DB::table('push_notification')->where('role_id', $student->id)->where('role', 'student')->first();
            //         if($device and $device->device_id != '')
            //         {
            //             $apiKey = 'AAAA8JlvU7U:APA91bF3mOJrumY6dnjOKqZ6Iy2-w4-Pn7tyDJONlt0SmuDGyCmzhsM2T7A79x4KOF1sz_voAU7cex3xH-5WO5ZMbKSkmlG4vP7vp6eOmFWH475nwaJd9AaUr62FMTQU3bL4F4VjaPDY';
            //             $client = new Client();
            //             $client->setApiKey($apiKey);
            //             $client->injectHttpClient(new \GuzzleHttp\Client());
            //             $note = new Notification($notification_content->title, $notification_content->description);
            //             $note->setIcon('notification_icon_resource_name')
            //                 ->setColor('#ffffff')
            //                 ->setSound('default')
            //                 ->setBadge(1);
            //             $message = new Message();
            //             $message->addRecipient(new Device($device->device_id));
            //             $message->setNotification($note)
            //                 ->setData(array('someId' => 111));
            //             $response = $client->send($message);
            //             \DB::table('notification_history')->insert([
            //                 'notification_type_id' => $input['notification_type'],
            //                 'date' => $input['date'],
            //                 'role_id' => $parent->id,
            //                 'role' => 'parent',
            //                 'message_type' => 'push_msg',
            //                 'school_id'=>$this->user->school_id
            //                 ]);
            //         }
            //         else
            //         {
            //             file_get_contents('http://103.16.101.52/sendsms/bulksms?username=hins-demo&password=123456&type=0&dlr=1&destination=91'.$parent->contact_no.'&source=TSTSMS&message='.$notification_content->description);
            //             \DB::table('notification_history')->insert([
            //                 'notification_type_id' => $input['notification_type'],
            //                 'date' => $input['date'],
            //                 'role_id' => $parent->id,
            //                 'role' => 'parent',
            //                 'message_type' => 'text_msg',
            //                 'school_id'=>$this->user->school_id
            //                 ]);
            //         }
            //     }
            // }

            $input['success'] = 'Notification Sent Successfully';
            return \Redirect::back()->withInput($input);
        }
    }

    public function deleteNotificationHistory($id) {
        \DB::table('notification_history')->where('id', $id)->delete();
        $msg['success'] = 'Success to delete Notification';
        return \Redirect::back()->withInput($msg);
    }

    public function deleteNotification($id) {
        \DB::table('notification_type')->where('id', $id)->delete();
        $msg['success'] = 'Success to delete Notification';
        return \Redirect::back()->withInput($msg);
    }

    public function notice() {
        return view('users.notice.notice');
    }

    public function postNotice() {
        $request = \Request::all();
        $userError = ['notice' => 'Notice', 'date' => 'Date', 'type' => 'User Type', 'image' => 'Image'];
        $validator = \Validator::make($request, [
            'notice' => 'required',
            'date' => 'required',
            'type' => 'required',
            'image' => 'image'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator)->withInput($request);
        } else {
            if (isset($request['image'])) {
                $image = $request['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName = $image->getClientOriginalName();
                $directory = 'notice';
                $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;
                $image = \Image::make($image);
                $image->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($directory . '/' . $filename);
                $imagefile = $directory . '/' . $filename;
            } else {
                $imagefile = '';
            }

            foreach ($request['type'] as $type) {
                \DB::table('notice')->insert([
                    'school_id' => $this->user->school_id,
                    'date' => date('d-m-Y', strtotime($request['date'])),
                    'notice' => $request['notice'],
                    'image' => $imagefile,
                    'type' => $type
                ]);
            }
            $request['success'] = 'Notice is added Successfully';
            return \Redirect::back()->withInput($request);
        }
    }

    public function getNotice() {
        $notices = \DB::table('notice')->orderBy('id', 'DESC')->get();
        return view('users.notice.list', compact('notices'));
    }

    public function deleteNotice($id) {
        \DB::table('notice')->where('id', $id)->delete();
        $input['success'] = 'Notice is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function message() {
        return view('users.message.index');
    }
    public function drivertrack()
    {
        /*$drivertrack = \DB::table('driver_track')->where('driver_track.school_id', \Auth::user()->school_id)->whereDate('created_at', '=', date('Y-m-d'))->get();
       dd($drivertack);
        if(!empty($drivertrack)){
            return view('driver.index');
        }
        else
        {
            return \Redirect::back();
        }*/
         $drivertrack = \DB::table('driver_track')->where('driver_track.school_id', \Auth::user()->school_id)->whereDate('created_at', '=', date('Y-m-d'))->get();
        if(!empty($drivertrack)){
            $school_id = Auth::user()->school_id;
            $schoolObj = \DB::table('school')->where('id', $school_id)->first();
            $mapKey = $schoolObj->map_key;
             if(!empty($mapKey)){
            return view('driver.index',compact('school_id','mapKey'));
            }else{
            return view('driver.index');
            }
           
        }
        else
        {
            return \Redirect::back();
        }

    }
    public function driverslocation(){
        $drivertrack = \DB::table('driver_track')->select('driver_track.*', \DB::raw('MAX(id) AS id'))
            ->where('driver_track.school_id', \Auth::user()->school_id)
            ->whereDate('created_at', '=', date('Y-m-d'))
            ->groupby('bus_id')
            ->get();
        return \api::success(['data' => $drivertrack]);

    }

    public function trasport() {
        $bus = \DB::table('bus')->where('school_id', \Auth::user()->school_id)->get();
        $mapping = \DB::table('student')->where('student.school_id', \Auth::user()->school_id)->join('bus', 'student.bus_id', '=', 'bus.id')
            ->join('bus_stop', 'student.bus_stop_id', '=', 'bus_stop.id')
            ->join('class', 'student.class_id', '=', 'class.id')
            ->select('student.id', 'student.name', 'student.registration_no', 'class.class', 'bus.route', 'bus.bus_no', 'bus_stop.stop')->get();

        return view('users.transport.index', compact('bus', 'mapping'));
    }

    public function getStopBus($id) {
        $stops = \DB::table('bus_stop')->where('bus_id', $id)->orderBy('stop_index', 'ASC')->get();
        return api(['data' => $stops]);
    }

    public function postMapping() {
        $input = \Request::all();

        if (!isset($input['reg_no'])) {
            $msg['error'] = 'Please Enter Registration No.';
            return \Redirect::back()->withInput($msg);
        } else {
            $student = \DB::table('student')->where('registration_no', $input['reg_no'])->where('school_id', \Auth::user()->school_id)->first();
            if (!$student) {
                $msg['error'] = 'Invalid Registration No.';
                return \Redirect::back()->withInput($msg);
            } else {
                if (!isset($input['bus_id']) OR ! $input['bus_id'] AND ! isset($input['stop']) OR ! $input['stop']) {
                    $msg['error'] = 'Please Choose Bus and Stop';
                    return \Redirect::back()->withInput($msg);
                } else {
                    \DB::table('student')->where('id', $student->id)
                        ->where('school_id', \Auth::user()->school_id)
                        ->update([
                            'bus_id' => $input['bus_id'],
                            'bus_stop_id' => $input['stop']
                        ]);
                    $msg['success'] = 'Success to Submit';
                    return \Redirect:: back()->withInput($msg);
                }
            }
        }
    }

    public function deleteMapping($id) {
        \DB::table('student')->where('id', $id)
            ->where('school_id', \Auth::user()->school_id)
            ->update([
                'bus_id' => '',
                'bus_stop_id' => ''
            ]);
        $msg['success'] = 'Success to delete';
        return \Redirect:: back()->withInput($msg);
    }

    public function feeFrequency() {
        $frequencies = FeeFrequency::where('school_id', $this->user->school_id)->get();
        return view('users.fee_frequency.frequency', compact('frequencies'));
    }

    public function postFrequency(FeeFrequency $frequency) {
        $input = \Request::all();
        $userError = ['frequency' => 'Frequency'];
        $validator = \Validator::make($input, ['frequency' => 'required'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator)->withInput($input);
        return $frequency->doPostFrequency($input, $this->user);
    }

    public function deleteFrequency($id) {
        FeeFrequency::where('id', $id)->delete();
        $input['success'] = 'Frequency deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function editFrequency($id) {
        $frequency = FeeFrequency::where('id', $id)->first();
        return view('users.fee_frequency.edit', compact('frequency'));
    }

    public function updateFrequency(FeeFrequency $frequency) {
        $input = \Request::all();
        $userError = ['frequency' => 'Frequency'];
        $validator = \Validator::make($input, ['frequency' => 'required'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator)->withInput($input);
        return $frequency->doUpdateFrequency($input, $this->user);
    }

    public function postInstallment(Request $request) {
        $request_data = $request->requestData;
        $inserted_id = [];
        ini_set('always_populate_raw_post_data', -1);
        foreach ($request_data as $key => $value) {

            $date = empty($value['due_date']) ? NULL : date_format(new DateTime($value['due_date']), 'Y-m-d');
            $inserted_id [] = Installment::insertGetId([
                'school_id' => Auth::user()->school_id,
                'amount' => $value['amount'],
                'due_date' => $date,
                'Installment_type' => $value['Installment_type'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        return response()->json($inserted_id);
    }

    public function feeStructureByclass($session,$class,FeeStructuree $fee){
        $school_id = \Auth::user()->school_id;
        $fees = $fee->getFeeByclass($school_id, $session, $class);
        $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('id',$fees[0]->class_id)->first();
        return view('users.fee_structure.view-fee', compact('fees','classes'));
    }

     public function deleteFeeStructure(Request $request){
         $id = $request->input('feeId');
         feestructuree::where('id',$id)->delete();
         $msg['success'] = 'deleted successfully';
         return \Redirect::back()->withInput($msg);
     }

   // public function deleteFeeStructure(Request $request){
        //$id = $request->input('feeId');
       // $alreadyCollectionFeeCount = Payment::where('fee_id',$id)->count();
       // if($alreadyCollectionFeeCount == 0){
           // feestructuree::where('id',$id)->delete();
          //  $msg['success'] = 'deleted successfully';
       // }else{
           // $msg['error'] = 'cannot delete this fee structure';
       // }

       // return \Redirect::back()->withInput($msg);
   // }

    public function addBoardingPointfees()
    {
         $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
        $getbus = \DB::table('busdetails')
                        ->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$session['id'])
                        ->get();
        //bus id , route id and boarding id
                $allboarding = \DB::table('boarding')->where('boarding.school_id', $this->user->school_id)
            ->where('boarding.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('route_details', 'boarding.route_id', '=', 'route_details.id')
            ->leftJoin('busdetails', 'boarding.bus_id', '=', 'busdetails.id')
            ->select
            (
                'boarding.id',
                'boarding.boarding',
                'route_details.route_name',
                'busdetails.bus_no'
            )
            ->orderBy('boarding.id', 'ASC')
            ->get();
        foreach($allboarding as $boardin)
        {
            $boardings[] = array(
                'boardingid' => $boardin->id,
                'boarding' => $boardin->boarding,
                'route' => $boardin->route_name,
                'bus_no' => $boardin->bus_no,
            );
        }
       //dd($allboarding,$boardings);
        return view('users.fee_structure.sion.busfeeDetails', compact('getbus','boardings'));
    }
public function deleteboarddetails() 
     {
         $input = \Request::all();
        //dd($input);
        if($input['boardId']){
            $getbusid =\DB::table('sionfee_structure')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('board_id', $input['boardId'])->first();
         if(empty($getbusid))
         {
        \DB::table('boarding')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id', $input['boardId'])->delete();
         $msg['success']= 'Boarding Deleted Successfully !!!';
         return \Redirect::back()->withInput($msg);
         }else{
            $msg['error'] = 'Some student added to this boarding . So You Can not delete this Boarding ';
            return \Redirect::back()->withInput($msg);
         }
     }
     }

    public function studentbusfeedetails()
    {
        $input = \Request::all();
        $bus_no=$input['busno'];
        $routename=$input['routename'];
        $boardpoint=$input['boardname'];
        $regno=$input['reg_no'];
        $stud_id=$input['studentsid'];
        $class=$input['class'];
       // dd($input);
        $getboard_amt = \DB::table('boarding')
                        ->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->where('route',$input['routename'])
                        ->where('bus_no',$input['busno'])
                        ->where('boarding',$input['boardname'])
                        ->first();
            $busfee = 'Bus Fees';

        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
            
                 DB::table('sionfee_structure')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'session_id'=> $session['id'],
               'reg_no'=> $regno,
               'student_id'=> $stud_id,
               'fees_name'=> $busfee,
               'boarding'=> $boardpoint,
               'class'=>$class,
               'amount'=> $getboard_amt->bus_fee
                                    
                )
                 );
                  $msg= 'Your Fees Structure Created Successfully !!!';
                  echo $msg;
        
            //return view('users.fee_structure.sion.student_buspaymentstr',compact('msg'));
        
    }


   public function busfeedetails()
    {

         $input = \Request::all();
        $bus_id=$input['bus'];
        $rout_id=$input['routes'];
        $boardpoint=$input['boardpoint'];
        $amt=$input['amt'];

        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
        $getbus = \DB::table('busdetails')
                        ->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->where('id',$input['bus'])
                        ->first();
        $getroute = \DB::table('route_details')
                        ->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->where('bus_id',$input['bus'])
                        ->where('id',$input['routes'])
                        ->first();
            foreach ($boardpoint as $key => $value) {
                 DB::table('boarding')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'session_id'=> $session['id'],
               'bus_id'=> $input['bus'],
               'route_id'=> $input['routes'],
               'route'=> $getroute->route_name,
               'bus_no'=> $getbus->bus_no,
               'boarding'=> $boardpoint[$key],
               'bus_fee'=> $amt[$key]
                                    
                )
                 );
            }
           
        $input['success'] = 'Boarding is added successfully';
        return \Redirect::back()->withInput($input);
    }

    

     public function viewbusfeedetails()
    {

        

        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
            //dd('hi');

           
            $getboarding = \DB::table('boarding')
                        ->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$session['id'])
                        ->get();
                        //dd('gwt',$getboarding);
        foreach ($getboarding as $key => $value) {
            $busroute[]=$value->route;
             $busno[]=$value->bus_no;
             $busid[]=$value->id;
             $boardpt[]=$value->boarding;
             $busfees[]=$value->bus_fee;
             $boardid[]=$value->id;
        }
        //dd($boardid);
        
        return view('users.fee_structure.sion.viewbusfeeDetails', compact('busroute', 'busno','busid','boardpt','busfees','boardid'));
    }
     public function deletebusfeedetails() 
     {
         $input = \Request::all();
         $feeId= $input['feeId'];
        \DB::table('boarding')->where('id', $input['feeId'])->delete();
         $input['success'] = 'Boarding is deleted successfully';
        return \Redirect::back()->withInput($input);
     }
     public function studentfeeindexstr()
    {

        return view('users.fee_structure.sion.studentfeeindex');
    }
    public function singlestudentfeestr()
    {
         $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
        return view('users.fee_structure.sion.singlestudent_paymentstr', compact('classes', 'session'));
    }
public function studentPaymentstr()
    {
      $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
            $getFee=\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    //->where('class',$input['class'])
                    ->where('student_id','=','0')
                    ->get();
        return view('users.fee_structure.sion.student_paymentstr', compact('classes', 'session','getFee'));
    }
     /*public function busPaymentstr()
    {
        
       $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->get();
            
        return view('users.fee_structure.sion.student_buspaymentstr', compact('classes', 'session'));
    }*/

    public function postStuwisedetailsforstuPayment()
    {
       $input = \Request::all();
        $reg_no=$input['regno'];
        $busfee = \DB::table('boarding')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->get();

                        
        $students = \DB::table('student')->where('registration_no', $input['regno'])
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id', \Auth::user()->school_id)->first();

        //dd('hi',$students,$busfee);
                foreach ($busfee as $key => $value) {
                    $boarding[]=$value->boarding;
                    $busroute[]=$value->route;
                    $busno[]=$value->bus_no;
                }

                $classData = addClass::select('class')->where('id',$students->class_id)->where('session_id',$this->active_session->id)
            ->first();
            $sectionData = Section::where('id', $students->section_id)
                        ->where('session_id',$this->active_session->id)
                        ->where('class_id', $students->class_id)->first();
       
            
        return view('users.fee_structure.sion.singlestudent_busfeestr' , compact('students','boarding','busroute','busno','classData','sectionData','reg_no'));
    }

    public function postClasswisedetailsforstuPayment(Request $request,Session $session)
    {
        $input = \Request::all();
        $class = $input['class'];
        $section = $input['section'];
        $payment_type=$input['payment_type'];
         $session_id = $session->get_active_session_id(Auth::user()->school_id);

                if($class )
                {
                    $classData = addClass::where('id', $class)
                        ->where('session_id',$session_id->id)->first();
                        
                    $sectionData = Section::where('id', $section)
                        ->where('session_id',$session_id->id)
                        ->where('class_id', $class)->first();
                    $sessionData = Session::where('id', $session_id->id)->first();
         
                    $students = \DB::table('student')
                        ->where('student.school_id', \Auth::user()->school_id)
                        ->where('student.session_id',$session_id->id)
                        ->where('student.class_id', $class)
                        ->where('student.section_id', $section)
                        ->get();
                }
    return view('users.fee_structure.sion.studentdetails', compact('students','sectionData','sessionData','classData','payment_type'));
}

public function poststudentwisefeedetails() 
     {
        $input = \Request::all();
        //dd('hi',$input);
        $studentsid=$input['student'];
        //$session=$input['session'];
                    $students = \DB::table('student')->where('id', $input['student'])
                            ->where('session_id',$this->active_session->id)//updated 14-4-2018
                            ->where('school_id', \Auth::user()->school_id)->first();

        $classData = addClass::select('class')->where('id',$students->class_id)->where('session_id',$this->active_session->id)
                                ->first();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
                            ->first();
            //dd($session->id);
        $class=$classData->class;
        $reg_no=$students->registration_no;
        
        $amt=$input['fee_amount'];
        $name=$input['fee_name'];
        $term_type=$input['payment_type'];

        $usererror = [
            //'session' => 'Session',
            //'class' => 'class ',
            'section' => 'Section',
            'student' => 'Student ',
            'fee_name' => 'Fee Name',
            'payment_type' => 'Payment Type ',
            'fee_amount' => 'Amount'
        ];
        $validator = \Validator::make($input, [
            //'session' => 'required',
            //'class' => 'required',
            'section' => 'required',
            'fee_name' => 'required',
            'student' => 'required',
            'payment_type' => 'required',
            'fee_amount' => 'required|numeric'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', "Given field is incorrect");
        }
        else
        {
       $result= DB::table('sionfee_structure')->insert(
                array(
                'school_id' => Auth::user()->school_id,
                'session_id' => $session->id,
                'class' => $class,
                'class_id' => $students->class_id,
                'student_id' => $studentsid,
                'payment_type'=>$term_type,
                'fees_name'=>$name,
                'reg_no'=>$reg_no,
                'amount'=>$amt,
                ));
       if($result)
            {
                $msg['success'] = ' Fee Structure Added Successfully ';
            }
            else{
                $msg['error'] = ' Error in adding Fee Structure ';
            }

            return \Redirect::back()->withInput($msg);
        }
        
     }
public function multiblestudent_paymentstr()
    {
         
        return view('users.fee_structure.sion.multiblestudent_paymentstr');
    }
public function busPaymentstr()
    {
        
         $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->get();
            foreach ($session as $key => $value) {
                $sessionid=$value->id;
                $sessionname=$value->session;
            }
        $busfee = \DB::table('boarding')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)->groupBy('bus_no')
                        ->get();
                foreach ($busfee as $key => $value) {
                    $boarding[]=$value->boarding;
                    $busroute[]=$value->route;
                    $busno[]=$value->bus_no;
                }
        $busses = \DB::table('busdetails')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->get();

        //bus id , route id and boarding id
                $allboarding = \DB::table('boarding')->where('boarding.school_id', $this->user->school_id)
            ->where('boarding.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('route_details', 'boarding.route_id', '=', 'route_details.id')
            ->leftJoin('busdetails', 'boarding.bus_id', '=', 'busdetails.id')
            ->select
            (
                'boarding.id',
                'boarding.boarding',
                'route_details.id as route_id',
                'route_details.route_name',
                'busdetails.id as bus_id',
                'busdetails.bus_no'
                
            )
            ->orderBy('boarding.id', 'ASC')
            ->get();
        foreach($allboarding as $boardin)
        {
            $boardings[] = array(
                'boardingid' => $boardin->id,
                'boarding' => $boardin->boarding,
                'route_id' => $boardin->route_id,
                'route' => $boardin->route_name,
                 'bus_id' => $boardin->bus_id,
                'bus_no' => $boardin->bus_no,
            );
        }

           
        return view('users.fee_structure.sion.singlestudent_buspaymentstr', compact('classes', 'session','sessionid','sessionname','busno','busroute','busfee','allboarding','boardings','busses'));
    }

    public function busPaymentindex()
    {
        return view('users.fee_structure.sion.busfeeindex');
    }

    public function multibusPaymentstr()
    {
         $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->get();
            foreach ($session as $key => $value) {
                $sessionid=$value->id;
                $sessionname=$value->session;
            }
        $busfee = \DB::table('boarding')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)->groupBy('bus_no')
                        ->get();
                foreach ($busfee as $key => $value) {
                    $boarding[]=$value->boarding;
                    $busroute[]=$value->route;
                    $busno[]=$value->bus_no;
                }
        $busses = \DB::table('busdetails')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->get();

        //bus id , route id and boarding id
                $allboarding = \DB::table('boarding')->where('boarding.school_id', $this->user->school_id)
            ->where('boarding.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('route_details', 'boarding.route_id', '=', 'route_details.id')
            ->leftJoin('busdetails', 'boarding.bus_id', '=', 'busdetails.id')
            ->select
            (
                'boarding.id',
                'boarding.boarding',
                'boarding.bus_fee',
                'route_details.id as route_id',
                'route_details.route_name',
                'busdetails.id as bus_id',
                'busdetails.bus_no'
                
            )
            ->orderBy('boarding.id', 'ASC')
            ->get();
        foreach($allboarding as $boardin)
        {
            $boardings[] = array(
                'boardingid' => $boardin->id,
                'boarding' => $boardin->boarding,
                'bus_fee' => $boardin->bus_fee,
                'route_id' => $boardin->route_id,
                'route' => $boardin->route_name,
                 'bus_id' => $boardin->bus_id,
                'bus_no' => $boardin->bus_no,
            );
        }

           
        return view('users.fee_structure.sion.student_buspaymentstr', compact('classes', 'session','sessionid','sessionname','busno','busroute','busfee','allboarding','boardings','busses'));
    }
public function deletboardSionFeeStructure() 
     {
         $input = \Request::all();
         $feeId= $input['feeId'];
         //dd($input);
        $getFeeid =\DB::table('sionfee_structure')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('board_id', $input['feeId'])->first();
         if(empty($getFeeid))
         {
        \DB::table('boarding')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id', $input['feeId'])->delete();
         }else{
            $msg['error'] = 'Some Students added to Bus fee structure for this Boarding. So You Can not delete this Boarding ';
            return \Redirect::back()->withInput($msg);
         }
        
        $msg['success']= 'Bus Fees Structure Deleted Successfully !!!';
         return \Redirect::back()->withInput($msg);
         
     }
 public function poststudentwisebusfeedetails() 
     {
        $input = \Request::all();
       //dd('hi',$input);
        $studentsid=$input['student'];
        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->get();
        foreach ($getSession as $key => $value) {
            $sessionid=$value->id;
            $session=$value->session;
        }
        
                    $students = \DB::table('student')->where('id', $input['student'])
                            ->where('session_id',$this->active_session->id)//updated 14-4-2018
                            ->where('school_id', \Auth::user()->school_id)->first();

                    $classData = addClass::select('class')->where('id',$students->class_id)->where('session_id',$this->active_session->id)
                                ->first();
            
        $class=$classData->class;
        $reg_no=$students->registration_no;
        $boarding=$input['board'];
        $amt=$input['fee_amount'];
        $name=$input['fee_name'];
         $route_id=$input['routes'];
        $bus_id=$input['bus'];
        //$route=$input['route'];
        //$busno=$input['busno'];
        $route_name = \DB::table('route_details')->where('id', $input['routes'])
                            ->where('session_id',$this->active_session->id)//updated 14-4-2018
                            ->where('school_id', \Auth::user()->school_id)->first();
            $bus_no = \DB::table('busdetails')->where('id', $input['bus'])
                            ->where('session_id',$this->active_session->id)//updated 14-4-2018
                            ->where('school_id', \Auth::user()->school_id)->first();
    $boardname = \DB::table('boarding')->where('id', $input['board'])
                            ->where('session_id',$this->active_session->id)//updated 14-4-2018
                            ->where('school_id', \Auth::user()->school_id)->first();
       $result= DB::table('sionfee_structure')->insert(
                array(
                'school_id' => Auth::user()->school_id,
                'session_id' => $sessionid,
                'class' => $class,
                'class_id' => $students->class_id,
                'student_id' => $studentsid,
                'boarding'=>$boardname->boarding,
                'bus_route'=>$route_name->route_name,
                'bus_no'=>$bus_no->bus_no,
                'board_id'=>$input['board'],
                'bus_id'=>$input['bus'],
                'route_id'=>$input['routes'],
                'fees_name'=>$name,
                'reg_no'=>$reg_no,
                'amount'=>$amt,
                ));

       if($result)
            {
                $msg['success'] = ' Bus Fee Structure Added Successfully ';
            }
            else{
                $msg['error'] = ' Error in adding Fee Structure ';
            }

            return \Redirect::back()->withInput($msg);
     }

public function viewSionFeeStructure() 
     {
        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->get();
        $classes = addClass::select('class','id')->where('session_id',$this->active_session->id)
            ->get();
        //dd('getSession',$getSession,'classes',$classes);
            return view('users.fee_structure.sion.view_feestructure', compact('classes','getSession'));
     }

     public function viewSionFeeStructuredetails() 
     {
        //dd('hi');
        $input = \Request::all();
        $session_id= $input['session_id'];
        $class= $input['class'];

        $getFee=\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('student_id','=','0')
                    ->get();
                    $payment_type=array();
                    $fees_name=array();
                    $amount=array();

                    foreach ($getFee as $key => $value) {
                        $payment_type[]=$value->payment_type;
                        $fees_name[]=$value->fees_name;
                        $amount[]=$value->amount;
                        $id[]=$value->id;

                    }
        //dd('payment_type',$payment_type,'fees_name',$fees_name,'amount',$amount);
        
            return view('users.fee_structure.sion.view_feestructuredetails', compact('payment_type','fees_name','amount','class','id'));
     }

     public function deletSionFeeStructure() 
     {
         $input = \Request::all();
         $feeId= $input['feeId'];
         
        $getFeeid =\DB::table('sionfee_collection')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('fee_id', $input['feeId'])->first();
         if(empty($getFeeid))
         {
        \DB::table('sionfee_structure')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id', $input['feeId'])->delete();
         }else{
            $msg1 = 'Few students paid to this Fees. So Fee Can not be deleted ';
        
         return view('users.fee_structure.sion.view_feestructuredetails',compact('msg1'));
         }
        
        $msg= 'Fees Structure Deleted Successfully !!!';
        
         return view('users.fee_structure.sion.view_feestructuredetails',compact('msg'));
     }
public function deleboardingSionFeeStructure() 
     {
         $input = \Request::all();
         $feeId= $input['feeId'];
         //dd($input);
        $getFeeid =\DB::table('sionfee_collection')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('fee_id', $input['feeId'])->first();
         if(empty($getFeeid))
         {
        \DB::table('sionfee_structure')->where('school_id', Auth::user()->school_id)->where('session_id',$this->active_session->id)->where('id', $input['feeId'])->delete();
         }else{
            $msg['error'] = 'Payment already paid to this fees name. So You Can not delete this Boarding fees ';
            return \Redirect::back()->withInput($msg);
         }
        
        $msg['success']= 'Fees Structure Deleted Successfully !!!';
         return \Redirect::back()->withInput($msg);

         
     }
public function postClasswisePaymentfee(Session $session )
    {
        $input = \Request::all();
        //dd('hi',$input);
        $termType=$input['term_type'];
        $feeName=$input['name'];
        $feeAmt=$input['amt'];
        $class=$input['class'];

        $termTypeids = array();
        $feeNameids = array();
        $feeAmtids = array();

        foreach($termType as $tType)
        {
            $termTypeids[]=$tType;
        }
        foreach($feeName as $feename)
        {
            $feeNameids[]=$feename;
        }
         foreach($feeAmt as $amts)
        {
            $feeAmtids[]=$amts;
        }
        $student_id=0;
        $classe = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 20-4-2018
            ->where('id',$class)
            ->first();
        $session_id = $session->get_active_session_id(Auth::user()->school_id);
//dd($input,$classe);
        foreach( $feeAmtids as $key => $n ) {
           $result= DB::table('sionfee_structure')->insert(
                array(
                'school_id' => Auth::user()->school_id,
                'session_id' => $session_id->id,
                'class' => $classe->class,
                'class_id' => $classe->id,
                'student_id' => $student_id,
                'payment_type'=>$termType[$key],
                'fees_name'=>$feeName[$key],
                'amount'=>$feeAmt[$key],
                ));
        }

        if($result)
            {
                $msg['success'] = ' Class Wise Fee Structure Added Successfully ';
            }
            else{
                $msg['error'] = ' Error in adding Fee Structure ';
            }

            return \Redirect::back()->withInput($msg);
        }

    public function previousYearPayment()
    {
        $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->get();
        return view('users.fee_structure.previous_payment', compact('classes', 'session'));
    }

    /** @ GET STUDENTS @ **/
    public function previousYearPaymentStudent()
    {
        $class_id = \Request::get('srclass');
        $section_id = \Request::get('srsection');
        $currentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        $getStudents = \DB::table('student')->where('class_id',$class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('section_id',$section_id)
            ->where('school_id',\Auth::user()->school_id)
            ->select('id','name')
            ->get();
        return $getStudents;
    }

    public function postPreviousYearPayment()
    {
        $input = \Request::all();
      //  dd($input);
        $usererror = [
            'session' => 'Session',
            'class' => 'class ',
            'section' => 'Section',
            'student' => 'Student ',
            'fee_name' => 'Fee Name',
            'payment_type' => 'Payment Type ',
            'fee_amount' => 'Amount'
        ];
        $validator = \Validator::make($input, [
            'session' => 'required',
            'class' => 'required',
            'section' => 'required',
            'fee_name' => 'required',
            'student' => 'required',
            'payment_type' => 'required',
            'fee_amount' => 'required|numeric'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', "Given field is incorrect");
        }
        else
        {
            if(empty($input['payment_last_date']))
            {
                $input['payment_last_date'] = 0;
            }
            if(empty($input['fine']))
            {
                $input['fine'] = 0;
            }
            $result = FeeStructure::insert([
                'school_id' => Auth::user()->school_id,
                'class_id' => $input['class'],
                'session' => $input['session'],
                'fees_name' => $input['fee_name'],
                'student_type'=> 'Existing',
                'payment_type' => $input['payment_type'],
                'installment_id' => '0',
                'student_id' =>$input['student'],
                'amount' => $input['fee_amount'],
                'last_date' => $input['payment_last_date'],
                'fine' => $input['fine']
            ]);
            if($result)
            {
                $msg['success'] = ' Fee Structure Added Successfully ';
            }
            else{
                $msg['error'] = ' Error in adding Fee Structure ';
            }

            return \Redirect::back()->withInput($msg);
        }

    }


    public function postFeeStructure(Request $request, FeeStructuree $fee) {
        $rows = $request->input('rows');
        for ($i = 0; $i < $rows; $i++) {
            $usererror = [
                'class_id' => 'class id',
                'fee_name_' . $i . '' => 'Fee Name',
                'student_type_' . $i . '' => 'Student Type',
                'payment_type_' . $i . '' => 'Payment Type ',
                'amount_' . $i . '' => 'Amount'
            ];
            $validator = \Validator::make($request->all(), [
                'class_id' => 'required',
                'fee_name_' . $i . '' => 'required',
                'student_type_' . $i . '' => 'required',
                'payment_type_' . $i . '' => 'required',
                'amount_' . $i . '' => 'required|numeric'
            ], $usererror);
            $validator->setAttributeNames($usererror);
            if ($validator->fails()) {
                return redirect()->back()->with('error', "Given filed is incorrect");
            }
        }
        return $fee->addFees($request, Auth::user());
    }

    public function feeStructure()
    {
        /*$fields = \DB::table('fee_head')->where('school_id', $this->user->school_id)->orderBy('id', 'DESC')->paginate(5);
        return view('users.fee_structure.structure', compact('fields'));*/
        $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 20-4-2018
            ->get();
        $session = Session::where('school_id', Auth::user()->school_id)
            ->where('active', 1)->get();
        return view('users.fee_structure.structure_v2', compact('classes', 'session'));
    }

    public function postStructure(FeeStructure $structure) {
        $input = \Request::all();

        $userError = ['fee_head' => 'Fee Head'];
        $validator = \Validator::make($input, ['fee_head_type' => 'required', 'fee_head' => 'required'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator)->withInput($input);
        else
            \DB::table('fee_head')->insert([
                'school_id' => $this->user->school_id,
                'fee_head_type' => $input['fee_head_type'],
                'fee_head' => $input['fee_head']
            ]);
        $msg['success'] = 'Success to Submit';
        return \Redirect::back()->withInput($msg);
    }

    public function listStructure() {
        $structures = FeeStructure::where('school_id', $this->user->school_id)->get();
        return view('users.fee_structure.list', compact('structures'));
    }

    public function deleteStructure($id) {
        \DB::table('fee_head')->where('id', $id)->delete();
        $input['success'] = 'Fee Head is deleted successfully';
        return Redirect::back()->withInput($input);
    }

    public function editStructure($id) {
        $field = FeeStructure::where('id', $id)->first();
        return view('users.fee_structure.edit', compact('field'));
    }

    public function updateStructure(FeeStructure $structure) {
        $input = \Request::all();
        $userError = ['structure' => 'Structure'];
        $validator = \Validator::make($input, ['structure' => 'required'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator)->withInput($input);
        return $structure->doUpdateStructure($input, $this->user);
    }

    public function admissionFee() {
        $fields = \DB::table('fee_admission')->where('fee_admission.school_id', $this->user->school_id)
            ->join('class', 'fee_admission.class_id', '=', 'class.id')
            ->select('fee_admission.id', 'fee_admission.amount', 'class.class')
            ->orderBy('fee_admission.id', 'DESC')->paginate(10);
        return view('users.fee_structure.admission_fee', compact('fields'));
    }

    public function postAdmissionFee() {
        $input = \Request::all();

        \DB::table('fee_admission')->insert([
            'school_id' => $this->user->school_id,
            'class_id' => $input['class'],
            'amount' => $input['amount']
        ]);
        $msg['success'] = 'Success to submit Admission Fee Amount';
        return \Redirect::back()->withInput($msg);
    }

    public function deleteAdmissionFee($id) {
        \DB::table('fee_admission')->where('id', $id)->delete();
        $msg['success'] = 'Success to Delete Admission Fee';
        return \Redirect::back()->withInput($msg);
    }

    public function getFeeHead($type) {
        $get = \DB::table('fee_head')
            ->where('school_id', \Auth::user()->school_id)
            ->where('fee_head_type', $type)
            ->get();
        return api(['data' => $get]);
    }

    public function feeHeadAmount() {
        $fees = \DB::table('fee_head')->where('school_id', $this->user->school_id)->orderBy('fee_head', 'ASC')->get();
        $fee_amount = \DB::table('fee_head_amount')->where('fee_head_amount.school_id', $this->user->school_id)
            ->join('fee_head', 'fee_head_amount.fee_head_id', '=', 'fee_head.id')
            ->join('class', 'fee_head_amount.class_id', '=', 'class.id')
            ->select('fee_head_amount.id', 'fee_head_amount.amount', 'fee_head.fee_head', 'class.class', 'fee_head.fee_head_type')
            ->paginate(10);
        $selectClass = \Session::get('selectedClass');
        $selectHeadType = \Session::get('selectedHeadType');
        $selectHead = \Session::get('selectedHead');
        return view('users.fee_structure.fee-head', compact('fees', 'fee_amount', 'selectHead', 'selectClass', 'selectHeadType'));
    }

    public function feeHeadAmountPost() {
        $input = \Request::all();

        $val = \DB::table('fee_head_amount')
            ->where('school_id', \Auth::user()->school_id)
            ->where('fee_head_id', $input['fee_head'])
            ->where('class_id', $input['class'])
            ->get();
        if (!$val) {
            \DB::table('fee_head_amount')->insert([
                'school_id' => $this->user->school_id,
                'fee_head_id' => $input['fee_head'],
                'class_id' => $input['class'],
                'amount' => $input['amount']
            ]);
            \Session::put('selectedClass', $input['class']);
            \Session::put('selectedHead', $input['fee_head']);
            $msg['success'] = 'Success to Submit Fee';
            return \Redirect::back()->withInput($msg);
        } else {
            $msg['error'] = 'Fee Head Amount Already Exists';
            return \Redirect::back()->withInput($msg);
        }
    }

    public function feeHeadAmountDelete($id) {
        \DB::table('fee_head_amount')->where('id', $id)->delete();
        $msg['success'] = 'Success to Delete Fee Head Amount';
        return \Redirect::back()->withInput($msg);
    }

    public function registration() {
        $get = \DB::table('registration_fee')
            ->where('registration_fee.school_id', $this->user->school_id)
            ->leftjoin('class', 'registration_fee.class_id', '=', 'class.id')
            ->select
            (
                'registration_fee.id', 'class.class', 'registration_fee.amount'
            )
            ->paginate(10);
        $selectClass = \Session::get('selectedClass');
        return view('users.fee_structure.registration', compact('get', 'selectedClass'));
    }

    public function feeregistrationPost() {
        $input = \Request::all();
        $post = \DB::table('registration_fee')
            ->insert
            ([
                'school_id' => $this->user->school_id,
                'class_id' => $input['class'],
                'amount' => $input['amount']
            ]);

        \Session::put('selectedClass', $input['class']);
        $msg['success'] = 'Success to Submit Fee';
        return \Redirect::back()->withInput($msg);
    }

    public function feeregistrationDelete($id) {
        \DB::table('registration_fee')
            ->where('id', $id)
            ->delete();

        $msg['success'] = 'Success to Delete Fee Registration Amount';
        return \Redirect::back()->withInput($msg);
    }

    public function security() {
        $get = \DB::table('security_fee')
            ->where('security_fee.school_id', $this->user->school_id)
            ->leftjoin('class', 'security_fee.class_id', '=', 'class.id')
            ->select
            (
                'security_fee.id', 'class.class', 'security_fee.amount'
            )
            ->paginate(10);
        $selectClass = \Session::get('selectedClass');
        return view('users.fee_structure.security', compact('get', 'selectedClass'));
    }

    public function feeSecurityPost() {
        $input = \Request::all();
        $post = \DB::table('security_fee')
            ->insert
            ([
                'school_id' => $this->user->school_id,
                'class_id' => $input['class'],
                'amount' => $input['amount']
            ]);

        \Session::put('selectedClass', $input['class']);
        $msg['success'] = 'Success to Submit Fee';
        return \Redirect::back()->withInput($msg);
    }

    public function feeSecurityDelete($id) {
        \DB::table('security_fee')
            ->where('id', $id)
            ->delete();

        $msg['success'] = 'Success to Delete Fee Security Amount';
        return \Redirect::back()->withInput($msg);
    }

    public function feeCollection() {
        /*return view('users.fee_collection.index');*/
        return view('users.fee_collection.index_v2');
    }

    public function single_stu_payment($reg, Students $student, Session $session, Payment $payment)
     {
        /******* updated 7-3-2018 by priya  *******/

        $register_no = str_replace(".", "/", $reg);

        /******* end *******/


        $session_id = $session->get_active_session_id(Auth::user()->school_id);
        $single_student = $student->get_student_by_regNo($register_no, Auth::user()->school_id);
        $payment_details = $payment->get_payment_student_by_id(Auth::user()->school_id, $session_id->id, $single_student->id);
        $classes = addClass::select('class')->where('id', $single_student->class_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->first();
       
       /* $fees = feestructuree::where('school_id', Auth::user()->school_id)
        ->where('class_id', $single_student->class_id)->orderBy('payment_type', 'DESC')->get();*/
        /** @ Updated 17-3-2018 @ **/

        $checkfeeExist = FeeStructure::where('school_id', \Auth::user()->school_id)
            ->where('session','=',$this->active_session->session)//updated 20-4-2018
            ->where('class_id',$single_student->class_id)
            ->where('student_id',$single_student->id)->first();
        if($checkfeeExist)
        {
            $fees = FeeStructure::where('school_id', \Auth::user()->school_id)
                ->where('session','=',$this->active_session->session)//updated 20-4-2018
                ->where('class_id',$single_student->class_id)
                ->whereIn('student_id',array('0',$single_student->id))
                ->orderBy('payment_type', 'DESC')->get();
        }
        else
        {
            $fees = FeeStructure::where('school_id', \Auth::user()->school_id)
                ->where('session','=',$this->active_session->session)//updated 20-4-2018
                ->where('class_id',$single_student->class_id)
                ->where('student_id','=','0')
                ->orderBy('payment_type', 'DESC')->get();
        }

        /**********   End   *********/

        $installment = array();
        $total_amount = 0;
        $fee_balance_amount = array();
        foreach ($fees as $key => $value) {
            if ($value->payment_type == 'ANNUAL')
            {
                $installment_ids = json_decode(json_decode($value->installment_id));
                foreach ($installment_ids as $in_key => $id)
                {
                    $installment[$value->id][] = installment::where('school_id', Auth::user()->school_id)->where('id', $id)->first();
                }
            }
            $total_amount += $value->amount;
            $payied_balance =$payment->get_balance_amountby_stu($value->id,$single_student->id);
            $fee_balance_amount[$value->id] = empty($payied_balance)?array('balance_amount'=>$value->amount):$payied_balance;

        }
        if(count($classes)>0)
        {
            $single_student->class_id = $classes->class;
        }
        $school = school::where('id', Auth::user()->school_id)->get();
        return view('users.fee_collection.single_student_payment', compact('single_student', 'school', 'fees', 'installment', 'payment_details', 'fee_balance_amount', 'total_amount'));
    }

    // public function payfee($register_no, $fee_id, Students $student, FeeStructuree $fee_structure, Payment $payment) {
    //     $installments = array();
    //     $single_student = $student->get_student_by_regNo($register_no, Auth::user()->school_id);
    //     $fee = $fee_structure->get_by_id(Auth::user()->school_id, $fee_id);
    //     if ($fee->payment_type == "ANNUAL") {
    //         $installment_ids = json_decode(json_decode($fee->installment_id));
    //         foreach ($installment_ids as $key => $value) {
    //             $paid = payment::where('fee_id', $fee_id)->where('installment_id', $value)->first();
    //             if (empty($paid)) {
    //                 $installments [] = installment::where('school_id', Auth::user()->school_id)->where('id', $value)->first();
    //             }
    //         }
    //     }
    //     $payment_id = 0;
    //     $paid_month = array();
    //     $payment_details = $payment->get_balance_amountby_stu($fee->id,$single_student->id);
    //     if (!empty($payment_details)) {
    //         $balance_amount = $payment_details->balance_amount;
    //         $payment_id = $payment_details->id;
    //         $get_payment = $payment->get_payment_by_id($payment_id);
    //         $paid_month = json_decode($get_payment->fee_detail);
    //     } else {
    //         $balance_amount = 0;
    //     }
    //     $amount = ($balance_amount > 0 ? $balance_amount : $fee->amount);
    //     if ($fee->payment_type) {
    //         $amount = $fee->amount;
    //         $amount = ($balance_amount > 0 ? $balance_amount : $fee->amount);
    //     }
    //     return view('users.fee_collection.payFee', compact('single_student', 'fee', 'amount', 'installments', 'payment_id', 'paid_month'));
    // }

    // public function pay(Request $request, Payment $payment, Session $session, FeeStructuree $fee_structure) {

    //     $pay_month[date("Y-m-d")] = array();
    //     $fee_detail = array();
    //     $fee_id = $request->input('feeId');
    //     $fee = $fee_structure->get_by_id(Auth::user()->school_id, $fee_id);
    //     $payment_details = $payment->get_balance_amountby_stu($fee_id,$request->input('studentId'));
    //     $balance_amount = $request->input('feeAmount') - $request->input('recivedAmount');
    //     $paid = ($balance_amount == 0 ? true : false );
    //     /*$amount_lateFee = $request->input('recivedAmount') + $request->input('lateFee');
    //     $amount = $amount_lateFee - $request->input('concession');*/
    //      $amount = $request->input('recivedAmount');
    //     $paied_now = array();
    //     if ($fee->payment_type == 'MONTHLY') {
    //         foreach ($request->input() as $key => $value) {
    //             if ('paymonth_' == substr($key, 0, 9)) {
    //                 $pay_month[date("Y-m-d")][] = $request->input($key);

    //             }
    //         }
    //         $require_amnt = ($request->input('feeAmount') * count($pay_month[date("Y-m-d")]))+ $request->input('lateFee');
    //         $balance_amount = ($require_amnt - $request->input('concession') )- $request->input('recivedAmount');
    //         $paid = ($balance_amount == 0 ? true : false );
    //         $amount = $request->input('feeAmount') * count($pay_month[date("Y-m-d")]);
    //     }
    //     if ($fee->payment_type == 'ANNUAL') {
    //         foreach ($request->input() as $key => $value) {
    //             if ('installment_type_' == substr($key, 0, 17)) {
    //                 $id = explode('_', $key);
    //                 $fee_detail[] = array(
    //                     'installment_amount' => $request->input('installment_amount_' . $id[2].''),
    //                     'installment_type' => $request->input($key),
    //                     'installment_id' => $id[2]
    //                 );
    //             }
    //         }
    //         $balance_amount = ($balance_amount+$request->input('lateFee'))-$request->input('concession');
    //     }
    //     if($fee->payment_type == 'ONE TIME'){
    //         $balance_amount = ($balance_amount+$request->input('lateFee'))-$request->input('concession');
    //     }
    //     $cheque_detail[date("Y-m-d")] = array(
    //         'cheque_no' => $request->input('chequeNo'),
    //         'cheque_bank_name' => $request->input('chequeBankName'),
    //         'cheque_date' => $request->input('chequeDate')
    //     );
    //     $transaction_detail[date("Y-m-d")] = array(
    //         'transaction_no' => $request->input('transactionNo'),
    //         'transaction_bank_name' => $request->input('transactionBankName')
    //     );
    //     $fee_type = $request->input('feeType');
    //     $session_id = $session->get_active_session_id(Auth::user()->school_id);
    //     if (empty($payment_details)) {
    //         /* insert new payment */
    //         $payment_data = array(
    //             'student_id' => $request->input('studentId'),
    //             'school_id' => Auth::user()->school_id,
    //             'session_id' => $session_id->id,
    //             'fee_id' => $fee_id,
    //             'paid' => $paid,
    //             'amount' => $amount,
    //             'balance_amount' => $balance_amount,
    //             'fee_name' => $request->input('feesName'),
    //             'fee_detail' => json_encode($fee_detail),
    //             'date' => $request->input('paymentDate'),
    //             'recived_by' => Auth::user()->school_id,
    //             'payment_type' => $request->input('paymentMode'),
    //             'late_fee' => $request->input('lateFee'),
    //             'concession' => $request->input('concession'),
    //             'created_at' => date('Y-m-d H:i:s'),
    //             'updated_at' => date('Y-m-d H:i:s')
    //         );
    //         $recipt = $payment->insert_payment($payment_data, $fee_type, $pay_month, $cheque_detail, $transaction_detail);
    //         $recipt['currentPaiedMonth'] = $pay_month[date("Y-m-d")];
    //         $recipt['recivedAmount']=$request->input('recivedAmount');
    //         $recipt['feeAmount']=$request->input('feeAmount');
    //         return view('users.fee_collection.payment_recipt', compact('recipt'));
    //     } else {

    //         /* update a payment */
    //         $where_data = array(
    //             'payment_id' => $payment_details->id,
    //             'student_id' => $request->input('studentId')
    //         );
    //         $paid_details = "";
    //         if ($balance_amount == 0) {
    //             $amount = (($fee->amount + $payment_details->late_fee) + $request->input('lateFee')) - $request->input('concession');
    //         }
    //         if ($fee->payment_type == 'MONTHLY') {
    //             $paid_details = json_decode($payment_details->fee_detail, true);
    //             foreach ($paid_details as $keys => $values) {
    //                 if ($keys == date("Y-m-d")) {
    //                     return redirect()->back()->with('error', "Please pay tomorrow");
    //                 } else {
    //                     $date = new DateTime('tomorrow');
    //                     $paid_details[$date->format('Y-m-d')] = array_diff($pay_month[date("Y-m-d")], $values);
    //                     $paied_now[]= array_diff($pay_month[date("Y-m-d")], $values);
    //                 }
    //             }
    //             $extra_fee = $request->input('lateFee') - $request->input('concession');
    //             $temp_amount = $fee->amount * count($paied_now[0]) +$extra_fee;
    //             $balance_amount = $temp_amount  - $request->input('recivedAmount');
    //             $amount =  $fee->amount * (count($paid_details[$date->format('Y-m-d')]) + count($values));
    //         }
    //         $update_late_fee = $request->input('lateFee')+$payment_details->late_fee;
    //         if($fee->payment_type == 'ONE TIME'){
    //             if($balance_amount == 0){
    //                 $paid = true;
    //             }else{
    //                 $amount = $amount + ( $fee->amount - $request->input('feeAmount'));
    //             }
    //         }

    //         if ($fee->payment_type == 'ANNUAL') {

    //             $installment_amount = $request->input('installment_amount_' . $id[2].'');
    //             $amount = 0 ;
    //             $paid_details = array();
    //             foreach ($request->input() as $key => $value) {
    //                 if ('installment_type_' == substr($key, 0, 17)) {
    //                     $id = explode('_', $key);
    //                     $amount += $installment_amount;
    //                     $paid_details[]= array(
    //                         'installment_amount' => $installment_amount,
    //                         'installment_type' => $request->input($key),
    //                         'installment_id' => $id[2]
    //                     );
    //                 }
    //             }
    //         }
    //         $payment_data = array(
    //             'paid' => $paid,
    //             'amount' => $amount,
    //             'balance_amount' => $balance_amount,
    //             'date' => $request->input('paymentDate'),
    //             'fee_detail' => json_encode($paid_details),
    //             'recived_by' => Auth::user()->school_id,
    //             'payment_type' => $request->input('paymentMode'),
    //             'late_fee' => $update_late_fee,
    //             'concession' => $request->input('concession'),
    //         );
    //         $recipt = $payment->update_payment($where_data, $payment_data, $fee_type, $pay_month, $cheque_detail, $transaction_detail);
    //         $recipt['currentPaiedMonth'] = $paied_now[0];
    //         $recipt['recivedAmount']=$request->input('recivedAmount');
    //         $recipt['feeAmount']=$request->input('feeAmount');
    //         return view('users.fee_collection.payment_recipt', compact('recipt'));
    //     }
    // }

    // changes done by parthiban 28-09-2017
    public function payfee($reg, $fee_id, Students $student, FeeStructuree $fee_structure, Payment $payment)
    {
        /******* updated 7-3-2018 by priya  *******/

        $register_no = str_replace(".", "/", $reg);

        /******* end *******/
        $installments = array();
        $single_student = $student->get_student_by_regNo($register_no, Auth::user()->school_id);
        $fee = $fee_structure->get_by_id(Auth::user()->school_id, $fee_id);
        if ($fee->payment_type == "ANNUAL")
        {
            $installment_ids = json_decode(json_decode($fee->installment_id));
            foreach ($installment_ids as $key => $value)
            {
                $paid = payment::where('fee_id', $fee_id)
                    ->where('session_id','=',$this->active_session->id)//updated 20-4-2018
                    ->where('installment_id', $value)->first();
                if (empty($paid))
                {
                    $installments [] = installment::where('school_id', Auth::user()->school_id)->where('id', $value)->first();
                }
            }
        }
        $payment_id = 0;
        $paid_month = array();
        $payment_details = $payment->get_balance_amountby_stu($fee->id,$single_student->id);
        if (!empty($payment_details))
        {
            $balance_amount = $payment_details->balance_amount;
            $last_paid_date = $payment_details->last_paid_date;
            $payment_id = $payment_details->id;
            // $get_payment = $payment->get_payment_by_id($payment_id);
            $get_payments = $payment->get_payment_by_id_parthiban($fee->id,$single_student->id);
            foreach ($get_payments as $key => $get_payment)
            {
                $paid_month[] = json_decode($get_payment->fee_detail);
            }
        }
        else
        {
            $balance_amount = 0;
            $last_paid_date = 0;
        }

        $amount = ($balance_amount > 0 ? $balance_amount : $fee->amount);
        if ($fee->payment_type)
        {
            $amount = $fee->amount;
            $amount = ($balance_amount > 0 ? $balance_amount : $fee->amount);
        }

        return view('users.fee_collection.payFee', compact('single_student', 'fee', 'amount', 'installments', 'payment_id', 'paid_month','last_paid_date'));
    }

    // changes done by parthiban 29-09-2017
    public function pay(Request $request, Payment $payment, Session $session, FeeStructuree $fee_structure) {
        $pay_month[date("Y-m-d")] = array();
        $fee_detail = array();
        $fee_id = $request->input('feeId');
        $fee = $fee_structure->get_by_id(Auth::user()->school_id, $fee_id);
        $payment_details = $payment->get_balance_amountby_stu($fee_id,$request->input('studentId'));
        $balance_amount = $request->input('feeAmount') - $request->input('recivedAmount');
        $paid = ($balance_amount == 0 ? true : false );
        $amount = $request->input('recivedAmount');
        $paied_now = array();
        if ($fee->payment_type == 'MONTHLY') {
            foreach ($request->input() as $key => $value) {
                if ('paymonth_' == substr($key, 0, 9)) {
                    $pay_month[date("Y-m-d")][] = $request->input($key);

                }
            }
            // $require_amnt = ($request->input('feeAmount') * count($pay_month[date("Y-m-d")]))+ $request->input('lateFee');
            $require_amnt = ($request->input('feeAmount') + $request->input('lateFee'));
            $balance_amount = ($require_amnt - $request->input('concession') )- $request->input('recivedAmount');
            $paid = ($balance_amount == 0 ? true : false );
            // $amount = $request->input('feeAmount') * count($pay_month[date("Y-m-d")]);
            //$amount = $request->input('feeAmount');
        }
        if ($fee->payment_type == 'ANNUAL') {
            foreach ($request->input() as $key => $value) {
                if ('installment_type_' == substr($key, 0, 17)) {
                    $id = explode('_', $key);
                    $fee_detail[] = array(
                        'installment_amount' => $request->input('installment_amount_' . $id[2].''),
                        'installment_type' => $request->input($key),
                        'installment_id' => $id[2]
                    );
                }
            }
            $balance_amount = ($balance_amount+$request->input('lateFee'))-$request->input('concession');
        }
        if($fee->payment_type == 'ONE TIME'){
            $balance_amount = ($balance_amount+$request->input('lateFee'))-$request->input('concession');
        }

        /** @ Updated 17-3-2018 @ **/
        if($fee->payment_type == 'PREVIOUS YEAR PAYMENT')
        {
            $balance_amount = ($balance_amount+$request->input('lateFee'))-$request->input('concession');
        }
        $cheque_detail[date("Y-m-d")] = array(
            'cheque_no' => $request->input('chequeNo'),
            'cheque_bank_name' => $request->input('chequeBankName'),
            'cheque_date' => $request->input('chequeDate')
        );
        $transaction_detail[date("Y-m-d")] = array(
            'transaction_no' => $request->input('transactionNo'),
            'transaction_bank_name' => $request->input('transactionBankName')
        );
        $fee_type = $request->input('feeType');
        $session_id = $session->get_active_session_id(Auth::user()->school_id);

        /* insert new payment */
        $payment_data = array(
            'student_id' => $request->input('studentId'),
            'school_id' => Auth::user()->school_id,
            'session_id' => $session_id->id,
            'fee_id' => $fee_id,
            'paid' => $paid,
            'amount' => $amount,
            'balance_amount' => $balance_amount,
            'fee_name' => $request->input('feesName'),
            'fee_detail' => json_encode($fee_detail),
            'date' => $request->input('paymentDate'),
            'recived_by' => Auth::user()->school_id,
            'payment_type' => $request->input('paymentMode'),
            'late_fee' => $request->input('lateFee'),
            'concession' => $request->input('concession'),
            'last_paid_date' => date('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $recipt = $payment->insert_payment($payment_data, $fee_type, $pay_month, $cheque_detail, $transaction_detail);
        $recipt['currentPaiedMonth'] = $pay_month[date("Y-m-d")];
        $recipt['recivedAmount']=$request->input('recivedAmount');
        $recipt['feeAmount']=$request->input('feeAmount');
        return view('users.fee_collection.payment_recipt', compact('recipt'));
    }

    public function postFee(Fee $fee) {
        $input = \Request::all();
        $userError = ['frequn' => 'Frequency', 'registration' => 'Registration'];
        $validator = \Validator::make($input, ['frequn' => 'required', 'registration' => 'required']
            , $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator)->withInput($input);
        return $fee->doPostFee($input, $this->user);
    }

    public function viewFee() {
        $reg_no = \Request::get('reg_no');
        if ($reg_no) {
            $student = \DB::table('student')
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->where('student.registration_no', $reg_no)
                ->where('student.school_id', \Auth::user()->school_id)
                ->join('class', 'student.class_id', '=', 'class.id')
                ->select(
                    'class.class', 'student.name', 'student.id', 'student.class_id', 'student.roll_no', 'student.registration_no'
                )
                ->first();

            $fees = \DB::table('fee_collection')
                ->where('student_id', $student->id)
                ->get();
            $total_discount = 0;
            $total_pay = 0;
            foreach ($fees as $fee) {
                $total_discount = $total_discount + (int) $fee->discount;
                $total_pay = $total_pay + (int) $fee->pay_amount;
            }

            $fee_head = \DB::table('fee_head')
                ->where('fee_head.school_id', \Auth::user()->school_id)
                ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')
                ->where('fee_head_amount.class_id', $student->class_id)
                ->get();
            $total_amount = 0;
            foreach ($fee_head as $key => $value) {

                if ($value->fee_head_type == 'annual') {
                    $total_amount = $total_amount + $value->amount;
                } else if ($value->fee_head_type == 'month') {
                    $total_amount = $total_amount + ($value->amount * 12);
                }
            }
            $balance = $total_amount - ($total_discount + $total_pay);
        } else {
            $fees = '';
            $total_discount = '';
            $total_pay = '';
            $balance = '';
            $total_amount = '';
        }
        return view('users.fee_collection.view', compact('fees', 'total_pay', 'total_discount', 'balance', 'total_amount', 'student'));
    }

    public function feeStatement($reg) {
        $student = \DB::table('student')
            ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
            ->where('student.registration_no', $reg)
            ->where('student.school_id', \Auth::user()->school_id)
            ->join('class', 'student.class_id', '=', 'class.id')
            ->select(
                'class.class', 'student.name', 'student.id', 'student.class_id', 'student.roll_no', 'student.registration_no'
            )
            ->first();

        $fees = \DB::table('fee_collection')
            ->where('student_id', $student->id)
            ->get();
        $total_discount = 0;
        $total_pay = 0;
        foreach ($fees as $fee) {
            $total_discount = $total_discount + (int) $fee->discount;
            $total_pay = $total_pay + (int) $fee->pay_amount;
        }

        $fee_head = \DB::table('fee_head')
            ->where('fee_head.school_id', \Auth::user()->school_id)
            ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')
            ->where('fee_head_amount.class_id', $student->class_id)
            ->get();
        $total_amount = 0;
        foreach ($fee_head as $key => $value) {

            if ($value->fee_head_type == 'annual') {
                $total_amount = $total_amount + $value->amount;
            } else if ($value->fee_head_type == 'month') {
                $total_amount = $total_amount + ($value->amount * 12);
            }
        }
        $balance = $total_amount - ($total_discount + $total_pay);

        \Excel::create("statement-" . $student->registration_no, function($excel) use ($fees, $total_pay, $total_discount, $balance, $total_amount, $student) {

            $excel->sheet('Excel sheet', function($sheet) use ($fees, $total_pay, $total_discount, $balance, $total_amount, $student) {
                $sheet->loadView('users.fee_collection.statement')->with('fees', $fees)->with('total_pay', $total_pay)->with('total_discount', $total_discount)->with('balance', $balance)->with('total_amount', $total_amount)->with('student', $student);
                $sheet->setOrientation('portrait');
            });
        })->download('pdf');
    }

    public function resultDownload($class, $section, $exam, $id) {
        $students = \DB::table('student')->where('student.class_id', $class)
            ->where('student.school_id', \Auth::user()->school_id)
            ->where('student.section_id', $section)
            ->where('student.id', $id)
            ->first();
        $result = \DB::table('subject')
            ->where('subject.school_id', \Auth::user()->school_id)
            ->join('result', 'subject.id', '=', 'result.subject_id')
            ->where('result.exam_type_id', $exam)
            ->where('result.student_id', $id)
            ->get();

        $totalMarks = 0;
        $totalPassMarks = 0;
        $totalObtain = 0;
        foreach ($result as $res) {
            $totalMarks = $totalMarks + $res->max_marks;
            $totalPassMarks = $totalPassMarks + $res->pass_marks;
            if(is_numeric($res->obtained_marks))
            {
                $totalObtain = $totalObtain + $res->obtained_marks;
            }
           else
            {
                $totalObtain = $totalObtain + 0;
            }
            //$totalObtain = $totalObtain + $res->obtained_marks;
            $resultof=$res->result;
            $date=$res->date;
        }
        $students->totalMarks = $totalMarks;
        $students->totalPassMarks = $totalPassMarks;
        $students->totalObtain = $totalObtain;
        $students->resultof=$resultof;
        $students->date=$date;
        $students->result = $result;
       // dd($students);
        $pdf = \PDF::loadView('users.result.invoice_result',compact('students'));
        return $pdf->download('MarkReports.pdf');
        // $view = \View::make('users.result.invoice_result', compact('students'));
        // $html = $view->render();
        // return \PDF::loadHTML($html, 'A4', 'portrait')->stream();
        //  //$pdf = App::make('dompdf.wrapper');
        // $pdf = PDF::loadHTML('<h1>Test</h1>');
        //  return $pdf->download('invoice.pdf');
        //return view('users.result.invoice_result',compact(students));
//        \Excel::create("invoice_result-" . $student->registration_no, function($excel) use ($students) {
//
//            $excel->sheet('Excel sheet', function($sheet) use ($students) {
//                $sheet->loadView('users.result.invoice_result')->with('students', $students);
//                $sheet->setOrientation('portrait');
//            });
//        })->download('pdf');
        //$html="<h1>vasu</h1>";
        //$data=array("vasu"=>"devan");
        //print_r($students);
        //$data=array

        // // $view = \View::make('result_report', compact('students', 'result'));
        //     $html = $view->render();
        //     \PDF::loadHtml($html, 'A4', 'portrait')->save('report/report'.$this->student->id.'.pdf');
        //     $pdfReport = config('constants.share_link').$this->student->id.'.pdf';
    }

    public function timeTable()
    {
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        $teaching = \DB::table('staff')->where('school_id', $this->user->school_id)
            ->where('staff_type', 'Teaching Staff')->first();
        $teachers = \DB::table('teacher')->where('school_id', $this->user->school_id)
            ->where('session_id',$this->active_session->id)//updated 10-5-2018
            ->where('type', $teaching->id)->get();
        return view('users.time_table.add_time_table', compact('classes', 'teachers'));
    }

    public function getTimeTable(TimeTable $time) {
        return $time->doGetTimeTable($this->user);
    }

    public function postTimeTable(TimeTable $time) {
        $input = \Request::all();
        $userError = ['class' => 'Class', 'section' => 'Section', 'subject' => 'Subject', 'period' => 'Period', 'start_time' => 'Start Time',
            'end_time' => 'End Time', 'day' => 'Day', 'teacher' => 'Teacher'];
        $validator = \Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'subject' => 'required',
            // 'period' => 'required',
            'period' => 'required|integer|between:1,10',//updated 20-11-2017 by priya
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
           // 'start_time' => 'required|date_format:H:i',//updated 20-11-2017
           // 'end_time' => 'required|date_format:H:i|after:start_time',//updated 20-11-2017
            'teacher' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $time->doPostTimeTable($this->user, $input);
    }

    public function deleteTimetable($id) {
        \DB::table('time-table')->where('id', $id)->delete();
        $msg['success'] = 'Success to delete TimeTable';
        return \Redirect::back()->withInput($msg);
    }
//    public function report() {
//        $input = \Request::all();
//        if (!$input) {
//            return view('users.report.index');
//        } else {
//            $input['from'] = date('Y-m-d', strtotime($input['from']));
//            $input['to'] = date('Y-m-d', strtotime($input['to']));
//            // dd($input);
//            if ($input['type'] == 'classAccording') {
//                $students = \DB::table('student')->where('school_id', \Auth::user()->school_id)
//                        ->where('class_id', $input['class'])->where('section_id', $input['section'])
//                        ->get();//mari for v3
//				$get_cls=\DB::table('class')
//					->where('class.school_id','=',\Auth::user()->school_id)
//					->where('class.id','=',$input['class'])
//					->join('section','class.school_id','=','section.school_id')
//					->where('section.id','=',$input['section'])
//					->where('section.class_id','=',$input['class'])
//					->select('section.section','class.class')->first();
//					$input['class_name']=$get_cls->class;
//					$input['section_name']=$get_cls->section;
//                $attendances = \DB::table('attendance')
//                        ->where('attendance.school_id', \Auth::user()->school_id)
//                        ->where('attendance.class_id', $input['class'])
//                        ->where('attendance.section_id', $input['section'])
//                        ->whereBetween('attendance.date', array($input['from'], $input['to']))
//                        ->join('student', 'attendance.student_id', '=', 'student.id')
//                        ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
//                        ->get();
//
//                $filename = 'class attendance';
//                $type = 'classAccording';
//            } else {
//                $students = \DB::table('student')->where('registration_no', $input['regno'])->where('school_id', \Auth::user()->school_id)->get();
//
//				$get_cls=\DB::table('class')
//				->where('class.school_id','=',\Auth::user()->school_id)
//				->where('class.id','=',$students[0]->class_id)
//				->join('section','class.school_id','=','section.school_id')
//				->where('section.id','=',$students[0]->section_id)
//				->where('section.class_id','=',$students[0]->class_id)
//				->select('section.section','class.class')->first();
//				$input['class_name']=$get_cls->class;
//				$input['section_name']=$get_cls->section;
//
//                $attendances = \DB::table('attendance')
//                        ->where('attendance.student_id', $students[0]->id)
//                        ->where('attendance.school_id', \Auth::user()->school_id)
//                        ->whereBetween('attendance.date', array($input['from'], $input['to']))
//                        ->join('student', 'attendance.student_id', '=', 'student.id')
//                        ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
//                        ->get();
//                $filename = 'registration' . $input['regno'];
//
//                $type = 'singleStudent';
//            }
//			//dd($attendances);
//            return $this->process_attendance_report($attendances, $students, $input, $filename);
//        }
//    }

//    public function report() {
//        $input = \Request::all();
//        if (!$input) {
//            return view('users.report.index');
//        } else {
//            $input['from'] = date('Y-m-d', strtotime($input['from']));
//            $input['to'] = date('Y-m-d', strtotime($input['to']));
//            // dd($input);
//            if ($input['type'] == 'classAccording') {
//                $students = \DB::table('student')->where('school_id', \Auth::user()->school_id)
//                        ->where('class_id', $input['class'])->where('section_id', $input['section'])
//                        ->get();
//                $attendances = \DB::table('attendance')
//                        ->where('attendance.school_id', \Auth::user()->school_id)
//                        ->where('attendance.class_id', $input['class'])
//                        ->where('attendance.section_id', $input['section'])
//                        ->whereBetween('attendance.date', array($input['from'], $input['to']))
//                        ->join('student', 'attendance.student_id', '=', 'student.id')
//                        ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
//                        ->get();
//
//                $filename = 'class attendance';
//                $type = 'classAccording';
//            } else {
//                $students = \DB::table('student')->where('registration_no', $input['regno'])->where('school_id', \Auth::user()->school_id)->get();
//
//                $attendances = \DB::table('attendance')
//                        ->where('attendance.student_id', $students[0]->id)
//                        ->where('attendance.school_id', \Auth::user()->school_id)
//                        ->whereBetween('attendance.date', array($input['from'], $input['to']))
//                        ->join('student', 'attendance.student_id', '=', 'student.id')
//                        ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
//                        ->get();
//                $filename = 'registration' . $input['regno'];
//
//                $type = 'singleStudent';
//            }
//            return $this->process_attendance_report($attendances, $students, $input, $filename);
//        }
//    }
//        public function report() {//mari for v3
//        $input = \Request::all();
//        if (!$input) {
//            return view('users.report.index');
//        } else {
//            $input['from'] = date('Y-m-d', strtotime($input['from']));
//            $input['to'] = date('Y-m-d', strtotime($input['to']));
//            // dd($input);
//            if ($input['type'] == 'classAccording') {
//                $students = \DB::table('student')->where('school_id', \Auth::user()->school_id)
//                        ->where('class_id', $input['class'])->where('section_id', $input['section'])
//                        ->get();//mari for v3
//				$get_cls=\DB::table('class')
//					->where('class.school_id','=',\Auth::user()->school_id)
//					->where('class.id','=',$input['class'])
//					->join('section','class.school_id','=','section.school_id')
//					->where('section.id','=',$input['section'])
//					->where('section.class_id','=',$input['class'])
//					->select('section.section','class.class')->first();
//					$input['class_name']=$get_cls->class;
//					$input['section_name']=$get_cls->section;
//                $attendances = \DB::table('attendance')
//                        ->where('attendance.school_id', \Auth::user()->school_id)
//                        ->where('attendance.class_id', $input['class'])
//                        ->where('attendance.section_id', $input['section'])
//                        ->whereBetween('attendance.date', array($input['from'], $input['to']))
//                        ->join('student', 'attendance.student_id', '=', 'student.id')
//                        ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
//                        ->get();
//
//                $filename = 'class attendance';
//                $type = 'classAccording';
//            } else {
//                $students = \DB::table('student')->where('registration_no', $input['regno'])->where('school_id', \Auth::user()->school_id)->get();
//
//				$get_cls=\DB::table('class')
//				->where('class.school_id','=',\Auth::user()->school_id)
//				->where('class.id','=',$students[0]->class_id)
//				->join('section','class.school_id','=','section.school_id')
//				->where('section.id','=',$students[0]->section_id)
//				->where('section.class_id','=',$students[0]->class_id)
//				->select('section.section','class.class')->first();
//				$input['class_name']=$get_cls->class;
//				$input['section_name']=$get_cls->section;
//
//                $attendances = \DB::table('attendance')
//                        ->where('attendance.student_id', $students[0]->id)
//                        ->where('attendance.school_id', \Auth::user()->school_id)
//                        ->whereBetween('attendance.date', array($input['from'], $input['to']))
//                        ->join('student', 'attendance.student_id', '=', 'student.id')
//                        ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
//                        ->get();
//                $filename = 'registration' . $input['regno'];
//
//                $type = 'singleStudent';
//            }
//			//dd($attendances);
//            return $this->process_attendance_report($attendances, $students, $input, $filename);
//        }
//    }
//
//
//  public function process_attendance_report($attendances, $students, $input, $filename) {
//		//dd($input);
//        $am_totalPresent = 0;
//        $am_totalLeave = 0;
//        $am_totalAbsent = 0;
//        $pm_totalPresent = 0;
//        $pm_totalLeave = 0;
//        $pm_totalAbsent = 0;
//        $att_date = array();
//        $am_attendance['init'] = 'p';
//        $pm_attendance['init'] = 'p';
//        $am = array();
//        $pm = array();
//        foreach ($attendances as $key => $value) {
//            if ($value->attendance_session == 'am') {
//                $am_attendance[] = $value;
//            } elseif ($value->attendance_session == 'pm') {
//                $pm_attendance[] = $value;
//            }
//        }
//        $attendance_date = $this->get_inbetween_date($input['from'], $input['to']);
//        if($attendance_date == NULL){
//            $input['error'] = 'no attendance at that date';
//            return \Redirect::back()->withInput($input);
//        }
//        foreach ($attendance_date as $date) {
//            foreach ($students as $key => $value) {
//                foreach ($am_attendance as $att_key => $att_value) {//mari for v3
//					$exist = \DB::table('attendance_status')->
//                            where('school_id',\Auth::user()->school_id)
//                            ->where('date',$date)->where('attendance_session','am')
//                            ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
//                            ->first();
//                    if(count($exist)>0){
//                        if ($att_value->student_id == $value->id && $att_value->date == $date) {
//                            $am[$date][$value->id] = $att_value->attendance;
//                            if ($am[$date][$value->id] == 'L') {
//                                $am_totalLeave++;
//                            } elseif($am[$date][$value->id] == 'A') {
//                                $am_totalAbsent++;
//                            }
//                            break;
//                        } else {
//                            $am[$date][$value->id] = 'P';
//                        }
//                    }else{
//                        $am[$date][$value->id] = '-';
//                    }
//
//                }
//                foreach ($pm_attendance as $att_key => $att_value) {//mari for v3
//					$exist_pm = \DB::table('attendance_status')->
//                            where('school_id',\Auth::user()->school_id)
//                            ->where('date',$date)->where('attendance_session','pm')
//                            ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
//                            ->first();
//                    if(count($exist_pm)>0){
//
//                        $server_date = new DateTime();
//                        $record_date = new DateTime($exist_pm->date);
//                        if ($att_value->student_id == $value->id && $att_value->date == $date) {
//
//                                $pm[$date][$value->id] = $att_value->attendance;
//                            if ($pm[$date][$value->id] == 'L') {
//                                $pm_totalLeave++;
//                            } elseif($pm[$date][$value->id] == 'A') {
//                                $pm_totalAbsent++;
//                            }
//                            break;
//                        } else {
//                            if($server_date->format('d-m-Y') == $record_date->format('d-m-Y') && date('H') < 13){
//                                $pm[$date][$value->id] = '-';
//								//$pm[$date][$value->id] = 'P';
//                            }else{
//                               $pm[$date][$value->id] = 'P';
//                            }
//                        }
//                    }else{
//                        $pm[$date][$value->id] = '-';
//                    }
//                }
//
//                if ($am[$date][$value->id] == 'P') {
//                    $am_totalPresent++;
//                }
//                if ($pm[$date][$value->id] == 'P') {
//                    $pm_totalPresent++;
//                }
//            }
//        }
//		$fromdate=$input['from'];
//		$todate=$input['to'];
//		$class=$input['class_name'];
//		$section=$input['section_name'];
//        \Excel::create($filename, function($excel) use ($attendance_date,$fromdate,$todate,$class,$section,
//                $students, $am, $pm, $am_totalPresent, $am_totalLeave, $am_totalAbsent, $pm_totalPresent, $pm_totalLeave, $pm_totalAbsent) {
//            $excel->sheet('Excel sheet', function($sheet) use ($attendance_date,$fromdate,$todate,$class,$section,
//                    $students, $am, $pm, $am_totalPresent, $am_totalLeave, $am_totalAbsent, $pm_totalPresent, $pm_totalLeave, $pm_totalAbsent) {
//                $sheet->setFontSize(12);
//                $sheet->setAllBorders('thin');
//
//                // $sheet->setWidth('I', 600);
//                $sheet->loadView('users.report.attendanceExport')->with('attendance_date', $attendance_date)
//                        ->with('am', $am)->with('pm', $pm)->with('students', $students)
//                        ->with('am_totalPresent', $am_totalPresent)->with('am_totalLeave', $am_totalLeave)
//                        ->with('am_totalAbsent', $am_totalAbsent)->with('pm_totalPresent', $pm_totalPresent)
//						->with('fromdate',$fromdate)
//						->with('todate', $todate)
//						->with('class',$class)
//						->with('section',$section)
//                        ->with('pm_totalLeave', $pm_totalLeave)->with('pm_totalAbsent', $pm_totalAbsent);
//						//->with('class',)
//            });
//        })->store('xls', storage_path('/public/excel'));
//        $fileURL = storage_path() . '/public/excel/' . $filename . '.xls';
//        \Session::put('attendanceUrl', $fileURL);
//        $classes = $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)->get();
//        return view('users.report.index', compact('attendances', 'type', 'am_totalLeave', 'am_totalPresent', 'am_totalAbsent', 'pm_totalLeave', 'pm_totalPresent', 'pm_totalAbsent', 'students', 'pm', 'am', 'attendance_date'));
//    }
    public function report() {
        $input = \Request::all();
        if (!$input) {
            return view('users.report.index');
        } else {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));
            if ($input['type'] == 'classAccording') {
                $students = \DB::table('student')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('class_id', $input['class'])->where('section_id', $input['section'])
                    ->get();
                $get_cls=\DB::table('class')
                    ->where('class.school_id','=',\Auth::user()->school_id)
                    ->where('class.session_id',$this->active_session->id)//updated 14-4-2018    
                    ->where('class.id','=',$input['class'])
                    ->join('section','class.school_id','=','section.school_id')
                    ->where('section.id','=',$input['section'])
                    ->where('section.class_id','=',$input['class'])
                    ->select('section.section','class.class')->first();
                $input['class_name']=$get_cls->class;
                $input['section_name']=$get_cls->section;
                $attendances = \DB::table('attendance')
                    ->where('attendance.school_id', \Auth::user()->school_id)
                    ->where('attendance.class_id', $input['class'])
                    ->where('attendance.section_id', $input['section'])
                    ->whereBetween('attendance.date', array($input['from'], $input['to']))
                    ->join('student', 'attendance.student_id', '=', 'student.id')
                    ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
                    ->get();
                $filename = 'class attendance';
                $type = 'classAccording';
            } else {
                $students = \DB::table('student')->where('registration_no', $input['regno'])
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id', \Auth::user()->school_id)->get();
                $get_cls=\DB::table('class')
                    ->where('class.school_id','=',\Auth::user()->school_id)
                    ->where('class.session_id',$this->active_session->id)//updated 14-4-2018    
                    ->where('class.id','=',$students[0]->class_id)
                    ->join('section','class.school_id','=','section.school_id')
                    ->where('section.id','=',$students[0]->section_id)
                    ->where('section.class_id','=',$students[0]->class_id)
                    ->select('section.section','class.class')->first();
                $input['class_name']=$get_cls->class;
                $input['section_name']=$get_cls->section;
                $attendances = \DB::table('attendance')
                    ->where('attendance.student_id', $students[0]->id)
                    ->where('attendance.school_id', \Auth::user()->school_id)
                    ->whereBetween('attendance.date', array($input['from'], $input['to']))
                    ->join('student', 'attendance.student_id', '=', 'student.id')
                    ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
                    ->get();
                //$filename = 'registration' . $input['regno'];
                /******* updated 7-3-2018 by priya  *******/

                    $register_no = str_replace("/", ".", $input['regno']);
                    $filename = 'registration' . $register_no;

                    /******* end *******/

                $type = 'singleStudent';
            }
            return $this->process_attendance_report($attendances, $students, $input, $filename);
        }
    }

    // public function process_attendance_report($attendances, $students, $input, $filename) {
    //     //dd($students);
    //     $am_totalPresent = 0;
    //     $am_totalLeave = 0;
    //     $am_totalAbsent = 0;
    //     $pm_totalPresent = 0;
    //     $pm_totalLeave = 0;
    //     $pm_totalAbsent = 0;
    //     $att_date = array();
    //     $am_attendance['init'] = 'p';
    //     $pm_attendance['init'] = 'p';
    //     $am = array();
    //     $pm = array();
    //     foreach ($attendances as $key => $value) {
    //         if ($value->attendance_session == 'am') {
    //             $am_attendance[] = $value;
    //         } elseif ($value->attendance_session == 'pm') {
    //             $pm_attendance[] = $value;
    //         }
    //     }
    //     $attendance_date = $this->get_inbetween_date($input['from'], $input['to']);
    //     if($attendance_date == NULL){
    //         $input['error'] = 'no attendance at that date';
    //         return \Redirect::back()->withInput($input);
    //     }
    //     foreach ($attendance_date as $date) {
    //         foreach ($students as $key => $value) {
    //             foreach ($am_attendance as $att_key => $att_value) {//mari for v3
    //                 $exist = \DB::table('attendance_status')->
    //                 where('school_id',\Auth::user()->school_id)
    //                     ->where('date',$date)->where('attendance_session','am')
    //                     ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
    //                     ->first();
    //                 if(!empty($exist)){
    //                     if($value->created_at<$exist->created_at){
    //                         if ($att_value->student_id == $value->id && $att_value->date == $date) {
    //                             $am[$date][$value->id] = $att_value->attendance;
    //                             if ($am[$date][$value->id] == 'L') {
    //                                 $am_totalLeave++;
    //                             } elseif($am[$date][$value->id] == 'A') {
    //                                 $am_totalAbsent++;
    //                             }
    //                             break;
    //                         } else {
    //                             $am[$date][$value->id] = 'P';
    //                         }
    //                     }
    //                     else{
    //                         $am[$date][$value->id] = '-';
    //                     }

    //                 }else{
    //                     $am[$date][$value->id] = '-';
    //                 }
    //             }
    //             foreach ($pm_attendance as $att_key => $att_value) {//mari for v3
    //                 $exist_pm = \DB::table('attendance_status')->
    //                 where('school_id',\Auth::user()->school_id)
    //                     ->where('date',$date)->where('attendance_session','pm')
    //                     ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
    //                     ->first();
    //                 if(!empty($exist_pm)){
    //                     $server_date = new DateTime();
    //                     $record_date = new DateTime($exist_pm->date);
    //                     if($value->created_at<$exist_pm->created_at){
    //                         if ($att_value->student_id == $value->id && $att_value->date == $date) {
    //                             $pm[$date][$value->id] = $att_value->attendance;
    //                             if ($pm[$date][$value->id] == 'L') {
    //                                 $pm_totalLeave++;
    //                             } elseif($pm[$date][$value->id] == 'A') {
    //                                 $pm_totalAbsent++;
    //                             }
    //                             break;
    //                         } else {
    //                             if($server_date->format('d-m-Y') == $record_date->format('d-m-Y') && date('H') < 13){
    //                                 $pm[$date][$value->id] = '-';
    //                                 //$pm[$date][$value->id] = 'P';
    //                             }else{
    //                                 $pm[$date][$value->id] = 'P';
    //                             }
    //                         }
    //                     }
    //                     else{
    //                        $pm[$date][$value->id] = '-'; 
    //                     }
    //                 }else{
    //                     $pm[$date][$value->id] = '-';
    //                 }
    //             }
    //             if ($am[$date][$value->id] == 'P') {
    //                 $am_totalPresent++;
    //             }
    //             if ($pm[$date][$value->id] == 'P') {
    //                 $pm_totalPresent++;
    //             }
    //         }
    //     }
    //     $fromdate=$input['from'];
    //     $todate=$input['to'];
    //     $class=$input['class_name'];
    //     $section=$input['section_name'];
    //     \Excel::create($filename, function($excel) use ($attendance_date,$fromdate,$todate,$class,$section,
    //         $students, $am, $pm, $am_totalPresent, $am_totalLeave, $am_totalAbsent, $pm_totalPresent, $pm_totalLeave, $pm_totalAbsent) {
    //         $excel->sheet('Excel sheet', function($sheet) use ($attendance_date,$fromdate,$todate,$class,$section,
    //             $students, $am, $pm, $am_totalPresent, $am_totalLeave, $am_totalAbsent, $pm_totalPresent, $pm_totalLeave, $pm_totalAbsent) {
    //             $sheet->setFontSize(12);
    //             $sheet->setAllBorders('thin');

    //             // $sheet->setWidth('I', 600);
    //             $sheet->loadView('users.report.attendanceExport')->with('attendance_date', $attendance_date)
    //                 ->with('am', $am)->with('pm', $pm)->with('students', $students)
    //                 ->with('am_totalPresent', $am_totalPresent)->with('am_totalLeave', $am_totalLeave)
    //                 ->with('am_totalAbsent', $am_totalAbsent)->with('pm_totalPresent', $pm_totalPresent)
    //                 ->with('fromdate',$fromdate)
    //                 ->with('todate', $todate)
    //                 ->with('class',$class)
    //                 ->with('section',$section)
    //                 ->with('pm_totalLeave', $pm_totalLeave)->with('pm_totalAbsent', $pm_totalAbsent);
    //             //->with('class',)
    //         });
    //     })->store('xls', storage_path('/public/excel'));
    //     $fileURL = storage_path() . '/public/excel/' . $filename . '.xls';
    //     \Session::put('attendanceUrl', $fileURL);
    //     $classes = $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)->get();
    //     return view('users.report.index', compact('attendances', 'type', 'am_totalLeave', 'am_totalPresent', 'am_totalAbsent', 'pm_totalLeave', 'pm_totalPresent', 'pm_totalAbsent', 'students', 'pm', 'am', 'attendance_date'));
    // }
       public function process_attendance_report($attendances, $students, $input, $filename) {
        //dd($input);
        $am_totalPresent = 0;
        $am_totalLeave = 0;
        $am_totalAbsent = 0;
        $pm_totalPresent = 0;
        $pm_totalLeave = 0;
        $pm_totalAbsent = 0;
        $att_date = array();
        $am_attendance['init'] = 'p';
        $pm_attendance['init'] = 'p';
        $am = array();
        $pm = array();
        foreach ($attendances as $key => $value) {
            if ($value->attendance_session == 'am') {
                $am_attendance[] = $value;
            } elseif ($value->attendance_session == 'pm') {
                $pm_attendance[] = $value;
            }
        }
        $attendance_date = $this->get_inbetween_date($input['from'], $input['to']);
        if($attendance_date == NULL){
            $input['error'] = 'no attendance at that date';
            return \Redirect::back()->withInput($input);
        }
        //dd($students);
        foreach ($attendance_date as $date) {
            foreach ($students as $key => $value) {
                foreach ($am_attendance as $att_key => $att_value) {//mari for v3
                    $exist = \DB::table('attendance_status')->
                    where('school_id',\Auth::user()->school_id)
                        ->where('date',$date)->where('attendance_session','am')
                        ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
                        ->first();
                        //dd($exist);
                    if(!empty($exist)){
                        $update_am_time=$exist->created_at;
                        if($exist->updated_at!=''&&$exist->updated_at!=0){
                            $update_am_time=$exist->updated_at;
                           // dd('ddddddddd');
                        }
                        if($value->created_at<$update_am_time){
                            if ($att_value->student_id == $value->id && $att_value->date == $date) {
                                $am[$date][$value->id] = $att_value->attendance;
                                if ($am[$date][$value->id] == 'L') {
                                    $am_totalLeave++;
                                } elseif($am[$date][$value->id] == 'A') {
                                    $am_totalAbsent++;
                                }
                                break;
                            } else {
                                $am[$date][$value->id] = 'P';
                            }
                        }
                        else{
                             $am[$date][$value->id] = '-';   
                        }
                    }
                    else{
                        $am[$date][$value->id] = '-';
                    }


                }
                foreach ($pm_attendance as $att_key => $att_value) {//mari for v3
                    $exist_pm = \DB::table('attendance_status')->
                    where('school_id',\Auth::user()->school_id)
                        ->where('date',$date)->where('attendance_session','pm')
                        ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
                        ->first();
                    if(!empty($exist_pm)){
                        $update_pm_time=$exist_pm->created_at;
                        if($exist_pm->updated_at!=''&&$exist_pm->updated_at!=0){
                            $update_pm_time=$exist_pm->updated_at;
                        }
                        $server_date = new DateTime();
                        $record_date = new DateTime($exist_pm->date);
                        if($value->created_at<$update_pm_time){
                            if ($att_value->student_id == $value->id && $att_value->date == $date) {

                                $pm[$date][$value->id] = $att_value->attendance;
                                if ($pm[$date][$value->id] == 'L') {
                                    $pm_totalLeave++;
                                } elseif($pm[$date][$value->id] == 'A') {
                                    $pm_totalAbsent++;
                                }
                                break;
                            } else {
                                if($server_date->format('d-m-Y') == $record_date->format('d-m-Y') && date('H') < 13){
                                    $pm[$date][$value->id] = '-';
                                    //$pm[$date][$value->id] = 'P';
                                }else{
                                    $pm[$date][$value->id] = 'P';
                                }
                            }
                        }
                        else{
                            $pm[$date][$value->id] = '-';
                        }
                    }else{
                        $pm[$date][$value->id] = '-';
                    }
                }

                if ($am[$date][$value->id] == 'P') {
                    $am_totalPresent++;
                }
                if ($pm[$date][$value->id] == 'P') {
                    $pm_totalPresent++;
                }
            }
        }
        $fromdate=$input['from'];
        $todate=$input['to'];
        $class=$input['class_name'];
        $section=$input['section_name'];
        \Excel::create($filename, function($excel) use ($attendance_date,$fromdate,$todate,$class,$section,
            $students, $am, $pm, $am_totalPresent, $am_totalLeave, $am_totalAbsent, $pm_totalPresent, $pm_totalLeave, $pm_totalAbsent) {
            $excel->sheet('Excel sheet', function($sheet) use ($attendance_date,$fromdate,$todate,$class,$section,
                $students, $am, $pm, $am_totalPresent, $am_totalLeave, $am_totalAbsent, $pm_totalPresent, $pm_totalLeave, $pm_totalAbsent) {
                $sheet->setFontSize(12);
                $sheet->setAllBorders('thin');

                // $sheet->setWidth('I', 600);
                $sheet->loadView('users.report.attendanceExport')->with('attendance_date', $attendance_date)
                    ->with('am', $am)->with('pm', $pm)->with('students', $students)
                    ->with('am_totalPresent', $am_totalPresent)->with('am_totalLeave', $am_totalLeave)
                    ->with('am_totalAbsent', $am_totalAbsent)->with('pm_totalPresent', $pm_totalPresent)
                    ->with('fromdate',$fromdate)
                    ->with('todate', $todate)
                    ->with('class',$class)
                    ->with('section',$section)
                    ->with('pm_totalLeave', $pm_totalLeave)->with('pm_totalAbsent', $pm_totalAbsent);
                //->with('class',)
            });
        })->store('xls', storage_path('/public/excel'));
        $fileURL = storage_path() . '/public/excel/' . $filename . '.xls';
        \Session::put('attendanceUrl', $fileURL);
        $classes = $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.report.index', compact('attendances', 'type', 'am_totalLeave', 'am_totalPresent', 'am_totalAbsent', 'pm_totalLeave', 'pm_totalPresent', 'pm_totalAbsent', 'students', 'pm', 'am', 'attendance_date'));
    }

    public function addExamTimeTable()
    {
        //return view('users.time_table.add_exam_time_table');
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        $teaching = \DB::table('staff')->where('school_id', $this->user->school_id)
            ->where('staff_type', 'Teaching Staff')->first();
        $teachers = \DB::table('teacher')->where('school_id', $this->user->school_id)
            ->where('session_id',$this->active_session->id)//updated 10-5-2018
            ->where('type', $teaching->id)->get();
        $exam_type = \DB::table('exam')->where('school_id', $this->user->school_id)->get();
        return view('users.time_table.add_exam_time_table', compact('classes', 'teachers','exam_type'));
    }

    //Add Exam TimeTable
    public function postExamTimeTable(Timetable $time)
    {
        //return 'exam';exit;
        $input = \Request::all();

        //echo '4 '.$input['exam_date'].'<br>';
        //exit;
        $userError = ['class' => 'Class', 'section' => 'Section', 'subject' => 'Subject',  'start_time' => 'Start Time',
            'end_time' => 'End Time', 'exam_date' => 'Exam Date', 'teacher' => 'Teacher','exam_type' => 'Exam Type '];
        $validator = \Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'subject' => 'required',
            'exam_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'teacher' => 'required',
            'exam_type' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);

        if($validator->fails())
            //return 'exam';exit;
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $time->doPostExamTimeTable($this->user, $input);
    }

    //View Exam TimeTable

    /*public function viewExamTimeTable()
    {
        return view('users.time_table.exam_time_table');
    }*/

    public function viewExamTimeTable(TimeTable $time)
    {
        return $time->doGetExamTimeTable($this->user);
    }

    //Delete Exam TimeTable
    public function deleteExamTimeTable($id)
    {
        $deleteExam = \DB::table('exam_timetable')->where('id', $id)->delete();
        if($deleteExam)
        {
            $msg['success'] = ' Success to delete Exam TimeTable';
        }
        else
        {
            $msg['error'] = ' Exam TimeTable is not deleted';
        }
        return \Redirect::back()->withInput($msg);
    }
    public function appviewExamTimeTable($platform,$id)
    {
        $get_exam_type =\DB::table('exam')->where('school_id',$this->user->school_id)
            ->where('id',$id)->first();
        $timetables = \DB::table('exam_timetable')->where('exam_timetable.school_id', $this->user->school_id)
            ->where('exam_timetable.exam_type_id', $id)
            ->leftJoin('class', 'exam_timetable.class_id', '=', 'class.id')
            ->leftJoin('section', 'exam_timetable.section_id', '=', 'section.id')
            ->leftJoin('subject', 'exam_timetable.subject_id', '=', 'subject.id')
            ->leftJoin('teacher', 'exam_timetable.teacher_id', '=', 'teacher.id')
            ->leftJoin('exam', 'exam_timetable.exam_type_id', '=', 'exam.id')
            ->select
            (
                'exam_timetable.id',
                'exam.exam_type',
                'class.class',
                'section.section',
                'subject.subject',
                'teacher.name',
                'exam_timetable.start_time',
                'exam_timetable.end_time',
                'exam_timetable.exam_date'
            )
            ->get();
        return api(['data' => $timetables]);

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

                if ($date_now > $date && $date->format('N') != 7 && !in_array($date, $holiday)) {
                    $inbetween_date[] = $date->format("Y-m-d");
                }
            }
        }
        return $inbetween_date;
    }

    // public function result() {
    //     return view('users.result.result');
    // }

    // public function resultCred() {
    //     $input = \Request::all();
    //     $class = addClass::where('id', $input['class'])->first();
    //     if (!$class) {
    //         $input['error'] = 'Class is not exist';
    //         return Redirect::back()->withInput($input);
    //     }
    //     $section = Section::where('class_id', $input['class'])->where('id', $input['section'])->first();
    //     if (!$section) {
    //         $input['error'] = 'Section is not exist';
    //         return Redirect::back()->withInput($input);
    //     }
    //     return Redirect::route('get.result', ['class' => $input['class'], 'section' => $input['section']]);
    // }
    public function result() {//changes by mari 03.10.2017

       $examtype=\DB::table('exam')->where('school_id','=',$this->user->school_id)->get();
         
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
         
        return view('users.result.result',compact('examtype','classes'));
    }
    public function resultCred() {//changes by mari 03.10.2017
         $class_id = \Request::get('class');
       
        $section = \Request::get('section');
        $exam = \Request::get('exam_type');
        $subject = \Request::get('subject');

        if ($class_id and $section and $exam and $subject) {
            $students = \DB::table('student')->where('class_id', $class_id)->where('school_id', \Auth::user()->school_id)
             ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('section_id', $section)->get();
            foreach ($students as $student) {
                //$result = \DB::table('result')->where('exam_type_id', $exam)->join('subject', 'result.subject_id', '=', 'subject.id')
               // ->where('student_id', $student->id)->get();
                $result = \DB::table('result')->where('exam_type_id', $exam)->where('subject_id', $subject)
               ->where('student_id', $student->id)->get();
               //dd($result);
                $totalObtain = 0;
                $max_total=0;
                $pass_totol=0;
                $student->result = $result;
                $result_mod = \DB::table('result_mod')->where('student_id', $student->id)->where('exam_type_id', $exam)->first();
                $student->resultof = $result_mod->result;
                $student->result_remarks = $result_mod->remarks;
                foreach ($result as $rs) {
                    $student->max_marks = $rs->max_marks;
                    $student->pass_marks = $rs->pass_marks;
                    $student->date = $rs->date;
                    if(is_numeric($rs->obtained_marks)){                        
                        $totalObtain = $totalObtain + $rs->obtained_marks;
                    }else{                        
                        $totalObtain = $totalObtain + 0;
                    }                    
                    $max_total= $max_total + $rs->max_marks;
                    $pass_totol=$pass_totol+$rs->pass_marks;
                }
                $student->totalObtain = $totalObtain;
                $student->max_total = $max_total;
                $student->pass_totol = $pass_totol;
            }
            // dd($results);
        } else {
            $classData = '';
            $sectionData = '';
            $examData = '';
            $students = '';
        }
        return view('users.result.resultdata', compact('students', 'class_id', 'section', 'exam'));
        }
    public function getResult($class, $section) {
        $students = Students::where('student.class_id', $class)
            ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
            ->where('student.section_id', $section)
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id', 'student.name', 'student.roll_no', 'class.class', 'class.id as class_id', 'section.section', 'section.id as section_id')
            ->orderBy('student.roll_no', 'ASC')
            ->get();
        $subs = Section::where('id', $section)->first();
        $subjects = Subject::whereIn('id', json_decode($subs->subjects))->select('id', 'subject')->get();
        $getclass = addClass::where('id', $class)->where('student.session_id',$this->active_session->id)//updated 14-4-2018
        ->first();
        $getsection = Section::where('id', $section)
        ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
        ->first();
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        return view('users.result.resultdata', compact('students', 'getclass', 'getsection', 'subjects', 'exams'));
    }

    // public function postResult(Result $result) {
    //     $input = \Request::all();
    //     $userError = [
    //         'class' => 'Class Id',
    //         'section' => 'Section Id',
    //         'exam_type_id' => 'Exam Type Id',
    //         'month' => 'Month',
    //         'student_marks' => 'Student Marks With Id',
    //         'date' => 'Date',
    //         'max_marks' => 'Maximum Marks',
    //         'pass_marks' => 'Passing Marks'
    //     ];
    //     $validator = \Validator::make($input, [
    //                 'class' => 'required|numeric',
    //                 'section' => 'required|numeric',
    //                 'exam_type_id' => 'required|numeric',
    //                 'month' => 'required',
    //                 'student_marks' => 'required',
    //                 'date' => 'required',
    //                 'max_marks' => 'required|numeric',
    //                 'pass_marks' => 'required|numeric'
    //                     ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if ($validator->fails())
    //         return \Redirect::back()->withInput($input)->withErrors($validator);
    //     return $result->doPostResult($input, $this->user);
    // }

    // public function viewResult() {
    //     $class_id = \Request::get('class');
    //     $section = \Request::get('section');
    //     $exam = \Request::get('exam');

    //     if ($class_id and $section and $exam) {
    //         $students = \DB::table('student')->where('class_id', $class_id)->where('school_id', \Auth::user()->school_id)->where('section_id', $section)->get();
    //         foreach ($students as $student) {
    //             $result = \DB::table('result')->where('exam_type_id', $exam)->join('subject', 'result.subject_id', '=', 'subject.id')->where('student_id', $student->id)->get();
    //             $totalObtain = 0;
    //             $student->result = $result;
    //             $result_mod = \DB::table('result_mod')->where('student_id', $student->id)->where('exam_type_id', $exam)->first();
    //             $student->resultof = $result_mod->result;
    //             $student->result_remarks = $result_mod->remarks;
    //             foreach ($result as $rs) {
    //                 $student->max_marks = $rs->max_marks;
    //                 $student->pass_marks = $rs->pass_marks;
    //                 $student->date = $rs->date;
    //                 $totalObtain = $totalObtain + $rs->obtained_marks;
    //             }
    //             $student->totalObtain = $totalObtain;
    //         }
    //         // dd($results);
    //     } else {
    //         $classData = '';
    //         $sectionData = '';
    //         $examData = '';
    //         $students = '';
    //     }
    //     return view('users.result.list', compact('students', 'class_id', 'section', 'exam'));
    // }
    public function postResult(Result $result) {//changes by mari 03.10.2017
        $input = \Request::all();

        $examtype=\DB::table('exam')->where('school_id','=',$this->user->school_id)->get();
        $subject=\DB::table('subject')->where('school_id','=',$this->user->school_id)->get();
        $userError = ['marks' => 'Student Marks '];
        $validator = \Validator::make($input, ['marks' => 'required|numeric'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return $this->result();
        $grade=\DB::table('grade_system')
            ->where('school_id','=', $this->user->school_id)
            ->where('exam_type_id','=',$input['examid'])
            ->where('from_marks','<=',$input['marks'])
            ->where('to_marks','>=',$input['marks'])
            ->first();
        if(!$grade){
            \Session::flash('error','Grade id not avaialable.');
            return view('users.result.result',compact('subject','examtype'));
        }
        $update=Result::where('exam_type_id','=',$input['examid'])->where('id','=',$input['result_id'])->update(
            ['obtained_marks'=>$input['marks'],'result'=>$grade->result,'grade'=>$grade->grade]);

        \Session::flash('success','Result is updated successfully.');
        return view('users.result.result',compact('subject','examtype'));
    }
    public function viewResult() {//changes by mari 03.10.2017
        $class_id = \Request::get('class');
        $section = \Request::get('section');
        $exam = \Request::get('exam');

        if ($class_id and $section and $exam) {
            $students = \DB::table('student')->where('class_id', $class_id)->where('school_id', \Auth::user()->school_id)
             ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('section_id', $section)->get();
            foreach ($students as $student) {
                $result = \DB::table('result')->where('exam_type_id', $exam)->join('subject', 'result.subject_id', '=', 'subject.id')
                ->where('student_id', $student->id)->get();
                $totalObtain = 0;
                $max_total=0;
                $pass_totol=0;
                $student->result = $result;
                $result_mod = \DB::table('result_mod')->where('student_id', $student->id)->where('exam_type_id', $exam)->first();
                $student->resultof = $result_mod->result;
                $student->result_remarks = $result_mod->remarks;
                foreach ($result as $rs) {
                    $student->max_marks = $rs->max_marks;
                    $student->pass_marks = $rs->pass_marks;
                    $student->date = $rs->date;
                    if(is_numeric($rs->obtained_marks)){                        
                        $totalObtain = $totalObtain + $rs->obtained_marks;
                    }else{                        
                        $totalObtain = $totalObtain + 0;
                    }                    
                    $max_total= $max_total + $rs->max_marks;
                    $pass_totol=$pass_totol+$rs->pass_marks;
                }
                $student->totalObtain = $totalObtain;
                $student->max_total = $max_total;
                $student->pass_totol = $pass_totol;
            }
            // dd($results);
        } else {
            $classData = '';
            $sectionData = '';
            $examData = '';
            $students = '';
        }
        return view('users.result.list', compact('students', 'class_id', 'section', 'exam'));
    }

    public function bookCategory() {
        $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.library.category', compact('categories'));
    }
    public function deleteBook($id)
    {
        $checkBookAvailability = Library::where('id', $id)->first();
        $getIssue = \DB::table('issue')->where('book_id',$checkBookAvailability->id)
            ->where('return_flag',0)->first();/* updated 29-9-2017 by priya */
        //return $checkBookAvailability->available;exit;
        //if($checkBookAvailability->available == '0')
        if(!$getIssue)/* updated 29-9-2017 by priya */
        {
            //return 'Not Issued';exit;
            $deleteExam = Library::where('id', $id)->delete();
            if($deleteExam)
            {
                $msg['success'] = ucwords($checkBookAvailability->book_name).' is deleted';
            }
        }
        else
        {
            //return 'Issued';exit;
            $msg['error'] = ucwords($checkBookAvailability->book_name).' is already issued.';
        }
        return \Redirect::back()->withInput($msg);
    }
    public function deleteCategory($id)
    {
        $getCategory =  \DB::table('library')->where('book_category', $id)->get();
        $get_category_name = \DB::table('book_category')->where('id', $id)->first();
        if(!$getCategory)
        {
            //return ucwords($get_category_name->category) .' is Deleted';exit;
            $deleteBookCategory = \DB::table('book_category')->where('id', $id)->delete();
            if($deleteBookCategory)
            {
                $msg['success'] = 'Category '.ucwords($get_category_name->category) .' is Deleted';
            }
        }
        else
        {
            //return 'Cant delete '.ucwords($get_category_name->category);exit;
            $msg['error'] = 'In '.ucwords($get_category_name->category).' book(s) already added.So you cant delete';/* updated 29-9-2017 by priya */
        }
        return \Redirect::back()->withInput($msg);
    }

   
    public function postCategory() {
        $input = \Request::all();
        if (!$input['category']) {
            $msg['error'] = 'Please Enter Category';
        } else {
            //$input['category'])
            $exit_categoery=\DB::table('book_category')->where('category','=',$input['category'])->where('school_id','=', \Auth::user()->school_id)->first();
            if(empty($exit_categoery)){
                \DB::table('book_category')->insert([
                    'category' => $input['category'],
                    'school_id' => \Auth::user()->school_id,
                    'fine' => $input['fine']
                ]);
                $msg['success'] = 'Success to Submit Category';
            }else
            {
                $msg['error'] = 'Category is Already Exists';
            }
        }

        return \Redirect::back()->withInput($msg);
    }
public function libraryindex()
    {
       // dd('kkkkk');
        return view('users.library.libraryindex');
    }
    public function libraryreportindex()
    {
       // dd('kkkkk');
        return view('users.library.libraryreportindex');
    }
    public function libraryreportbookIssue()
    {
      // dd('kkkkkllll');
        return view('users.library.getbookissue');
    }
    public function issuebookreportdetails()
    {
        $input = \Request::all();
        $from=date('d-m-Y', strtotime($input['from']));
        $to=date('d-m-Y', strtotime($input['to']));
        $checkissue = \DB::table('issue')->where('school_id', \Auth::user()->school_id)
       ->whereBetween('issue_date', array($from, $to))->first();

      

       if(!empty($checkissue) && $input['report_type'] == 'issue'){
        // issue report
        if($input['user_type'] == 'student'){
            $issues = \DB::table('issue')->where('issue.school_id', \Auth::user()->school_id)
            ->join('library', 'library.id', '=', 'issue.book_id')
            ->whereBetween('issue.issue_date', array($from, $to))
            ->where('issue.type', '=', 'student')->get();
            
        }elseif($input['user_type'] == 'staff'){
            $issues = \DB::table('issue')->where('issue.school_id', \Auth::user()->school_id)
            ->join('library', 'library.id', '=', 'issue.book_id')
            ->whereBetween('issue.issue_date', array($from, $to))
            ->where('issue.type', '=', 'staff')->get();
        }else{
            $issues = \DB::table('issue')->where('issue.school_id', \Auth::user()->school_id)
            ->join('library', 'library.id', '=', 'issue.book_id')
            ->whereBetween('issue.issue_date', array($from, $to))
            ->get();
        }
        
        }
        
    //return report
        $checkreturn = \DB::table('issue')->where('school_id', \Auth::user()->school_id)
       ->whereBetween('return_date', array($from, $to))->first();

      // dd($checkreturn,$input);
        if(!empty($checkreturn) && $input['report_type'] == 'return'){
        // return report
        if($input['user_type'] == 'student'){
            $issues = \DB::table('issue')->where('issue.school_id', \Auth::user()->school_id)
            ->join('library', 'library.id', '=', 'issue.book_id')
            ->whereBetween('issue.return_date', array($from, $to))
            ->where('issue.type', '=', 'student')->get();
            
        }elseif($input['user_type'] == 'staff'){
            $issues = \DB::table('issue')->where('issue.school_id', \Auth::user()->school_id)
            ->join('library', 'library.id', '=', 'issue.book_id')
            ->whereBetween('issue.return_date', array($from, $to))
            ->where('issue.type', '=', 'staff')->get();
        }else{
            $issues = \DB::table('issue')->where('issue.school_id', \Auth::user()->school_id)
            ->join('library', 'library.id', '=', 'issue.book_id')
            ->whereBetween('issue.return_date', array($from, $to))->get();
        }
        
        }
     //dd($issues);
        return view('users.library.issuereturnbooklist',compact('issues'));
    }

    public function getgateentryReport()
    {
       //dd('kkkkkllll');
        return view('users.library.getgateentryregister');
    }
     public function gateentryReport()
    {
        $input = \Request::all();
        $from=date('d-m-Y', strtotime($input['from']));
        $to=date('d-m-Y', strtotime($input['to']));
        
        $checkgate = \DB::table('librarygateregister')->where('school_id', \Auth::user()->school_id)
       ->whereBetween('date', array($from, $to))->first();
      // dd($checkgate,$input);
      // dd($checkreturn,$input);
        if(!empty($checkgate) ){
        // return report
        if($input['user_type'] == 'student'){
            $gateentries = \DB::table('librarygateregister')->where('school_id', \Auth::user()->school_id)
            ->whereBetween('date', array($from, $to))
            ->where('type', '=', 'student')->get();
            
        }elseif($input['user_type'] == 'staff'){
            $gateentries = \DB::table('librarygateregister')->where('school_id', \Auth::user()->school_id)
            ->whereBetween('date', array($from, $to))
            ->where('type', '=', 'staff')->get();
        }else{
            $gateentries = \DB::table('librarygateregister')->where('school_id', \Auth::user()->school_id)
            ->whereBetween('date', array($from, $to))->get();
        }
        
        }
    // dd($gateentries);
        return view('users.library.gateentriesreportdet',compact('gateentries'));
       
    }
    public function getlibrarysubjectReport()
    {
       // dd("kkkkk");
        $subjects = \DB::table('book_subject')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.library.subjectreport', compact('subjects'));
        /******  end  *******/


    }
    public function librarysubject_report()
    {
        $input = \Request::all();
       if($input['subject_id']== 'all'){
        $subjects = \DB::table('library')->where('school_id', \Auth::user()->school_id)->get();
       }else{
        $subjects = \DB::table('library')->where('school_id', \Auth::user()->school_id)
        ->where('subject_id', $input['subject_id'])->get();
        
       }
        
        return view('users.library.libsubjectreportdet', compact('subjects'));
        /******  end  *******/


    }
    public function bookList()
    {
       // dd("kkkkk");
        $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.library.booklist', compact('categories'));
        /******  end  *******/


    }
    
    public function bookviewlist()
    {
        $input = \Request::all();
        //dd("kkkkk",$input);
        if($input['book_category'] == 'all'){
            $books = \DB::table('library')->where('school_id', \Auth::user()->school_id)->get(); 
            $category = 'All'; 
        }else{
          $books = \DB::table('library')->where('school_id', \Auth::user()->school_id)->where('book_category', $input['book_category'])->get();  
        $category1 = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->where('id',$input['book_category'])->first();
        $category = $category1->category;
        }
       
       // dd("kkkkk",$input,$category);

        $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.library.booklist', compact('categories','books','category'));
        /******  end  *******/


    }
    public function editbooks($id)
    {
        $books = Library::join('book_category',  'library.book_category', '=', 'book_category.id')
                ->join('book_subject', 'library.subject_id', '=', 'book_subject.id')
               ->where('library.id', $id)->select('library.*','book_subject.book_subject','book_subject.id as newsubject_id','book_category.category','book_category.id as newcategory_id')
                ->first();
        
       // $books = Library::where('id', $id)->where('school_id', \Auth::user()->school_id)->where('subject',)->first();
       // 
         $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
          $subjects = \DB::table('book_subject')->where('school_id', \Auth::user()->school_id)->get();
       // dd($books,$categories,$subjects);
       return view('users.library.updatebooks', compact('books','subjects','categories'));
    }
    public function updatebooks(Library $library) {
        $input = \Request::all();
       // dd($input);
        $userError = [
            'book_no' => 'Book No',
            // 'subject_id' => 'Subject',
            'price' => 'Price',
            'book_name' => 'Book Name',
           // 'category' => 'Category',
            'auth_name' => 'Author Name',
            'pub_name' => 'Publisher Name',
            'pub_date' => 'Year of Publish',
           // 'pdate' => 'Purchase Date'
        ];
        $validator = \Validator::make($input, [
            'book_no' => 'required',
            // 'subject_id' => 'required|numeric',
            'price' => 'required',
            'book_name' => 'required',
           // 'category' => 'required',
            'auth_name' => 'required',
            'pub_name' => 'required',
            'pub_date' => 'required',
           // 'pdate' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $library->doUpdateLibrary($input, $this->user);
    }
    
    public function library()
    {
        //return 'Get Category';exit;
        $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
        $subjects = \DB::table('book_subject')->where('school_id', $this->user->school_id)->get();
        $book_category = \Request::get('book_category');
        if($book_category)
        {
            //return $book_category;exit;
            $getCategoryName = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)
                ->where('id',$book_category)
                ->first();

            /******** updated 5-10-2017 by priya **********/
            $books = Library::where('library.school_id', $this->user->school_id)
                ->where('library.book_category', $book_category)
                ->leftJoin('book_subject', 'library.subject_id', '=', 'book_subject.id')
                ->select(
                    'library.id',
                    'library.book_no',
                    'subject.book_subject',
                    'library.price',
                    'library.book_name',
                    'library.publisher_name',
                    'library.auth_name',
                    'library.issued_books',
                    'library.no_of_books',
                    'library.available'
                )
                ->orderBy('library.book_no','asc')//updated 21-10-2017 by priya
                ->get();
        }
        return view('users.library.library', compact('subjects', 'books', 'categories','getCategoryName'));
        /******  end  *******/


    }
    public function postLibrary(Library $library) {
        $input = \Request::all();
        $userError = [
            'book_no' => 'Book No',
            // 'subject_id' => 'Subject',
            'price' => 'Price',
            'book_name' => 'Book Name',
            'category' => 'Category',
            'auth_name' => 'Author Name',
            'pub_name' => 'Publisher Name',
            'pub_date' => 'Year of Publish',
           // 'vendor_name' => 'vendor_name',
            'pdate' => 'Purchase Date'
        ];
        $validator = \Validator::make($input, [
            'book_no' => 'required',
            // 'subject_id' => 'required|numeric',
            'price' => 'required',
            'book_name' => 'required',
            'category' => 'required',
            'auth_name' => 'required',
            'pub_name' => 'required',
            'pub_date' => 'required',
            //'vendor_name' => 'vendor_name',
            'pdate' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $library->doPostLibrary($input, $this->user);
    }

    public function getStudentLibrary()
    {
        $reg_no = \Request::get('reg_no');
        $book_no = \Request::get('book_no');
        //echo $this->user->school_id;
        $student = Students::where('student.registration_no', $reg_no)
            ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'class.class', 'section.section')
            ->where('student.school_id', $this->user->school_id)
            ->first();
        //dd($student);
        $getBook = \DB::table('library')->where('book_no',$book_no)
            ->where('school_id', \Auth::user()->school_id)->first();
        $getStudent = \DB::table('issue')->where('issue.student_id', $student->id)
            ->where('issue.book_id',$getBook->id)->get();

        if ($getStudent)
        {
            return 'empty';
        }
        else
        {
            $library = \DB::table('issue')->where('issue.student_id', $student->id)
                ->where('issue.return_flag', 0)
                ->leftJoin('library', 'issue.book_id', '=', 'library.id')
                ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                ->select('library.book_no', 'subject.subject', 'issue.issue_date', 'issue.return_date')->get();


            $data['library'] = $library;
            $data['student'] = $student;
            return $data;
        }
    }

    public function getTeacherLibrary(){
        $username = \Request::get('username');
        $book_no = \Request::get('book_no');
        $teacher=\DB::table('users')->where('users.username', $username)
            ->where('users.school_id', \Auth::user()->school_id)
            ->leftJoin('teacher','users.id','=','teacher.user_id')
            ->select('teacher.id','teacher.name','teacher.email','teacher.user_id')->first();
        //dd($teacher);
        $getBook = \DB::table('library')->where('book_no',$book_no)
            ->where('school_id', \Auth::user()->school_id)->first();
        $getTeacher = \DB::table('issue')->where('issue.teacher_name',$teacher->user_id)
            ->where('issue.book_id',$getBook->id)->get();

        if ($getTeacher)
        {
            return 'empty';
        }
        else
        {
            $library=\DB::table(issue)->where('issue.teacher_name',$teacher->user_id)
                ->where('issue.return_flag', 0)
                ->leftJoin('library', 'issue.book_id', '=', 'library.id')
                ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                ->select('library.book_no', 'subject.subject', 'issue.issue_date', 'issue.return_date')->get();


            $data['library'] = $library;
            $data['teacher'] = $teacher;
            return $data;
        }
    }
    public function getbookLibrary(){
        $book_no=\Request::get('book_no');
        $check = Library::where('school_id', \Auth::user()->school_id)->where('book_no', $book_no)->first();
        //print_r($check);
        if(!$check)
        {
            return 'empty';
        }
        else
        {
            if($check->available==1)
            {
                $issue_det= \DB::table('issue')->where('issue.book_id', $check->id)
                    ->where('issue.school_id', \Auth::user()->school_id)
                    ->where('issue.student_id','!=','0')
                    ->leftJoin('student', 'issue.student_id', '=', 'student.id')
                    ->leftJoin('library', 'issue.book_id', '=', 'library.id')
//                            ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                    ->select('student.name','library.book_no', 'library.book_name', 'issue.issue_date', 'issue.return_date')->get();

                if(!$issue_det){
                    $issue_det= \DB::table('issue')->where('issue.book_id', $check->id)
                        ->where('issue.school_id', \Auth::user()->school_id)
                        ->whereNotNull('issue.teacher_name')
                        ->leftJoin('teacher', 'issue.teacher_name', '=', 'teacher.user_id')
                        ->leftJoin('library', 'issue.book_id', '=', 'library.id')
//                            ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                        ->select('teacher.name','library.book_no', 'library.book_name', 'issue.issue_date', 'issue.return_date')->get();
                    $issue_det['teacher']=1;
                }
                return $issue_det;
            }
            else
            {
                return 'Not available';
            }
        }
    }

    public function issueBook() {
        return view('users.library.issue');
    }
    public function bookSubject() {
       $subjects = \DB::table('book_subject')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.library.booksubject', compact('subjects'));
    }
    public function postbookSubject() {
        $input = \Request::all();
        if (!$input['subject']) {
            $msg['error'] = 'Please Enter Subject';
        } else {

            $exit_subject=\DB::table('book_subject')->where('book_subject','=',$input['subject'])->where('school_id','=', \Auth::user()->school_id)->first();
            if(empty($exit_subject)){
                \DB::table('book_subject')->insert([
                    'book_subject' => $input['subject'],
                    'school_id' => \Auth::user()->school_id,
                   // 'fine' => $input['fine']
                ]);
                $msg['success'] = 'Success to Submit Subject';
            }else
            {
                $msg['error'] = 'Subject is Already Exists';
            }
        }

        return \Redirect::back()->withInput($msg);
    }

    public function issueBookPost(Library $library) {
        $input = \Request::all();

        if($input['user_role'] === 'Student'){
            //echo 'students';
            $userError = [
                'book_no' => 'Book No',
                'registration_no' => 'Registration No',
                'issue_date' => 'Issue Date',
                'return_date' => 'Return Date'
            ];
            $validator = \Validator::make($input, [
                'book_no' => 'required',
                'registration_no' => 'required',
                'issue_date' => 'required',
                'return_date' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails()){
                return \Redirect::back()->withInput($input)->withErrors($validator);
            }else
            {
                return $library->doIssueBookPost($input, $this->user);
            }


        }
        else
        {
            //echo "vasu";
            $userErrors = [
                'book_no' => 'Book No',
                'user_name' => 'User Name',
                'issue_date' => 'Issue Date',
                'return_date' => 'Return Date'
            ];
            $validators = \Validator::make($input, [
                'book_no' => 'required',
                'user_name' => 'required',
                'issue_date' => 'required',
                'return_date' => 'required'
            ], $userErrors);
            $validators->setAttributeNames($userErrors);

            if ($validators->fails()){
                return \Redirect::back()->withInput($input)->withErrors($validators);
            }else{
                return $library->doIssueBookPostTeacher($input, $this->user);
            }


        }

    }

    // public function bookInfo() {
    //     $book = \Request::get('book_no');
    //     $getBook = Library::where('book_no', $book)->where('school_id',$this->user->school_id)->first();
    //     $bookInfo = \DB::table('issue')->where('book_id', $getBook->id)->where('issue.return_flag', 0)
    //             ->where('issue.student_id','!=','0')
    //             ->leftJoin('library', 'issue.book_id', '=', 'library.id')
    //             ->leftjoin('book_category', 'library.book_category', '=', 'book_category.id')
    //             ->leftJoin('student', 'issue.student_id', '=', 'student.id')
    //             ->select('issue.student_id','student.name', 'student.registration_no', 'library.book_no', 'issue.return_date', 'issue.issue_date', 'book_category.category', 'book_category.fine')
    //             ->first();
    //     if(!$bookInfo)
    //     {
    //         $bookInfo = \DB::table('issue')->where('book_id', $getBook->id)->where('issue.return_flag', 0)
    //             ->leftJoin('library', 'issue.book_id', '=', 'library.id')
    //             ->leftjoin('book_category', 'library.book_category', '=', 'book_category.id')
    //             ->leftJoin('teacher', 'issue.teacher_name', '=', 'teacher.user_id')
    //             ->select('issue.teacher_name','teacher.name', 'library.book_no', 'issue.return_date', 'issue.issue_date', 'book_category.category', 'book_category.fine')
    //             ->first();
    //     }
    //     if (!$bookInfo) {
    //         return 'empty';
    //     } else {
    //         if (strtotime(date('d-m-Y')) <= strtotime($bookInfo->return_date)) {
    //             $fine = 0;
    //         } else {
    //             $diff = strtotime(date('d-m-Y')) - strtotime($bookInfo->return_date);
    //             $total_fine = floor($diff / (60 * 60 * 24));
    //             $fine = $total_fine * $bookInfo->fine;
    //         }
    //         $data['fine'] = $fine;
    //         $data['bookInfo'] = $bookInfo;
    //         return $data;
    //     }
    // }
    public function bookInfo()
    {
        $book = \Request::get('book_no');
        $getBook = Library::where('book_no', $book)->where('school_id',$this->user->school_id)->first();
        $bookInfo = \DB::table('issue')->where('book_id', $getBook->id)
            ->where('return_flag', 0)->first();
        if(!$bookInfo)
        {
            return 'empty';
        }
        else
        {
            //return $bookInfo->book_id;
            return 'Available';
        }
    }

    public function fineReceipt($id) {
        $bookInfo = \DB::table('issue')
            ->where('book_id', $id)
            ->leftJoin('library', 'issue.book_id', '=', 'library.id')
            ->leftjoin('book_category', 'library.book_category', '=', 'book_category.id')
            ->leftJoin('student', 'issue.student_id', '=', 'student.id')
            ->select(
                'student.name', 'student.registration_no', 'library.book_no', 'issue.return_date', 'issue.issue_date', 'book_category.category', 'book_category.fine'
            )
            ->first();
        \Excel::create("finereceipt-" . $student->registration_no, function($excel) use ($bookInfo) {

            $excel->sheet('Excel sheet', function($sheet) use ($bookInfo) {
                $sheet->loadView('users.result.finereceipt')->with('bookInfo', $bookInfo);
                $sheet->setOrientation('portrait');
            });
        })->download('pdf');
    }

    public function returnBook() {
        return view('users.library.return');
    }

    // public function returnBookPost(Library $library) {
    //     $input = \Request::all();
    //     $userError = [
    //         'book_no' => 'Book No',
    //         'bookrel' => 'Book Option',
    //         'return_date' => 'Return Date'
    //     ];
    //     $validator = \Validator::make($input, [
    //                 'book_no' => 'required',
    //                 'bookrel' => 'required',
    //                     ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if ($validator->fails())
    //         return \Redirect::back()->withInput($input)->withErrors($validator);
    //     return $library->doReturnBookPost($input, $this->user);
    // }
    public function returnBookPost(Library $library) {
        $input = \Request::all();
        $userError = [
            'book_no' => 'Book No',
            'user_role' => 'User',
            // 'registration_no' => 'Registration No',
            'user_name' => 'User Name',
            'bookrel' => 'Book Option',
            'return_date' => 'Return Date'
        ];
        $validator = \Validator::make($input, [
            'book_no' => 'required',
            'user_role' => 'required',
            // 'registration_no' => 'required',
            'bookrel' => 'required',
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $library->doReturnBookPost($input, $this->user);
    }

    public function attendanceReport(Report $report) {
       // dd('dddddddddd');
        return $report->doAttendanceReport($this->user);
    }

    public function holiday() {
        return view('users.holiday.index');
    }

    public function managerData() {
        return view('users.data-manager.index');
    }

    public function importStudent(Students $student, Employee $emp) {
        $input = \Request::all();
        $userError = ['data' => 'Manager Type', 'file' => 'File'];
        $validator = \Validator::make($input, ['data' => 'required', 'file' => 'required',], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails()) {
            return \Redirect::back()->withInput($input)->withErrors($validator);
        }
        if ($input['data'] == 'student') {
            return $student->doImportStudent($this->user, $input);
        }
        if ($input['data'] == 'employee') {
            return $emp->doImportEmployee($this->user, $input);
        }
        if ($input['data'] == 'studentfees') {
            return $student->doImportStudentFees($this->user, $input);
        }
        if ($input['data'] == 'studentmapping') {
            return $student->doImportMapping($this->user, $input);
        }
    }

    public function managerExport() {
        return view('users.export-manager.index');
    }

    public function post() {
        $post = Post::where('school_id', $this->user->school_id)->first();
        return view('users.post.post', compact('post'));
    }

    public function postPost(Post $post) {
        $input = \Request::all();
        $userError = ['image' => 'Post Image'];
        $validator = \Validator::make($input, [
            'image' => 'required|image'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $post->doPostPost($this->user, $input);
    }

    public function gallery() {
        $galleries = Gallery::where('school_id', $this->user->school_id)->get();
        return view('users.gallery.addgallery', compact('galleries'));
    }

    public function getGallery()
    {
        $galleries = Gallery::with('hasManyImages')
            ->where('school_id', $this->user->school_id)->get();
        return view('users.gallery.gallery', compact('galleries'));
    }

    public function deleteGallery($id) {
        \DB::table('gallery_img')->where('gallery_id', $id)->delete();
        \DB::table('gallery')->where('id', $id)->delete();
        $msg['success'] = 'Success to Delete Gallery';
        return \Redirect::back()->withInput($msg);
    }

    public function editGallery($id) {
        $gallery = \DB::table('gallery')->where('id', $id)->first();
        $images = \DB::table('gallery_img')->where('gallery_id', $gallery->id)->get();
        return view('users.gallery.edit', compact('gallery', 'images'));
    }

    public function updateGallery(Gallery $gallery) {
        $input = \Request::all();
        $userError = ['id' => 'Id', 'header' => 'Event Name', 'files' => 'Images'];
        $validator = \Validator::make($input, ['id' => 'required|numeric', 'header' => 'required'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails()) {
            return \Redirect::back()->withInput($input)->withErrors($validator);
        } else {
            return $gallery->doUpdateGallery($this->user, $input);
        }
    }

    public function fieldsGallery(Gallery $gallery) {
        $input = \Request::all();
        $userError = ['date' => 'Date', 'header' => 'Event Name'];
        $validator = \Validator::make($input, ['date' => 'required'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \api::notValid(['errorMsg' => $validator->errors()->first()]);
        return $gallery->doFieldsGallery($this->user, $input);
    }

    public function postGallery(Gallery $gallery, $id) {
        $input = \Request::all();
        $userError = ['file' => 'Image'];
        $validator = \Validator::make($input, ['file' => 'required|image'], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \api::notValid(['errorMsg' => $validator->errors()->first()]);
        return $gallery->doPostGallery($this->user, $input, $id);
    }

    public function usersRole() {

        $get = \DB::table('users')
            ->where('school_id', \Auth::user()->school_id)
            ->where('type', 'user_role')
            ->get();
        foreach ($get as $key => $value) {
            $val = \DB::table('user_role')
                ->where('role_id', $value->id)
                ->get();
            $value->permission = $val;
        }
        return view('users.user-role.index', compact('get'));
    }

    // public function asignUsersRole($id) {
    //     $get = \DB::table('users')
    //             ->where('school_id', \Auth::user()->school_id)
    //             ->where('type', 'user_role')
    //             ->get();
    //     foreach ($get as $key => $value) {
    //         $val = \DB::table('user_role')
    //                 ->where('role_id', $value->id)
    //                 ->get();
    //         $value->permission = $val;
    //     }
    //     $teacher = \DB::table('teacher')
    //                     ->where('school_id', \Auth::user()->school_id)
    //                     ->where('id', $id)->first();
    //     $asign_user = \DB::table('users')->where('id', $teacher->user_id)->first();
    //     $username = $asign_user->username;
    //     $password = $asign_user->hint_password;
    //     return view('users.user-role.index', compact('get', 'username', 'password'));
    // }
    public function asignUsersRole($id) {

        $getroleval = \DB::table('teacher')->where('teacher.id', $id)
            ->join('users', 'teacher.user_id', '=', 'users.id')
            ->join('user_role', 'teacher.user_id', '=', 'user_role.role_id')
            ->select('users.id','users.username', 'users.hint_password', 'user_role.permission_id', 'user_role.value')->get();
        //dd($getrole);
        if($getroleval){
            $getrole=array();
            foreach ($getroleval as $key => $getroles) {
                if($key == 0)
                {
                    $getrole['username']=$getroles->username;
                    $getrole['password']=$getroles->hint_password;
                    $getrole['id']=$getroles->id;
                }
                $getrole['permission_id'][]=$getroles->permission_id;
                $getrole['value'][]=$getroles->value;
            }
            $getrole['permission_id']=array_unique($getrole['permission_id']);
            $getrole['value']=array_unique($getrole['value']);
            //dd($getrole['value']);
        }
        else
        {
            $getroleval = \DB::table('teacher')->where('teacher.id', $id)
                ->join('users', 'teacher.user_id', '=', 'users.id')
                ->select('users.username', 'users.hint_password')->first();
            //dd($getroleval);
            $getrole=array();
            $getrole['username']=$getroleval->username;
            $getrole['password']=$getroleval->hint_password;
            $getrole['value']=array();


        }
        return view('users.user-role.index', compact('getrole'));
    }


    // public function deleteUserRole($id) {
    //     \DB::table('user_role')->where('role_id', $id)->delete();
    //     \DB::table('users')->where('id', $id)->delete();
    //     $msg['success'] = 'Success to Delete User';
    //     return \Redirect::back()->withInput($msg);
    // }

    // public function changePassword($id) {
    //     return view('users.user-role.change_pass', compact('id'));
    // }

    // public function postPassword($id) {
    //     $input = \Request::get('password');

    //     \DB::table('users')->where('id', $id)->update([
    //         'password' => \Hash::make($input),
    //         'hint_password' => $input
    //     ]);
    //     $msg['success'] = 'Success to Change Password';
    //     return \Redirect::route('user.usersRole')->withInput($msg);
    // }
    public function deleteUserRole($id) {

        $deleteuserrole=\DB::table('user_role')->where('role_id', $id)->delete();
        $val=\DB::table('users')->where('users.id', $id)
            ->join('teacher', 'users.id', '=','teacher.user_id')
            ->select('teacher.id')->first();

        $msg['success'] = 'Success to Delete User';
        return \Redirect::route('asign.usersRole', $val->id)->withInput($msg);
    }

    public function changePassword($id) {
        return view('users.user-role.change_pass', compact('id'));
    }

    public function postPassword($id) {
        $input = \Request::get('password');

        \DB::table('users')->where('id', $id)->update([
            'password' => \Hash::make($input),
            'hint_password' => $input
        ]);
        $val=\DB::table('users')->where('users.id', $id)
            ->join('teacher', 'users.id', '=','teacher.user_id')
            ->select('teacher.id')->first();
        $msg['success'] = 'Success to Change Password';
        return \Redirect::route('asign.usersRole', $val->id)->withInput($msg);
    }

    public function deduction() {
        $deductions = \DB::table('deduction')->where('school_id', $this->user->school_id)->get();
        return view('users.payroll.deducation', compact('deductions'));
    }

    public function postDeduction() {
        $input = \Request::all();

        if (is_numeric($input['percentage'])) {
            \DB::table('deduction')->insert(['school_id' => $this->user->school_id, 'deduction_type' => $input['deduction'], 'percentage' => $input['percentage']]);
            $msg['success'] = 'Success to Submit Deduction';
            return \Redirect::back()->withInput($msg);
        } else {
            $msg['error'] = 'Percentage in only Numberic Value input';
            return \Redirect::back()->withInput($msg);
        }
    }

    public function deleteDeduction($id) {
        \DB::table('deduction')->where('school_id', $this->user->school_id)->where('id', $id)->delete();
        $msg['success'] = 'Success to Delete Deduction Type';
        return \Redirect::back()->withInput($msg);
    }

    public function inputSalary() {
        $staff_type = \DB::table('staff')->where('school_id', $this->user->school_id)->get();
        $inputSalary = \DB::table('salary')->where('salary.school_id', $this->user->school_id)
            ->join('staff', 'salary.staff_type', '=', 'staff.id')
            ->join('teacher', 'salary.employee_id', '=', 'teacher.id')
            ->select('salary.id', 'salary.value', 'teacher.name', 'teacher.salary', 'staff.staff_type')
            ->orderBy('salary.id', 'DESC')->get();
        return view('users.payroll.salaryInput', compact('staff_type', 'inputSalary'));
    }

    public function employeeGet($staff) {
        $employee = \DB::table('teacher')->where('school_id', $this->user->school_id)->where('type', $staff)->get();
        return api(['data' => $employee]);
    }

    public function employeeSalaryPost() {
        $input = \Request::all();
        $userError = ['staff_type' => 'Employee Type', 'employee' => 'Employee Name', 'salary' => 'Salary'];
        $validator = \Validator::make($input, [
            'staff_type' => 'required',
            'employee' => 'required',
            'salary' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
            return \Redirect::back()->withErrors($validator);
        \DB::table('salary')->insert([
            'staff_type' => $input['staff_type'],
            'school_id' => $this->user->school_id,
            'employee_id' => $input['employee'],
            'value' => $input['salary']
        ]);
        $msg['success'] = 'Success to Submit';
        return \Redirect::back()->withInput($msg);
    }

    public function employeeSalaryDelete($id) {
        \DB::table('salary')->where('school_id', $this->user->school_id)->where('id', $id)->delete();
        $msg['success'] = 'success to delete';
        return \Redirect::back()->withInput($msg);
    }

    public function calculateSalary(Salary $calc) {
        return $calc->salaryCalc($this->user);
    }

    // public function usersRole()
    // {
    //     return view('users.user-role.index');
    // }

    public function userRolePost() {
        $input = \Request::all();
        $userError = ['username' => 'Username', 'password' => 'Password'];
        $validator = \Validator::make($input, [
            'username' => 'required',
            'password' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails()) {
            return \Redirect::back()->withErrors($validator);
        } else {
            if (count($input['accessLevels']) > 0) {
                $exist = \DB::table('users')->where('school_id', $this->user->school_id)->where('username', $input['username'])->first();
                if ($exist) {
                    $checkid=\DB::table('user_role')->where('role_id',$exist->id)->select('permission_id')->get();
                    $alreadyarray=array();
                    foreach ($checkid as $key => $value) {

                        $alreadyarray[]=$checkid[$key]->permission_id;

                    }

                    $arrayval=array();
                    foreach ($input['accessLevels'] as $key => $level) {
                        $arrayval[]=$key;
                        \DB::table('user_role')->insert([
                            'role_id' => $exist->id,
                            'permission_id' => $key,
                            'value' => $level
                        ]);
                    }
                    $res=array_diff($alreadyarray, $arrayval);
                    foreach ($res as $key => $value) {
                        \DB::table('user_role')->where('role_id',$exist->id)->where('permission_id',$value)->delete();
                    }
                    \DB::table('users')->where('id', $exist->id)->update(['type' => 'user_role']);
                    $input['success'] = 'User Role is added successfully';
                    return \Redirect::back()->withInput($input);
                } else {
                    $id = \DB::table('users')->insertGetId([
                        'type' => 'user_role',
                        'school_id' => $this->user->school_id,
                        'username' => $input['username'],
                        'password' => \Hash::make($input['password']),
                        'hint_password' => $input['password']
                    ]);
                    if ($id) {
                        foreach ($input['accessLevels'] as $key => $level) {
                            \DB::table('user_role')->insert([
                                'role_id' => $id,
                                'permission_id' => $key,
                                'value' => $level
                            ]);
                        }
                    }
                    $input['success'] = 'User Role is added successfully';
                    return \Redirect::back()->withInput($input);
                }
            } else {
                $input['error'] = 'Select atleast one access level';
                return \Redirect::back()->withInput($input);
            }
        }
    }

    public function viewNotification() {
        $notifications = \DB::table('notification_history')
            ->where('notification_history.school_id', $this->user->school_id)
            ->leftJoin('notification_type', 'notification_history.notification_type_id', '=', 'notification_type.id')
            ->select
            (
                'notification_history.id', 'notification_type.title', 'notification_type.description', 'notification_history.date', 'notification_history.role', 'notification_history.message_type'
            )
            ->orderBy('notification_history.id', 'DESC')->get();

        return view('users.notification.view', compact('notifications'));
    }

    public function knowledgeBank() {
        $class_id = \Request::get('class');
        return view('users.knowledge.input', compact('class_id'));
    }

    public function studentsReport() {
         $session = \Request::get('session');
        $class = \Request::get('class');
        $caste = \Request::get('caste');
        $religion = \Request::get('religion');
        //dd($class);
        $sessions = \DB::table('session')->where('school_id', $this->user->school_id)->get();
        $castes = \DB::table('caste')->where('school_id', \Auth::user()->school_id)->get();
        $religions = \DB::table('religion')->where('school_id', \Auth::user()->school_id)->get();
        $students = '';
        if ($session != null AND $class != null AND $caste != null AND $religion != null) {
            $sessionGet = \DB::table('session')->where('school_id', $this->user->school_id)->where('id', $session)->first();
            $students = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id', $session)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->Join('caste', 'student.caste_id', '=', 'caste.id')
                ->Join('religion', 'student.religion', '=', 'religion.id');
            if ($class != 0) {
                $students = $students->where('student.class_id', $class);
            }
             if ($caste != 0) {
                $students = $students->where('student.caste_id', $caste);
            }
             if ($religion != 0) {
                $students = $students->where('student.religion', $religion);
            }
            $students = $students->select('student.id', 'student.name', 'student.registration_no', 'student.roll_no', 'student.dob', 'student.date_of_admission','student.date_of_joining','student.gender', 'student.previous_school', 'student.blood_group','student.aadhar_no','student.emi_no','student.rte','class.class', 'section.section', 'caste.caste', 'religion.religion','parent.name as father', 'parent.mother', 'parent.mobile as parent_mobile', 'parent.email as parent_mail', 'parent.city', 'parent.address', 'parent.pin_code')->get();
            $students['session'] = $sessionGet->session;

            \Excel::create('students-' . $session, function($excel) use ($students) {
                $excel->sheet('Excel sheet', function($sheet) use ($students) {
                    $sheet->setFontSize(12);
                    $sheet->setAllBorders('thin');
                    $sheet->loadView('users.report.studentsExport')->with('students', $students);
                });
            })->store('xls', storage_path('students'));
            $fileURL = storage_path() . '/students/students-' . $session . '.xls';
            \Session::put('attendanceUrl', $fileURL);
            // dd($students);
        } else {
            $msg['error'] = 'Please Choose Class and Session';
        }

        return view('users.report.student', compact('sessions', 'students', 'religions','castes','fileURL'))->withInput($msg);
    }
        //dd('rrrrrrrrrr');
        /*$session = \Request::get('session');
        $class = \Request::get('class');

        $sessions = \DB::table('session')->where('school_id', $this->user->school_id)->get();
        $students = '';
        if ($session != null AND $class != null) {
            $sessionGet = \DB::table('session')->where('school_id', $this->user->school_id)->where('id', $session)->first();
            $students = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id', $session)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id');
            if ($class != 0) {
                $students = $students->where('student.class_id', $class);
            }
            $students = $students->select('student.id', 'student.name', 'student.registration_no', 'student.roll_no', 'student.dob', 'student.gender', 'student.contact_no', 'student.email', 'student.previous_school', 'student.blood_group', 'class.class', 'section.section', 'parent.name as father', 'parent.mother', 'parent.mobile as parent_mobile', 'parent.email as parent_mail', 'parent.city', 'parent.address', 'parent.pin_code')->get();
            $students['session'] = $sessionGet->session;

            \Excel::create('students-' . $session, function($excel) use ($students) {
                $excel->sheet('Excel sheet', function($sheet) use ($students) {
                    $sheet->setFontSize(12);
                    $sheet->setAllBorders('thin');
                    $sheet->loadView('users.report.studentsExport')->with('students', $students);
                });
            })->store('xls', storage_path('students'));
            $fileURL = storage_path() . '/students/students-' . $session . '.xls';
            \Session::put('attendanceUrl', $fileURL);
            // dd($students);
        } else {
            $msg['error'] = 'Please Choose Class and Session';
        }

        return view('users.report.student', compact('sessions', 'students', 'fileURL'))->withInput($msg);*/
    

    public function searchStudent(Request $request) {
        $get = \DB::table('student')->where('registration_no', $request['search'])
            ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
            ->where('student.school_id', $this->user->school_id)
            ->join('class', 'student.class_id', '=', 'class.id')
            ->select('student.id', 'student.name', 'student.registration_no', 'class.class')
            ->get();
        if (!$get) {
            $get = \DB::table('student')->where('name', 'LIKE', '%' . $request['search'] . '%')
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->where('student.school_id', $this->user->school_id)
                ->join('class', 'student.class_id', '=', 'class.id')
                ->select('student.id', 'student.name', 'student.registration_no', 'class.class')
                ->get();
        }
        if (!$get)
            return api()->notFound(['errorMsg' => 'Not Found']);
        return api(['data' => $get]);
    }

    public function getInfoStudent($id) {
        $get = \DB::table('student')
            ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
            ->where('student.school_id', $this->user->school_id)
            ->where('student.id', $id)
            ->join('parent', 'student.parent_id', '=', 'parent.id')
            ->join('class', 'student.class_id', '=', 'class.id')
            ->leftjoin('section', 'student.section_id', '=', 'section.id')
            ->leftjoin('bus_stop', 'student.bus_stop_id', '=', 'bus_stop.id')
            ->select('student.registration_no', 'student.name', 'student.dob', 'student.email', 'student.id', 'parent.name as father', 'parent.mother', 'parent.mobile', 'class.class', 'section.section', 'student.avatar', 'student.class_id', 'student.bus_stop_id', 'bus_stop.transport_fee')
            ->first();
        $transport_fee = \DB::table('bus_stop')->where('school_id', $this->user->school_id)->where('id', $get->bus_stop_id)->first();
        $security = \DB::table('security_fee')
            ->where('school_id', $this->user->school_id)
            ->where('class_id', $get->class_id)
            ->first();
        $fee = \DB::table('fee_head')
            ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', $this->user->school_id)->where('fee_head.fee_head_type', 'month')->get();
        $month_total = 0;

        foreach ($fee as $fe) {
            $month_total = $month_total + $fe->amount;
        }

        $annual = $month_total * 12;
        $months = [
            ['month' => '1', 'name' => 'January', 'status' => '0'],
            ['month' => '2', 'name' => 'February', 'status' => '0'],
            ['month' => '3', 'name' => 'March', 'status' => '0'],
            ['month' => '4', 'name' => 'April', 'status' => '0'],
            ['month' => '5', 'name' => 'May', 'status' => '0'],
            ['month' => '6', 'name' => 'June', 'status' => '0'],
            ['month' => '7', 'name' => 'July', 'status' => '0'],
            ['month' => '8', 'name' => 'August', 'status' => '0'],
            ['month' => '9', 'name' => 'September', 'status' => '0'],
            ['month' => '10', 'name' => 'October', 'status' => '0'],
            ['month' => '11', 'name' => 'November', 'status' => '0'],
            ['month' => '12', 'name' => 'December']
        ];
        foreach ($months as $key => $month) {
            $test = \DB::table('fee_collection')
                ->where('school_id', \Auth::user()->school_id)
                ->where('student_id', $get->id)
                ->where('months', 'LIKE', '%' . $month['month'] . '%')
                ->first();
            // dd($month['month'], $test);

            if (!$test) {
                $months[$key]['status'] = 0;
            } else {
                $months[$key]['status'] = 1;
            }
        }
        $totalPay = \DB::table('fee_collection')->where('student_id', $get->id)->where('fee_collection.school_id', $this->user->school_id)->get();
        $countPay = 0;
        $total_discount = 0;


        foreach ($totalPay as $pay) {
            $total_discount = $total_discount + (int) $pay->discount;
            $countPay = $countPay + (int) $pay->pay_amount;
        }
        $val = \DB::table('fee_head')
            ->where('fee_head.school_id', \Auth::user()->school_id)
            ->where('fee_head.fee_head_type', 'annual')
            ->join('fee_head_amount', 'fee_head_amount.fee_head_id', '=', 'fee_head.id')
            ->where('fee_head_amount.class_id', $get->class_id)
            ->get();
        $annualfee = 0;
        foreach ($val as $key => $value) {
            $annualfee = $annualfee + $value->amount;
        }

        $total_fee = $annual + $annualfee + (int) $transport_fee->transport_fee;

        $balance = ($annual + $annualfee + (int) $transport_fee->transport_fee) - ($countPay + $total_discount);
        $getHead = \DB::table('fee_head')
            ->where('fee_head.school_id', \Auth::user()->school_id)
            ->join('fee_head_amount', 'fee_head_amount.fee_head_id', '=', 'fee_head.id')
            ->where('fee_head_amount.class_id', '=', $get->class_id)
            ->where('fee_head.fee_head_type', 'month')
            ->get();
        $total = 0;
        foreach ($getHead as $key => $value) {
            $total = $total + $value->amount;
        }

        if (!$get)
            return api()->notFound(['errorMsg' => 'Not Found']);
        return api([
            'data' => $get,
            'annual' => $annual,
            'annualfee' => $val,
            'pay' => $countPay,
            'balance' => $balance,
            'transport_fee' => $transport_fee,
            'months' => $months,
            'total_fee' => $total_fee,
            'total' => $total,
            'head' => $getHead,
            'total_discount' => $total_discount
        ]);
    }

    public function getMonthFeeHead($id, $month) {
        $student = \DB::table('student')
            ->where('id', $id)
            ->where('school_id', \Auth::user()->school_id)
            ->first();

        $get = \DB::table('fee_head')
            ->where('fee_head.school_id', \Auth::user()->school_id)
            ->join('fee_head_amount', 'fee_head_amount.fee_head_id', '=', 'fee_head.id')
            ->where('fee_head_amount.class_id', $student->class_id)
            ->where('fee_head.fee_head_type', 'month')
            ->get();

        $total = 0;
        foreach ($get as $key => $value) {
            $value->amount = $value->amount * $month;
            $total = $total + $value->amount;
        }
        return api(['data' => $get, 'total' => $total]);
    }

    public function feeCollectionPost() {
        $input = \Request::all();
        $input['discount'] = (isset($input['discount']) ? $input['discount'] : 0);
        $student = \DB::table('student')->where('id', $input['student_id'])->first();
        $input['date'] = date('d-m-Y', strtotime($input['date']));

        $invoiceNo = \DB::table('fee_collection')->insertGetId([
            'student_id' => $input['student_id'],
            'school_id' => $this->user->school_id,
            'date' => $input['date'],
            'pay_type' => $input['pay_type'],
            'months' => json_encode($input['month']),
            'remarks' => $input['remarks'],
            'discount' => $input['discount'],
            'pay_amount' => $input['pay']
        ]);

        $get = \DB::table('student')
            ->where('student.school_id', $this->user->school_id)
            ->where('student.session_id',$this->active_session->id)//updated 14-4-2018    
            ->where('student.id', $input['student_id'])
            ->join('school', 'student.school_id', '=', 'school.id')
            ->join('parent', 'student.parent_id', '=', 'parent.id')
            ->join('class', 'student.class_id', '=', 'class.id')
            ->leftjoin('bus_stop', 'student.bus_stop_id', '=', 'bus_stop.id')
            ->select('student.id', 'student.registration_no', 'student.name', 'school.school_name', 'school.mobile', 'school.address', 'parent.address as parent_address', 'parent.city', 'class.class', 'student.class_id', 'parent.name as father_name', 'student.bus_stop_id', 'bus_stop.transport_fee')
            ->first();

        $transport_fee = \DB::table('bus_stop')->where('school_id', $this->user->school_id)->where('id', $get->bus_stop_id)->first();

        $security = \DB::table('security_fee')
            ->where('school_id', $this->user->school_id)
            ->where('class_id', $get->class_id)
            ->first();
        $fee = \DB::table('fee_head')
            ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', \Auth::user()->school_id)->get();

        $monthfee = \DB::table('fee_head')
            ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', \Auth::user()->school_id)->where('fee_head.fee_head_type', 'month')->get();

        $annual_fee = \DB::table('fee_head')
            ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', \Auth::user()->school_id)->where('fee_head.fee_head_type', 'annual')->get();

        $month_total = 0;
        foreach ($monthfee as $fe) {
            $month_total = $month_total + $fe->amount;
            $monthannual = $month_total * 12;
        }
        $annualfee = 0;
        foreach ($annual_fee as $fe) {
            $annualfee = $annualfee + $fe->amount;
        }

        $total_fee = $monthannual + $transport_fee->transport_fee + $annualfee;
        $totalPay = \DB::table('fee_collection')->where('student_id', $get->id)->get();

        $countPay = 0;
        $total_discount = 0;
        foreach ($totalPay as $pay) {
            $total_discount = $total_discount + (int) $pay->discount;
            $countPay = $countPay + (int) $pay->pay_amount;
        }
        $balance = ($monthannual + $annualfee + $transport_fee->transport_fee) - ($countPay + $total_discount);
        $get->balance = $balance;
        $get->total_fee = $total_fee;
        $get->pay = ($countPay + $total_discount) - ((int) $input['discount'] + (int) $input['pay']);
        $get->totalpayment = $countPay + $total_discount;
        $get->discount = $input['discount'];
        $get->total_discount = $total_discount;
        $get->annual = $annual;
        $get->invoiceNo = $invoiceNo;
        foreach ($fee as $fe) {
            if ($fe->fee_head_type == 'annual') {
                $fe->month_fee = $fe->amount;
            } else {
                $fe->month_fee = $fe->amount * 12;
            }
        }

        // \Excel::create("Invoice-".$student->registration_no.'-'.$student->name.'-'.$input['date'], function($excel) use ($get, $input, $fee) {
        //     $excel->sheet('Excel sheet', function($sheet) use ($get, $input, $fee) {
        //         $sheet->loadView('users.fee_collection.invoice_regular')->with('input',$input)->with('get',$get)->with('fee', $fee);
        //         $sheet->setOrientation('portrait');
        //     });
        // })->download('pdf');
        // $fileURL = storage_path()."/collection/Invoice-".$student->registration_no.'-'.$student->name.'-'.$input['date'].'.pdf';
        // \Session::put('attendanceUrl', $fileURL);
        // $msg['success'] = 'Success to Submit Fee';

        return View('users.fee_collection.invoiceNew', compact('get', 'input', 'fee', 'monthannual', 'annualfee'));
    }

    public function invoiceCreate() {
        return view('users.fee_collection.invoiceNew');
    }

    public function feeCollectionReport()
    {
        $input = \Request::all();

        /*updated 20-4-2018*/
        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        /*end*/

        if ($input)
        {
            $input['from'] = date('d-m-Y', strtotime($input['from']));
            $input['to'] = date('d-m-Y', strtotime($input['to']));
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id');
            if ($input['class'] != 0)
            {
                $getStudent = $getStudent->where('student.class_id', $input['class']);
            }
            $getStudent = $getStudent->select('student.id', 'student.name', 'parent.name as father', 'student.roll_no', 'student.registration_no', 'student.class_id')->get();
            $totalPanding = 0;
            $totalRecive = 0;
            $totalMonthly = 0;
            $totalAnnual = 0;
            $totalonetimeFee = 0;//updated 20-4-2018
            $totalPrevious = 0;//updated 20-4-2018
            foreach ($getStudent as $student)
            {
                /*
                 * updated 20-4-2018
                 *
                 * $getFee = \DB::table('fee_structures')
                   ->where('school_id', $this->user->school_id)
                   ->where('class_id', $student->class_id)
                   ->get();
                *
                *
                */
                $checkfeeExist =\DB::table('fee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session','=',$this->active_session->session)//20-4-2018
                    ->where('class_id',$student->class_id)
                    ->where('student_id',$student->id)->first();
                if($checkfeeExist)
                {
                    $getFee = \DB::table('fee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session','=',$this->active_session->session)//20-4-2018
                        ->where('class_id',$student->class_id)
                        ->whereIn('student_id',array('0',$student->id))->get();
                }
                else
                {
                    $getFee = \DB::table('fee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session','=',$this->active_session->session)//20-4-2018
                        ->where('class_id',$student->class_id)
                        ->where('student_id','=','0')->get();
                }
                $annualFee = 0;
                $monthFee = 0;
                $onetimeFee=0;
                $previous = 0; //updated 20-4-2018
                $totallatefee = 0; //updated 20-4-2018
                $totalconcession = 0; //updated 20-4-2018

                foreach ($getFee as $fee)
                {
                    if ($fee->payment_type == 'ANNUAL')
                    {
                        $annualFee = $annualFee + $fee->amount;
                    }
                    else if($fee->payment_type == 'ONE TIME')
                    {
                        $onetimeFee = $onetimeFee +  $fee->amount;
                    }
                    else if($fee->payment_type == 'PREVIOUS YEAR PAYMEN')//updated 20-4-2018
                    {
                        $previous = $previous +  $fee->amount;
                    }
                    else
                    {
                        $fee->amount =$fee->amount * 12;
                        $monthFee = $monthFee +  $fee->amount;
                    }
                }
                $student->totalAnnualFee = $annualFee;
                $student->onetimeFee = $onetimeFee;
                $student->totalMonthly = $monthFee;

                $student->totalPreviousPayment = $previous;//updated 20-4-2018

                $input['from'] = date('Y-m-d', strtotime($input['from']));
                $input['to'] = date('Y-m-d', strtotime($input['to']));
                $history = \DB::table('payment')->where('student_id', $student->id)
                    ->where('session_id','=',$this->active_session->id)//updated 20-4-2018
                    ->whereBetween('date', array($input['from'], $input['to']))
                    ->get();

                $totalPay = 0;
                $concession = 0;
                $latefee=0;
                foreach ($history as $hi)
                {
                    $student->totalPay += $hi->amount;
                    $student->concession += $hi->concession;
                    $student->late_fee += $hi->late_fee;
                }
               // $student->totalBalance = ((($annualFee + $onetimeFee + $monthFee) - $student->totalPay )+$student->late_fee)-$student->concession;
                $student->totalBalance = ((($annualFee + $onetimeFee + $monthFee + $previous) - $student->totalPay )+$student->late_fee)-$student->concession;
                $totalPanding = $totalPanding + $student->totalBalance;
                $totalRecive = $totalRecive + $student->totalPay;
                $totalMonthly += $monthFee;
                $totalAnnual += $annualFee;
                $totallatefee += $student->late_fee;
                $totalconcession += $student->concession;
                $totalonetimeFee += $onetimeFee;
                $totalPrevious += $previous;//updated 20-4-2018
            }

            // dd($getStudent);
            \Excel::create("collection-" . $input['from'] . '_' . $input['to'], function($excel) use ($getStudent,$totalPrevious,$totalonetimeFee, $totalPanding, $totalRecive, $totalMonthly, $totalAnnual, $input) {

                $excel->sheet('Excel sheet', function($sheet) use ($getStudent,$totalPrevious,$totalonetimeFee, $totalPanding, $totalRecive, $totalMonthly, $totalAnnual, $input) {
                    $sheet->loadView('users.report.collectionExport')
                        ->with('getStudent', $getStudent)
                        ->with('totalPanding', $totalPanding)
                        ->with('totalRecive', $totalRecive)
                        ->with('totalMonthly', $totalMonthly)
                        ->with('totalAnnual', $totalAnnual)
                        ->with('input', $input)
                        ->with('totalPrevious', $totalPrevious)//updated 20-4-2018
                        ->with('totalonetimeFee', $totalonetimeFee);//updated 20-4-2018
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('report'));

            $fileURL = storage_path() . "/report/collection-" . $input['from'] . '_' . $input['to'] . '.xls';
            \Session::put('attendanceUrl', $fileURL);
        }
      //  return view('users.report.fee_collection', compact('totallatefee','totalconcession','getStudent', 'totalPanding','totalonetimeFee', 'totalRecive', 'totalMonthly', 'totalAnnual'));
        /*updated 20-4-2018*/
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id','=',$this->active_session->id)
            ->get();
        return view('users.report.fee_collection', compact('totallatefee','totalconcession','getStudent', 'totalPanding','totalonetimeFee', 'totalRecive', 'totalMonthly', 'totalAnnual','getSession','classes','totalPrevious'));
    }

public function selected_term_staffreport()
        {
            
            $staffid =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->groupBy('recived_by')->get();
            
             $termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->distinct()->get();

            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->get();
                    $staff_id=array();
                    $term_type=array();
            foreach ($termtype as $key => $value) {
                       
                $term_type[]=$value->payment_type;
            }   
            return view('users.report.sion.stafffee_collectionnew',compact('staffid','term_type','classes'));
        } 
    public function individual_balanceReportadmin()
        {
            
          
             $termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->distinct()->get();

            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->get();
                    $staff_id=array();
                    $term_type=array();
            foreach ($termtype as $key => $value) {
                       
                $term_type[]=$value->payment_type;
            }   
            //dd('hi');
            return view('users.report.sion.stafffee_balancenew',compact('term_type','classes'));
        } 

        public function individual_balanceReportbusfee()
        {
            

            $routes = \DB::table('boarding')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->groupBy('route')->orderby('bus_no', 'asc')
                    ->get();
                    //dd($routes);
                $bus_no=array();
                $bus_route=array();
            foreach ($routes as $key => $value) {
               $bus_no[] =$value->bus_no;
               $bus_route[] = $value->route;
            }
            return view('users.report.sion.stafffee_busreportnew',compact('routes','bus_no','bus_route'));
        } 
         public function school_feeReport()
        {
            return view('users.report.sion.schoolFeereport');
        }
        public function school_Received_Report() 
     {
         $staffid =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->groupBy('recived_by')->get();
            
             $termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->distinct()->get();
                    $term_type=array();
            foreach ($termtype as $key => $value) {
                $term_type[]=$value->payment_type;
            }   
            return view('users.report.sion.schoolreceivedreport',compact('staffid','term_type'));
     }

     public function school_Received_Reportdetails()
        {
            $input = \Request::all();
            $selected_staff=$input['staff'];
            $selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];

            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();

        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
            if($input['termtyp']!='0')
            {
                $paidstudents =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                   ->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))->get();
                
            }
            else{
                $paidstudents =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                  //->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))->get();
                
            }

             //dd('paidstudents',$paidstudents,'selected_term',$selected_term);
                    $feeId= array();
                    $student_id= array();
                    $termType= array();
                    $feeName= array();
                    $amount= array();
            foreach ($paidstudents as $stud ) {
                   $feeId[]=$stud->id;
                   $student_Id[]=$stud->student_id;
                   $termType[]=$stud->payment_type;
                   $feeName[]=$stud->fee_name;
                   $amt[]=$stud->amount;
            }
            
            $collection = collect($student_Id);
            $unique_student_id = $collection->unique()->values()->all(); 

        if ($unique_student_id) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));
            $getstudent=array();
            foreach ($unique_student_id as $stu_id ) {
                $getStudent[] = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->where('student.id',$stu_id)
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->select('student.id','student.name','student.roll_no','student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->get();
                //->get();
            }
        }


//for school fee report

          if($selected_staff !='0' && $input['termtyp'] != '0' )
          {
            foreach ($getStudent as $level1 ) {
            foreach ($level1 as $student ) {
                $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('payment_type',$input['termtyp'])
                    //->where('payment_type','=',null)
                     ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->where('class',$student->class)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('class',$student->class)
                        ->where('student_id','=','0')->get();
                    }

                    $amount=0;
                foreach ($getFee as $amt ) {
                   
                        $board=$amt->boarding;
                        $amount+=$amt->amount;
                }
               /* if($amount!='0'&& $board != null)
                {
                    $board=$amt->boarding;
                }
                else{
                    $board=0; 
                }*/
            $student->getstuFee=$amount;
            $student->boarding=$board;

                    $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','!=', "")
                   // ->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))->sum('amount');
                    //->whereBetween('date', array($input['from'], $input['to']))->get();
                
                   
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                     
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }
                $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                $student->balancAmt=$balancAmt; 
                $total_balancAmt += $balancAmt;
                $total_paidAmt +=$paidAmts;
                $total_studentAmt +=$amount;

            }
        }

    }
    else
    {
        foreach ($getStudent as $level1 ) {
            foreach ($level1 as $student ) {
                $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('bal_status','=', null)
                        ->where('bus_route','=',null)
                        ->where('class',$student->class)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('class',$student->class)
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                    }

                    
                    $amount=0;
                foreach ($getFee as $amt ) {
                   
                        $board=$amt->boarding;
                        $amount+=$amt->amount;
                }
                if($amount!='0'&& $board != null)
                {
                    $board=$amt->boarding;
                }
                else{
                        $board="Local Student"; 
                    }
                $student->getstuFee=$amount;
                $student->boarding=$board;
                $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','!=', "")
                    ->whereBetween('date', array($input['from'], $input['to']))->sum('amount');
                   
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                     
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     $total_paidAmt +=$paidAmts;
                     $total_studentAmt +=$amount;
                 }
             }
          }
//for bus fee report
                if($selected_staff !='0'  )
                 {
                    foreach ($getStudent as $level1 ) {
                    foreach ($level1 as $student ) {
                $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('payment_type','=',null)
                    ->where('bus_route','!=',null)
                    ->where('student_id',$student->id)->first();
               // dd('hi',$checkfeeExist);

                    if($checkfeeExist)
                    {
                    $getbusFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        //->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->where('payment_type','=',null)
                        ->where('bus_route','!=',null)
                        ->where('class',$student->class)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getbusFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                       // ->where('payment_type',$input['termtyp'])
                        ->where('payment_type','=',null)
                        ->where('bus_route','!=',null)
                        ->where('class',$student->class)
                        ->where('student_id','=','0')->get();
                    }
                    //dd('hi',$getFee);

                    $busamount=0;
                foreach ($getbusFee as $amt ) {
                   
                        $board=$amt->boarding;
                        $busamount+=$amt->amount;
                }
                if($busamount!='0'&& $board != null)
                {
                    $board=$amt->boarding;
                }
                else{
                    $board="Local Student"; 
                }
            $student->getstubusFee=$busamount;
            $student->boarding=$board;

                    $bus_paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','=', "")
                    ->whereBetween('date', array($input['from'], $input['to']))->sum('amount');
                    //->whereBetween('date', array($input['from'], $input['to']))->get();
                
                   //dd($bus_paidAmts);
                   if($bus_paidAmts!= null)
                   {
                     $student->paidstu_busAmount=$bus_paidAmts;
                     
                    }
                    else{
                     $student->paidstu_busAmount=0;
                    }
                $balancbus_Amt= $student->getstubusFee - $student->paidstu_busAmount;
                $student->balancbus_Amt=$balancbus_Amt; 
                $total_balancbusAmt += $balancbus_Amt;
                $total_buspaidAmt +=$bus_paidAmts;
                $total_busstudentAmt +=$busamount;

                //dd($student);

                     }
                 }

         }

        return view('users.report.sion.schoolfeerevd_detreport', compact('total_balancbusAmt','total_buspaidAmt','total_busstudentAmt','school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));

        }
        public function school_balance_Report()
        {
            
          
             $termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->distinct()->get();

            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->get();
                    $staff_id=array();
                    $term_type=array();
            foreach ($termtype as $key => $value) {
                       
                $term_type[]=$value->payment_type;
            }   
            //dd('hi');
            return view('users.report.sion.schoolFee_balanreport',compact('term_type','classes'));
        } 
        public function school_Balance_reportDetails()
        {
            //dd('hi');
            $input = \Request::all();
            $selected_staff=$input['staff'];
            $selected_class=$input['class'];
            $selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();


        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('class','=',$selected_class)
                    ->get();
            $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('class','=',$input['class'])
                    ->first();
                   // dd('class',$class->id);

        $final = []; 
        $total_balancAmt=0; 
        $total_paidAmt =0;
        $total_studentAmt =0;      
        if ($input) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));

            //dd('from',$input['from'],'from',$input['to']);
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id');
                
            
        }

            if($input['class'] != '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] == '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            else 
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.class_id','student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();
                    }
            foreach ($getStudent as $student ) {
                if($input['class'] != '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('payment_type',$input['termtyp'])
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getFee);
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->where('payment_type','!=', "")
                   // ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
                elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('bus_route','=',null)
                    //->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('bus_route','=',null)
                        //->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                    
                    
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','!=', "")
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
                elseif($input['class'] == '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('payment_type',$input['termtyp'])
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                       ->where('bal_status','=', null)
                       ->where('bus_route','=',null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->where('payment_type','!=', "")
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
               
                else
                {
//Fee Details
                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd('hi',$getFee);
                     $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    //dd('board',$board,'amount',$amount);
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                   $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','!=', "")
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                   
                    if($paidAmts!= null)
                     {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                     $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
    //bus fee details

            

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    //->where('class',$input['class'])
                    ->where('payment_type','=',null)
                    ->where('bus_route','!=',null)
                    ->where('student_id',$student->id)->first();
                   // dd($checkfeeExist);

                    if($checkfeeExist)
                    {
                    $getbusFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                       //->where('class',$input['class'])
                       // ->where('payment_type',$input['termtyp'])
                        ->where('payment_type','=',null)
                        ->where('bus_route','!=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getbusFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        //->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('payment_type','=',null)
                        ->where('bus_route','!=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getbusFee);
                    $busamount=0;
                    foreach ($getbusFee as $amt ) {
                    $board=$amt->boarding;
                    $busamount+=$amt->amount;
                   
                    }
                    if($busamount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstubusFee=$busamount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidbusAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    //->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','=',"")
                    //->where('payment_type',$input['termtyp'])
                   // ->whereBetween('date', array($input['from'], $input['to']))
                    //->groupBy('class')
                    ->sum('amount');
                    
                    

                   if($paidbusAmts!= null)
                   {
                     $student->paidstu_busAmount=$paidbusAmts;
                    }
                    else{
                     $student->paidstu_busAmount=0;
                    }
                    //dd($paidbusAmts);

 //Balance Details
                
                    $balancbusAmt= $student->getstubusFee - $student->paidstu_busAmount;
                    $student->balancbusAmt=$balancbusAmt; 
                    $total_balancbusAmt += $balancbusAmt;
                     //$total_paidAmt +=$paidAmts;
                    // if($balancbusAmt != '0')
                    // {
                     $total_studentbusAmt +=$busamount;
                     $total_buspaidAmt +=$paidbusAmts;
                        
                    // }
            }
            \Excel::create('students-' , function($excel) use ($getStudent) {
                $excel->sheet('Excel sheet', function($sheet) use ($getStudent) {
                    $sheet->setFontSize(12);
                    $sheet->setAllBorders('thin');
                    $sheet->loadView('users.report.sion.school_bus_bal_rep')->with('students', $getStudent);
                });
            })->store('xls', storage_path('students'));
            $fileURL = storage_path() . '/students/students-' . $session . '.xls';
            \Session::put('attendanceUrl', $fileURL); 
        return view('users.report.sion.schoolFeebalancerepdet', compact('total_buspaidAmt','total_studentbusAmt','total_balancbusAmt','school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));
        }
        
     public function school_Overall_Report()
        {
             $termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->distinct()->get();
            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->get();
                    $staff_id=array();
                    $term_type=array();
            foreach ($termtype as $key => $value) {
                $term_type[]=$value->payment_type;
            }   
            //dd('hi');
            return view('users.report.sion.schoolFee_overallreport',compact('term_type','classes'));
        } 
        public function school_Overall_reportDetails()
        {
            //dd('hi');
            $input = \Request::all();
            $selected_staff=$input['staff'];
            $selected_class=$input['class'];
            $selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();


        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('class','=',$selected_class)
                    ->get();
            $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('class','=',$input['class'])
                    ->first();
                   // dd('class',$class->id);

        $final = []; 
        $total_balancAmt=0; 
        $total_paidAmt =0;
        $total_studentAmt =0;      
        if ($input) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));

            //dd('from',$input['from'],'from',$input['to']);
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id');
                
            
        }

            if($input['class'] != '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] == '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            else 
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.class_id','student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();
                    }
            foreach ($getStudent as $student ) {
                if($input['class'] != '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('payment_type',$input['termtyp'])
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getFee);
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->where('payment_type','!=', "")
                   // ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
                elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('bus_route','=',null)
                    //->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('bus_route','=',null)
                        //->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                    
                    
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','!=', "")
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
                elseif($input['class'] == '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('payment_type',$input['termtyp'])
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                       ->where('bal_status','=', null)
                       ->where('bus_route','=',null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->where('payment_type','!=', "")
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
               
                else
                {
//Fee Details
                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('bus_route','=',null)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('bus_route','=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('bus_route','=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd('hi',$getFee);
                     $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    //dd('board',$board,'amount',$amount);
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                   $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','!=', "")
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                   
                    if($paidAmts!= null)
                     {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                     $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
    //bus fee details

            

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    //->where('class',$input['class'])
                    ->where('payment_type','=',null)
                    ->where('bus_route','!=',null)
                    ->where('student_id',$student->id)->first();
                   // dd($checkfeeExist);

                    if($checkfeeExist)
                    {
                    $getbusFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                       //->where('class',$input['class'])
                       // ->where('payment_type',$input['termtyp'])
                        ->where('payment_type','=',null)
                        ->where('bus_route','!=',null)
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getbusFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        //->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('payment_type','=',null)
                        ->where('bus_route','!=',null)
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getbusFee);
                    $busamount=0;
                    foreach ($getbusFee as $amt ) {
                    $board=$amt->boarding;
                    $busamount+=$amt->amount;
                   
                    }
                    if($busamount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board="Local Student"; 
                    }
                    $student->getstubusFee=$busamount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidbusAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    //->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type','=',"")
                    //->where('payment_type',$input['termtyp'])
                   // ->whereBetween('date', array($input['from'], $input['to']))
                    //->groupBy('class')
                    ->sum('amount');
                    
                    

                   if($paidbusAmts!= null)
                   {
                     $student->paidstu_busAmount=$paidbusAmts;
                    }
                    else{
                     $student->paidstu_busAmount=0;
                    }
                    //dd($paidbusAmts);

 //Balance Details
                
                    $balancbusAmt= $student->getstubusFee - $student->paidstu_busAmount;
                    $student->balancbusAmt=$balancbusAmt; 
                    $total_balancbusAmt += $balancbusAmt;
                     //$total_paidAmt +=$paidAmts;
                    // if($balancbusAmt != '0')
                    // {
                     $total_studentbusAmt +=$busamount;
                     $total_buspaidAmt +=$paidbusAmts;
                        
                    // }
            }

                
            //dd('hi',$getStudent);
             
        return view('users.report.sion.school_Overall_reportDetails', compact('total_buspaidAmt','total_studentbusAmt','total_balancbusAmt','school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));
        }
        public function school_Datewise_Report()
        {
             //$termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    //->where('session_id','=',$this->active_session->id)->distinct()->get();
            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->get();
                    //$staff_id=array();
                    //$term_type=array();
            //foreach ($termtype as $key => $value) {
                //$term_type[]=$value->payment_type;
            //}   
            //dd('hi');
            return view('users.report.sion.schoolFee_Datewisereport',compact('classes'));
        } 
    public function school_Datewise_reportDetails()
        {
            //dd('hi');
            $input = \Request::all();
            //$selected_staff=$input['staff'];
            //$selected_class=$input['class'];
            //$selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();


        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        

        $final = []; 
        $total_balancAmt=0; 
        $total_paidAmt =0;
        $total_studentAmt =0;      
        if ($input) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));

        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->whereBetween('date', array($input['from'], $input['to']))
                   //->groupBy('invoice_id')
                    ->get();

        foreach ($paidAmts as $key => $value) {
           $total_paidAmt+=$value->amount;
        }
            //dd($paidAmts,$total_paidAmt);
        }
        return view('users.report.sion.school_Datewise_reportDetails', compact('paidAmts','total_paidAmt','input','school'));
        
        //return view('users.report.sion.school_Overall_reportDetails', compact('total_buspaidAmt','total_studentbusAmt','total_balancbusAmt','school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));
        }

        public function individual_balanceReportbusfeedetails()
        {
            $input = \Request::all();
            
            $selected_route=$input['route'];
            $selected_busNo=$input['busno'];
            
            $selected_from=$input['from'];
            $selected_to=$input['to'];
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();

            
        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('class','=',$selected_class)
                    ->get();
            $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('class','=',$input['class'])
                    ->first();

                    if($input['route'] != '0' && $input['busno'] != '0'){
                         $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('bus_route',$input['route'])
                        ->where('bus_no',$input['busno'])->get();

                            foreach ($getFee as $key => $value) {
                               $studentid[]=$value->student_id;
                            }

                    }else{
                        $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('bus_route','!=', null)
                        ->where('bus_no','!=', null)
                        ->get();

                            foreach ($getFee as $key => $value) {
                               $studentid[]=$value->student_id;
                               
                            }

                    }
        $final = []; 
        $total_balancAmt=0; 
        $total_paidAmt =0;
        $total_studentbusAmt =0; 
            foreach ($getFee as $student ) {
                if($input['route'] != '0' && $input['busno'] != '0' )
                {
                $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)
                ->where('id',$student->student_id)->select('name','registration_no','class_id','section_id')
                ->get();
                
               // dd($getStudent);
                foreach ($getStudent as $stu ) {
                    $student->name=$stu->name;
                    $student->registration_no=$stu->registration_no;
                }
                $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('id','=',$stu->class_id)->select('class')
                    ->first();
                    //dd($class);
                    $student->class=$class->class;
                    $amount=0;
                    $board=$student->boarding;
                    $amount+=$student->amount;
                    if($amount!='0'&& $board != null)
                    {
                        $board=$student->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boardings=$board;
        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('fee_id',$student->id)
                    ->sum('amount');
                    
                //dd($paidAmts);
                    //dd($paidAmts);
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

                   

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     
                     $total_studentbusAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                }
                    else
                    {
                        //dd('all');
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)
                ->where('id',$student->student_id)->select('name','registration_no','class_id','section_id')
                ->get();
                
                //dd($getStudent);
                foreach ($getStudent as $stu ) {
                    $student->name=$stu->name;
                    $student->registration_no=$stu->registration_no;
                }
                $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('id','=',$stu->class_id)->select('class')
                    ->first();
                    //dd($class);
                    $student->class=$class->class;
                    $amount=0;
                   // foreach ($getFee as $amt ) {
                    $board=$student->boarding;
                    $amount+=$student->amount;
                   
                   // }
                    //dd($board,$amount);
                    if($amount!='0'&& $board != null)
                    {
                        $board=$student->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boardings=$board;
                    
//paid details
                       
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('fee_id',$student->id)
                    ->sum('amount');
                    
                    
                    //dd($paidAmts);
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

                   

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     
                     $total_studentbusAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                 }
                 
            }
           
        return view('users.report.sion.fee_busOveralreport', compact('getFee','student','school','total_paidAmt','total_studentbusAmt','total_balancAmt','getStudent','selected_route','selected_busNo','selected_from','selected_to'));
        }

         public function selected_term_staffreportdetails()
        {
            //dd('hi');
            $input = \Request::all();
            $selected_staff=$input['staff'];
            $selected_class=$input['class'];
            $selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();

        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('class','=',$selected_class)
                    ->get();
            $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('class','=',$input['class'])
                    ->first();
                   // dd('class',$class->id);

        $final = []; 
        $total_balancAmt=0; 
        $total_paidAmt =0;
        $total_studentAmt =0;      
        if ($input) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));

            //dd('from',$input['from'],'from',$input['to']);
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id');
                
            
        }

            if($input['class'] != '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] == '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            else 
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.class_id','student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();
                    }
            foreach ($getStudent as $student ) {
                if($input['class'] != '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getFee);
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     $total_paidAmt +=$paidAmts;
                     $total_studentAmt +=$amount;
                }
                elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    //->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                    
                    
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     $total_paidAmt +=$paidAmts;
                     $total_studentAmt +=$amount;
                }
                elseif($input['class'] == '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     $total_paidAmt +=$paidAmts;
                     $total_studentAmt +=$amount;
                }
               
                else
                {
//Fee Details
                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd('hi',$getFee);
                     $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    //dd('board',$board,'amount',$amount);
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                   $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                   
                    if($paidAmts!= null)
                     {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                     $total_balancAmt += $balancAmt;
                     $total_paidAmt +=$paidAmts;
                     $total_studentAmt +=$amount;
                }
            }
             
        return view('users.report.sion.fee_collection1234', compact('school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));
        }
 public function individual_balanceReportadmindetails()
        {
            //dd('hi');
            $input = \Request::all();
            $selected_staff=$input['staff'];
            $selected_class=$input['class'];
            $selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();


        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('class','=',$selected_class)
                    ->get();
            $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('class','=',$input['class'])
                    ->first();
                   // dd('class',$class->id);

        $final = []; 
        $total_balancAmt=0; 
        $total_paidAmt =0;
        $total_studentAmt =0;      
        if ($input) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));

            //dd('from',$input['from'],'from',$input['to']);
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id');
                
            
        }

            if($input['class'] != '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] == '0' && $input['termtyp'] != '0')
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {
                    $getStudent = $getStudent->where('student.class_id', $class->id)->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();;
                    //dd('getStudent',$getStudent);
                    }
            else 
                    {
                    $getStudent = $getStudent->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'student.gender' ,'student.class_id','student.section_id','parent.mobile','class.class','section.section')->orderBy('class', 'asc')->get();
                    }
            foreach ($getStudent as $student ) {
                if($input['class'] != '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    ->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        ->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd($getFee);
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                   // ->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
                elseif($input['class'] != '0' && $input['termtyp'] == '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$input['class'])
                    //->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$input['class'])
                        //->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                    
                    
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$input['class'])
                    ->where('student_id','=',$student->id)
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    

                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
                elseif($input['class'] == '0' && $input['termtyp'] != '0')
                {

                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        ->where('payment_type',$input['termtyp'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                        $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                    
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
               
                else
                {
//Fee Details
                    $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class',$student->class)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('bal_status','=', null)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class',$student->class)
                        //->where('payment_type',$input['term'])
                        ->where('student_id','=','0')->get();
                       //dd('all',$getFee);
                    }
                    //dd('hi',$getFee);
                     $amount=0;
                    foreach ($getFee as $amt ) {
                    $board=$amt->boarding;
                    $amount+=$amt->amount;
                   
                    }
                    //dd('board',$board,'amount',$amount);
                    if($amount!='0'&& $board != null)
                    {
                        $board=$amt->boarding;
                    }
                    else{
                       $board=0; 
                    }
                    $student->getstuFee=$amount;
                    $student->boarding=$board;

//paid details
                       $stu_id[]=$student->id;
                   
                   $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    //->where('recived_by','=',$selected_staff)
                    ->where('class',$student->class)
                    ->where('student_id','=',$student->id)
                    //->whereBetween('date', array($input['from'], $input['to']))
                    ->groupBy('class')->sum('amount');
                   
                    if($paidAmts!= null)
                     {
                     $student->paidstu_Amount=$paidAmts;
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                     $total_balancAmt += $balancAmt;
                     //$total_paidAmt +=$paidAmts;
                     if($balancAmt != '0')
                     {
                     $total_studentAmt +=$amount;
                     $total_paidAmt +=$paidAmts;
                        
                     }
                }
            }
            //dd('hi');
             
        return view('users.report.sion.fee_balance1234', compact('school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));
        }


        public function individual_collectionReportadmin() 
     {
         $staffid =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->groupBy('recived_by')->get();
            
             $termtype =\DB::table('sionfee_collection')->select('payment_type')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)->distinct()->get();
                    $term_type=array();
            foreach ($termtype as $key => $value) {
                $term_type[]=$value->payment_type;
            }   
            return view('users.report.sion.individual_collectionnewadmin',compact('staffid','term_type'));
     }
     
     public function individual_collection()
        {
            $input = \Request::all();
            $selected_staff=$input['staff'];
            $selected_term=$input['termtyp'];
            $selected_from=$input['from'];
            $selected_to=$input['to'];

            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();

        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
            if($input['termtyp']!='0')
            {
                $paidstudents =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                   ->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))->get();
            }
            else{
                 $paidstudents =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                  //->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))->get();
            }

             //dd('paidstudents',$paidstudents,'selected_term',$selected_term);
                    $feeId= array();
                    $student_id= array();
                    $termType= array();
                    $feeName= array();
                    $amount= array();
            foreach ($paidstudents as $stud ) {
                   $feeId[]=$stud->id;
                   $student_Id[]=$stud->student_id;
                   $termType[]=$stud->payment_type;
                   $feeName[]=$stud->fee_name;
                   $amt[]=$stud->amount;
            }
            
            $collection = collect($student_Id);
            $unique_student_id = $collection->unique()->values()->all(); 

        if ($unique_student_id) 
        {
            $input['from'] = date('Y-m-d', strtotime($input['from']));
            $input['to'] = date('Y-m-d', strtotime($input['to']));
            $getstudent=array();
            foreach ($unique_student_id as $stu_id ) {
                $getStudent[] = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->where('student.id',$stu_id)
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->select('student.id','student.name','student.roll_no','student.registration_no', 'student.gender' ,'student.section_id','parent.mobile','class.class','section.section')->get();
                //->get();
            }
        }
        else{
            $msg['success'] = 'Success to Submit Question';
            
        return \Redirect::back()->withInput($msg);
         
        }

          if($selected_staff !='0' && $input['termtyp'] != '0' )
          {
            foreach ($getStudent as $level1 ) {
            foreach ($level1 as $student ) {
                $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('payment_type',$input['termtyp'])
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('payment_type',$input['termtyp'])
                        ->where('bal_status','=', null)
                        ->where('class',$student->class)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('payment_type',$input['termtyp'])
                        ->where('class',$student->class)
                        ->where('student_id','=','0')->get();
                    }

                    $amount=0;
                foreach ($getFee as $amt ) {
                   
                        $board=$amt->boarding;
                        $amount+=$amt->amount;
                }
                if($amount!='0'&& $board != null)
                {
                    $board=$amt->boarding;
                }
                else{
                    $board=0; 
                }
            $student->getstuFee=$amount;
            $student->boarding=$board;

                    $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                    ->where('student_id','=',$student->id)
                    ->where('payment_type',$input['termtyp'])
                    ->whereBetween('date', array($input['from'], $input['to']))->sum('amount');
                   
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                     
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }
                $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                $student->balancAmt=$balancAmt; 
                $total_balancAmt += $balancAmt;
                $total_paidAmt +=$paidAmts;
                $total_studentAmt +=$amount;

            }
        }

    }
    else
    {
        foreach ($getStudent as $level1 ) {
            foreach ($level1 as $student ) {
                $checkfeeExist =\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('student_id',$student->id)->first();

                    if($checkfeeExist)
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('bal_status','=', null)
                        ->where('class',$student->class)
                        ->whereIn('student_id',array('0',$student->id))->get();
                    }
                    else
                    {
                    $getFee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)
                        ->where('class',$student->class)
                        ->where('student_id','=','0')->get();
                    }

                    
                    $amount=0;
                foreach ($getFee as $amt ) {
                   
                        $board=$amt->boarding;
                        $amount+=$amt->amount;
                }
                if($amount!='0'&& $board != null)
                {
                    $board=$amt->boarding;
                }
                else{
                        $board=0; 
                    }
                $student->getstuFee=$amount;
                $student->boarding=$board;
                $paidAmts =\DB::table('sionfee_collection')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('recived_by','=',$selected_staff)
                    ->where('student_id','=',$student->id)
                    ->whereBetween('date', array($input['from'], $input['to']))->sum('amount');
                   
                   if($paidAmts!= null)
                   {
                     $student->paidstu_Amount=$paidAmts;
                     
                    }
                    else{
                     $student->paidstu_Amount=0;
                    }

 //Balance Details
                    $balancAmt= $student->getstuFee - $student->paidstu_Amount;
                    $student->balancAmt=$balancAmt; 
                    $total_balancAmt += $balancAmt;
                     $total_paidAmt +=$paidAmts;
                     $total_studentAmt +=$amount;
                 }
             }
          }
        return view('users.report.sion.individualfee_collection', compact('school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));

        }
    public function postKnowledge($id) {
        $input = \Request::all();
        $question = \DB::table('questions')->insertGetId(['class_id' => $id, 'school_id' => \Auth::user()->school_id, 'question' => $input['question']]);
        foreach ($input['option'] as $key => $opt) {
            if ($key == $input['correct']) {
                $curr = 1;
            } else {
                $curr = 0;
            }
            \DB::table('options')->insert([
                'question_id' => $question,
                'option' => $opt,
                'correct' => $curr
            ]);
        }
        $msg['success'] = 'Success to Submit Question';
        return \Redirect::back()->withInput($msg);
    }

    public function viewKnowledge() {
        $class_id = \Request::get('class');
        if($class_id){
            $questions = \DB::table('questions')
                ->where('school_id', \Auth::user()->school_id)
                ->where('class_id',$class_id)
                ->get();
            foreach ($questions as $question) {
                $option = \DB::table('options')->where('question_id', $question->id)->get();
                $question->option = $option;
            }
            return view('users.knowledge.view', compact('questions',$class_id));
        }
        else{
            $class_id='';
            return view('users.knowledge.view');
        }
        //$class_id = \Request::get('class');

    }

    public function deleteQuestion($id) {
        \DB::table('options')->where('question_id', $id)->delete();
        \DB::table('questions')->where('id', $id)->delete();
        $msg['success'] = 'Success to Submit Question';
        return \Redirect::back()->withInput($msg);
    }

    // updated 10-11-2017 by priya

    /** @  Add Time Table From Excel Sheet  @  **/
    public function postTimeTableExcelSheet(TimeTable $timetable)
    {
        $input = \Request::all();
        // return $input;
        $userError = ['excel_timetable' => 'Time Table'];
        $validator = \Validator::make($input, [
            'excel_timetable' => 'required|mimes:xls,xlsx'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            //return 'success';
           // $rows = Excel::load($input['excel_timetable'])->get();
            //return $timetable->doImportTimeTable($this->user, $input,$rows);
            return $timetable->doImportTimeTable($this->user, $input);

        }
        return \Redirect::back()->withInput($input);
    }

    // updated 13-11-2017 by priya

    /** @ View Edit Time Table Page @  **/
    public function editTimetableDetail($id)
    {
        $getTimeTableDetail = \DB::table('time-table')
             ->where('time-table.session_id',$this->active_session->id)//updated 14-4-2018
            ->where('time-table.school_id',\Auth::user()->school_id)
            ->where('time-table.id',$id)
            ->leftJoin('section','section.id','=','time-table.section_id')
            ->leftJoin('subject','subject.id','=','time-table.subject_id')
            ->select('time-table.*','section.section','subject.subject','section.subjects')
            ->first();
       // var_dump($getTimeTableDetail->subjects);
        $getSubjectName = \DB::table('subject')->whereIn('id', json_decode($getTimeTableDetail->subjects))->get();

        $classes = addClass::where('school_id', $this->user->school_id)
         ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        $teaching = \DB::table('staff')->where('school_id', $this->user->school_id)
            ->where('staff_type', 'Teaching Staff')->first();
        $teachers = \DB::table('teacher')->where('school_id', $this->user->school_id)
            ->where('type', $teaching->id)->get();

        return view('users.time_table.edit_time_table',compact('getTimeTableDetail','classes','teachers','getSubjectName'));
    }

    /** @  Update Time Table @ **/
    public function updateTimetableDetail()
    {
        $input = \Request::all();
        //return $input;
        $userError = ['teacher' => 'Teacher',
            'subject' => 'Subject'
            ];
        $validator = \Validator::make($input, [
            'teacher' => 'required',
            'subject' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            $checkTimeTable = \DB::table('time-table')
                 ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id',\Auth::user()->school_id)
                ->where('class_id',$input['class_id'])
                ->where('section_id',$input['section_id'])
               // ->where('subject_id',$input['subject_id'])
                ->where('day',$input['day'])
                ->where('period',$input['period'])
                ->first();
            //return $checkTimeTable;
            if(!$checkTimeTable)
            {
               TimeTable::where('id', $input['timeTable_id'])
                   ->update([
                    'teacher_id' => $input['teacher'],
                    'subject_id' => $input['subject']
                ]);
                $input['success'] = 'Time Table updated succesfully';
                return \Redirect::back()->withInput($input);
            }
            else
            {
                $input['error'] = 'Time Table Already Exist !!! ';
                return \Redirect::back()->withInput($input);
            }
        }
    }



    /*****************************************************************************
    *                              SYLLABUS MODULE
    *****************************************************************************/

    /** @ View Syllabus Index (add Syllabus) @ **/
    public function viewSyllabusIndex()
    {
        $class_id = \Request::get('class');
        return view('users.master.syllabus.index',compact('class_id'));
    }

    /** @ Get Subjects for same class @ **/
    public function getSyllabusSubjects()
    {
        $class_id = \Request::get('srclass');
        $currentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        $getSection = \DB::table('section')->where('school_id',\Auth::user()->school_id)
            ->where('class_id',$class_id)->get();
        $subject =array();
        $classSubjects = array();
        foreach($getSection as $section)
        {
           $subject[] = json_decode($section->subjects);
        }
        foreach($subject as $key => $sub1)
        {
            foreach($sub1 as $value => $sub2)
            {
                $classSubjects[] = $sub2;
            }
        }
        $filtered_array = array_unique($classSubjects);
       // dd($filtered_array);
        $sectionSubjects = Subject::where('school_id',\Auth::user()->school_id)
            ->whereIn('id',$filtered_array)
            ->select('id','subject')
            ->get();
      //  dd($sectionSubjects);
        return $sectionSubjects;
    }

    /** @ Post Syllabus for a subject @ **/
    public function postSyllabus()
    {
        $input = \Request::all();
        if(isset($input['submit_syllabus']))
        {
            $userError = ['class' => 'Class', 'subject' => 'Subject', 'syllabus' => 'Syllabus '];
            $validator = \Validator::make($input, [
                'class' => 'required',
                'subject' => 'required',
                'syllabus' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator);
            }
            else
            {
                $currentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
                    ->where('active',1)->first();
                $check =  \DB::table('syllabi')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$currentSession->id)
                    ->where('class_id',$input['class'])
                    ->where('subject_id',$input['subject'])
                    ->first();
                if(!$check)
                {
                    $syllabus = \DB::table('syllabi')->insert([
                        'class_id' => $input['class'],
                        'subject_id' => $input['subject'],
                        'session_id' => $currentSession->id,
                        'school_id' => \Auth::user()->school_id,
                        'syllabi' => $input['syllabus']
                    ]);
                    if($syllabus)
                    {
                        $msg['success'] = 'Success to Submit Syllabus !!! ';
                    }
                    else
                    {
                        $msg['error'] = ' Error in Submitting Syllabus !!! ';
                    }
                }
                else
                {
                    $msg['error'] = ' Already Syllabus for this subject..';
                }
                return \Redirect::back()->withInput($msg);
            }
        }
    }

    /** @ View Syllabus List @ **/
    public function viewSyllabusList()
    {
        $currentSession = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', 1)->first();

            $allSyllabus =  \DB::table('syllabi')
                ->where('syllabi.school_id',\Auth::user()->school_id)
                ->where('syllabi.session_id',$currentSession->id)
                ->leftJoin('class','class.id','=','syllabi.class_id')
                ->leftJoin('subject','subject.id','=','syllabi.subject_id')
                ->select('syllabi.id','syllabi.syllabi','subject.subject','class.class')
                ->get();
        return view('users.master.syllabus.view',compact('class_id','syllabus','getSubjectName','getClassName','allSyllabus'));
    }

    /** @ Get Syllabus Detail for a class & subject @ **/
    public function getSyllabusDetail()
    {
        $class_id = \Request::get('srclass');
        $input = \Request::all();
        $currentSession = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', 1)->first();
        if(isset($input['view_syllabus']))
        {
            $userError = ['class' => 'Class', 'subject' => 'Subject'];
            $validator = \Validator::make($input, [
                'class' => 'required',
                'subject' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator);
            }
            else
            {
                $syllabus =  \DB::table('syllabi')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$currentSession->id)
                    ->where('class_id',$input['class'])
                    ->where('subject_id',$input['subject'])
                    ->first();
                $getClassName = \DB::table('class')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('id',$input['class'])->first();
                $getSubjectName = \DB::table('subject')->where('school_id',\Auth::user()->school_id)
                    ->where('id',$input['subject'])->first();
            }
        }
        return view('users.master.syllabus.viewSyllabus',compact('class_id','syllabus','getSubjectName','getClassName'));
    }

    /** @ View Edit Syllabus Page  for  a subject @ **/
    public function editSyllabusId($id)
    {
        $currentSession = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', 1)->first();
        $getSyllabus = \DB::table('syllabi')
            ->where('syllabi.school_id',\Auth::user()->school_id)
            ->where('syllabi.id',$id)
            ->where('syllabi.session_id',$currentSession->id)
            ->leftJoin('class','class.id','=','syllabi.class_id')
            ->leftJoin('subject','subject.id','=','syllabi.subject_id')
            ->select('syllabi.id','syllabi.syllabi','syllabi.class_id','syllabi.subject_id','subject.subject','class.class')
            ->first();
        return view('users.master.syllabus.editSyllabus',compact('getSyllabus'));
    }

    /** @ Update Syllabus for a subject @ **/
    public function updateSyllabusId()
    {
        $input = \Request::all();
        $currentSession = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', 1)->first();
        if(isset($input['edit_syllabus']))
        {
            $userError = ['syllabus' => 'Syllabus'];
            $validator = \Validator::make($input, [
                'syllabus' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator);
            }
            else
            {
                $syllabusUpdate =  \DB::table('syllabi')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$currentSession->id)
                    ->where('id',$input['syllabus_id'])
                    ->update([
                        'syllabi' => $input['syllabus']
                    ]);
                if($syllabusUpdate)
                {
                    $msg['success'] = '  Syllabus updated Successfully !!! ';
                }
                else
                {
                    $msg['error'] = '   Do any Modification to update !!! ';
                }
            }
        }
        return \Redirect::back()->withInput($msg);
    }

    /** @ Delete Syllabus List @ **/
    public function deleteSyllabusId($id)
    {
        \DB::table('syllabi')->where('id', $id)->delete();
        $msg['success'] = '  Syllabus Deleted Successfully !!!';
        return \Redirect::back()->withInput($msg);
    }


    /*****************************************************************************
     *                              TRANING MATERIAL MODULE
     *****************************************************************************/

    public function getTraningMaterial()
    {
        $destinationPath = base_path() . '/public/training_material/';
        if(!file_exists($destinationPath)){
            File::makeDirectory($destinationPath);
        }
        return view('users.traning_material.index');
    }
        public function sendsms()
    {
             $class=DB::table('class')
             ->where('class.session_id',$this->active_session->id)//updated 14-4-2018
             ->where('class.school_id',\Auth::user()->school_id)
              ->join('section', 'class.id', '=', 'section.class_id')
             ->select('class.class','section.section','class.id as classid',
                'section.id as sectionid')->get();
             //dd($class);
             $classname=array_unique(array_column($class, 'class'));
             
               $array=array();
             foreach ($classname as $key => $value) {
                
                foreach ($class as $classkey => $classvalue) {
                    
                        if($class[$classkey]->class==$value)                   
                        {
                         
                            $array[$value][]=$classvalue;
                         
                        }
                    }    
             }
             
           return view('users.notification.sendsms',compact('array'));
    }
    public function sendsmsclass(Request $request)
    {
        $input=$request->all();
        
        if($input['smstype']=='Allsms')
        {
              $smsusername=DB::table('smsusers')
                    ->where('school_id',\Auth::user()->school_id)
                    ->select('username','password','type','smssource')
                    ->first();
                    


                if(!empty($smsusername))
                {
                    //dd($smsusername);   
                    $input['smsusername']=$smsusername->username;
                    $input['smsuserpassword']=$smsusername->password;
                    $input['smsusertype']=$smsusername->type;
                    $input['smssource']=$smsusername->smssource;
                    
                    Event::fire(new SendSmsNotification($input));
                    $msg['success'] = 'Message Send Successfully';
                            return \Redirect::back()->withInput($msg);      
                 }else{
                    $msg['error'] = 'School Sms Username Not Found';
                return \Redirect::back()->withInput($msg);

                 }   
          
        }
        else
        {
            //dd($input);
            if(!empty($input['classname'])){
            
                foreach ($input['section'] as $sectionkey => $sectionvalue) {                    
                    if(!in_array($sectionkey, $input['classname']))
                    {
                        unset($input['section'][$sectionkey]);
                    }
                }
                $input['classid']=DB::table('class')
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id',\Auth::user()->school_id)
                ->whereIn('class', $input['classname'])
                ->select('id')
                ->get();

                //dd($input);
                 
                    $smsusername=DB::table('smsusers')
                    ->where('school_id',\Auth::user()->school_id)
                    ->select('username','password','type','smssource')
                    ->first();
                    


                if(!empty($smsusername))
                {
                    //dd($smsusername);   
                    $input['smsusername']=$smsusername->username;
                    $input['smsuserpassword']=$smsusername->password;
                    $input['smsusertype']=$smsusername->type;
                    $input['smssource']=$smsusername->smssource;
                    Event::fire(new SendSmsNotification($input)); 
                     $msg['success'] = 'Message Send Successfully';
                    return \Redirect::back()->withInput($msg);      
                 }else{
                     $msg['error'] = 'School Sms Username Not Found';
                return \Redirect::back()->withInput($msg);

                 }   
               
                       
            }else{
                $msg['error'] = 'Pls Select Class Name';
                return \Redirect::back()->withInput($msg);

            }
            //dd($input);
        }
    }
    public function smsusernamedit()
    {        
         $smsusers=\DB::table('smsusers')
                        ->where('smsusers.school_id',\Auth::user()->school_id)
                        ->join('school','smsusers.school_id', '=', 'school.id')
                      ->select('smsusers.*', 'school.school_name', 'school.id as schoolid')
                    ->first();   
         
        return view('users.notification.smsusernamedit',compact('smsusers'));
    }
    
     public function editsmsusername(Request $request)
    {
        //dd($request);
        $input=$request->all();
        $id=\DB::table('smsusers')->where('id', $input['userid'])->update(['username' => $input['username'],'password'=> $input['password']]);
        if(isset($id))
        {
         return \Redirect::route('user.smsusernamedit');   
        }

    }


    /** @ Updated 14-4-2018 by priya @ **/
    public function deleteAllTimeTable()
    {
        $input = \Request::all();

        foreach($input['select'] as $key => $value)
        {
            TimeTable::where('id',$value)->delete();
        }
        $input['success'] = ' Timetables are deleted Succesfully';
        return \Redirect::back()->withInput($input);
    }

    /**************** end *****************/

}
