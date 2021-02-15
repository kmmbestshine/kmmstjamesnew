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

class TeacherController extends Controller
{
    protected $user;

    protected $class;

    protected $section;
    private $active_session;
    function __construct()
    {
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();

        if(\Auth::check())
        {
            $this->class = Employee::where('user_id', \Auth::user()->id)->select('class')->first();
            $this->section = Employee::where('user_id', \Auth::user()->id)->select('section')->first();

            $this->user= \Auth::user();
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

            \View::share(compact('school_image', 'roler','userplans'));
        }
    }

     public function getEmployee(Employee $emp)
    {
        return $emp->doGetEmployee($this->user);
    }

    public function insertEmployee()
    {
        $classes = addClass::where('school_id', $this->user->school_id)
            ->where('session_id',$this->active_session->id)//updated 6-6-2018
            ->get();
        $staffs = Staff::where('school_id', $this->user->school_id)->get();
        $salary = \DB::table('salary')->where('school_id', $this->user->school_id)->orderBy('value', 'ASC')->get();
        return view('users.employee.employee', compact('classes', 'staffs', 'salary'));
    }

    public function postEmployee(Employee $emp)
    {
        $input = \Request::all();
        $userError = ['type' => 'Employee Type', 'name' => 'Name', 'mobile' => 'Contact No', 'email' => 'Email', 'class' => 'Class', 'section' => 'Section', 'image' => 'Image'];
        $validator = \Validator::make($input, [
                'type' => 'required',
                'name' => 'required',
                'mobile' => 'required' 
            ], $userError);
        if($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $emp->doPostEmployee($input, $this->user);
    }

    public function deleteEmployee($id){
       $check_tme_tbl=\DB::table('time-table')->where('teacher_id','=',$id)
           ->where('session_id',$this->active_session->id)//updated 6-6-2018
           ->where('school_id','=',\Auth::user()->school_id)->get();
       $check_role=\DB::table('teacher')->where('teacher.id','=',$id)
               ->join('user_role','teacher.user_id','=','user_role.role_id')->get();
        if(empty($check_tme_tbl)&&empty($check_role)){
            Employee::where('id', $id)->delete();
            $msg['success'] = 'Success to Delete this Employee';
            return \Redirect::back()->withInput($msg);
        }
        else{
           $msg['error'] = 'Please Delete Assigned works';
            return \Redirect::back()->withInput($msg); 
        }
    }
    public function editEmployee($id)
    {
        $employee = Employee::where('id', $id)->first();
        $classes = addClass::where('school_id', $this->user->school_id)->get();
        $staffs = Staff::where('school_id', $this->user->school_id)->get();
        $sections = Section::where('class_id', $employee->class)->get();
        $salary = \DB::table('salary')->where('school_id', $this->user->school_id)->get();
        return view('users.employee.edit', compact('employee', 'classes', 'staffs', 'sections', 'salary'));
    }

    public function updateEmployee(Employee $emp)
    {
        $input = \Request::all();
        $userError = ['id' => 'Employee Id', 'type' => 'Employee Type', 'name' => 'Name', 'mobile' => 'Contact No', 'email' => 'Email', 'class' => 'Class', 'section' => 'Section', 'image' => 'Image'];
        $validator = \Validator::make($input, [
                'id' => 'required|numeric',
                'type' => 'required',
                'name' => 'required',
                'mobile' => 'required'
            ], $userError);
        if($validator->fails())
            return \Redirect::back()->withInput($input)->withErrors($validator);
        return $emp->doUpdateEmployee($input, $this->user);
    }

    public function getSubjects(Subject $subject, $platform)
    {
    	return $subject->doGetSubjects($this->user, $platform, $this->section);
    }

    public function postAttendance(Request $request, Attendance $att, $platform)
    {
    	$userError = ['attendance' => 'Attendance', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($request->all(), [
            'attendance' => 'required|json',
            'date' => 'required|date_format:dd-mm-YY'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $att->doPostAttendanceByTeacher($request, $this->user, $platform, $this->class, $this->section);
    }

    public function postHomeWork(Request $request, Homework $hw, $platform)
    {
    	$userError = ['subject_id'=>'Subject', 'description'=>'Description', 'image'=>'Image', 'date' => 'Date'];
        $validator = \Validator::make($request->all(), [
            'subject_id'=>'required',
            'description'=>'required',
            'image'=>'required',
            'date' => 'required|date_format:d-m-Y'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        return $hw->doPostHomework($request, $this->user, $platform, $this->class, $this->section);
    }

   /* public function exportEmployee()
    {
        $emps = Employee::where('teacher.school_id', $this->user->school_id)
                        ->leftJoin('staff', 'teacher.type', '=', 'staff.id')
                        ->leftJoin('class', 'teacher.class', '=', 'class.id')
                        ->leftJoin('section', 'teacher.section', '=', 'section.id')
                        ->leftJoin('users', 'teacher.user_id', '=', 'users.id')
                        ->select
                        (
                            'teacher.id',
                            'staff.staff_type',
                            'class.class',
                            'section.section',
                            'teacher.name',
                            'users.username',
                            'users.hint_password',
                            'teacher.mobile',
                            'teacher.email',
                            'teacher.avatar'
                        )
                        ->get();
        \Excel::create('Laravel Excel', function($excel) use ($emps) {
            $excel->sheet('Excel sheet', function($sheet) use ($emps) {
                $sheet->loadView('users.employee.export')->with('emps', $emps);
            });
        })->export('xls');
    }*/

    public function exportEmployee($session_id)
    {
        $emps = Employee::where('teacher.school_id', $this->user->school_id)
            ->where('teacher.session_id',$session_id)//updated 6-6-2018
            ->leftJoin('staff', 'teacher.type', '=', 'staff.id')
            ->leftJoin('class', 'teacher.class', '=', 'class.id')
            ->leftJoin('section', 'teacher.section', '=', 'section.id')
            ->leftJoin('users', 'teacher.user_id', '=', 'users.id')
            ->select
            (
                'teacher.id',
                'staff.staff_type',
                'class.class',
                'section.section',
                'teacher.name',
                'users.username',
                'users.hint_password',
                'teacher.mobile',
                'teacher.email',
                'teacher.avatar'
            )
            ->get();
        \Excel::create('Laravel Excel', function($excel) use ($emps)
        {
            $excel->sheet('Excel sheet', function($sheet) use ($emps)
            {

                // $style = array(
                //      'alignment' => array(
                //          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                //      )
                //  );

                //  $sheet->getDefaultStyle()->applyFromArray($style);
                //  $sheet->getActiveSheet()->getColumnDimension('I')->setWidth(0.54);
                //  $sheet->setFontSize(12);
                //  $sheet->setAllBorders('thin');

                // $sheet->setWidth('I', 600);
                $sheet->loadView('users.employee.export')->with('emps', $emps);
            });
        })->export('xls');
    }

    /** @ Updated 4-5-2018 by priya @ **/
    public function deleteAllEmployee()
    {
        $input = \Request::all();

        foreach($input['select'] as $key => $value)
        {
            Employee::where('id',$value)->delete();
        }
        $input['success'] = ' Employees are deleted Succesfully';
        return \Redirect::back()->withInput($input);
    }

    /**************** end *****************/
}