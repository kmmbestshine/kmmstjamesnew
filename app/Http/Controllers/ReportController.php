<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Mail\Mailer;
use PDF;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator,
    Redirect,
    Auth,
    api;

use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;
use DateTime;
use DatePeriod;
use DateInterval;
use Event;
use App\Events\SendNotification;
use DB;
use App\School;
use App\Report;
use App\Holiday;

class ReportController extends Controller
{
    protected $user;

    function __construct()
    {
        if(\Auth::check())
        {
            $this->user = \Auth::user();
            $school_image = School::where('id', \Auth::user()->school_id)->first();

            $roler = [];
            if(Auth::user()->type == 'user_role')
            {
                $roleuser = \DB::table('user_role')->where('role_id', Auth::user()->id)->get();
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
        }
        view()->share(compact('school_image', 'roler','userplans'));
    }

    /************************************************************************
    *                            EMPLOYEE SALARY REPORT
    *************************************************************************/

    /** @ Get Employee Salary Report Page @ **/
    public function viewEmployeeSalaryReport()
    {
        //return 'hi';
        $getStaffType = \DB::table('staff')->where('school_id',\Auth::user()->school_id)
            ->get();
        $getAllMonth = \DB::table('month')->get();
        return view('users.report.getEmployeeSalaryReport',compact('getStaffType','getAllMonth'));
    }

    /** @ Get Staff Name Based ON Staff Type @ **/
    public function getEmployeeNameSalaryReport()
    {
        $input = \Request::all();
        $getStaffName = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)
            ->where('type',$input['value'])
            ->get();
        return $getStaffName;
    }

    /** @ Get single Employee Salary Details @ **/
    public function postEmployeeSalaryReport(Report $report)
    {
        $input = \Request::all();
        //return $input;
        $userError = ['staff_type' => 'Staff Type',
            'employee_name' => 'Employee Name',
            'salary_month' => ' Month',
            'salary_year' => 'Year '
        ];
        $validator = \Validator::make($input, [
            'staff_type' => 'required',
            'employee_name' => 'required',
            'salary_month' => 'required',
            'salary_year' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            return $report->doGetEmployeeSalaryReport($this->user, $input);
        }

    }

    /************************************************************************
     *                            STUDENT's DAILY REPORT
     *************************************************************************/

    public function viewStudentsDailyReport()
    {
        $classes = \DB::table('class')
            ->where('school_id',\Auth::user()->school_id)
            ->get();
        return view('users.report.studentDailyReport',compact('classes'));
    }

    public function get_students_daily_base_report()
    {
        $classes = \DB::table('class')
            ->where('school_id',\Auth::user()->school_id)
            ->get();
        $input = \Request::all();
       // return $input;
        $userError = ['class_id' => 'Class ',
            'section_id' => 'Section',
            'date' => ' Date',
            'status' => 'Status '
        ];
        $validator = \Validator::make($input, [
            'class_id' => 'required',
            'section_id' => 'required',
            'date' => 'required',
            'status' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            $holiday = new Holiday();
            $is_holiday = $holiday->is_holiday($input['date']);
            if ($is_holiday)
            {
                $input['error'] = '  Not placed attendance at holiday !!! ';
                return \Redirect::back()->withInput($input);
            }
            $getCurrentSession = \DB::table('session')
                ->where('school_id',\Auth::user()->school_id)
                ->where('active',1)->first();
            $getStudentsReport = \DB::table('attendance')->where('attendance.school_id',\Auth::user()->school_id)
                ->where('attendance.attendance',$input['status'])
                ->where('attendance.date',$input['date'])
                ->leftJoin('student','student.id','=','attendance.student_id')
                ->leftJoin('class','class.id','=','attendance.class_id')
                ->leftJoin('section','section.id','=','attendance.section_id')
                ->leftJoin('users','users.id','=','student.user_id') ;
            if($input['class_id']!='all_class')
            {
               //return 'single class';
                $getStudentsReport = $getStudentsReport
                    ->where('attendance.class_id',$input['class_id'])
                    ->where('attendance.section_id',$input['section_id']);
            }

            $getStudentsReport = $getStudentsReport ->select(
                                // DB::raw("count(attendance.attendance) as total_status"),
                                'attendance.*',
                                'student.name',
                                'student.registration_no',
                                'student.roll_no',
                                'student.contact_no',
                                'users.username',
                                'class.class',
                                'section.section',
                                'class.class'
                                )
                                // ->groupBy('attendance.date')
                                ->get();
           // dd($getStudentsReport);
            $currentDate = date("d-m-Y_H_i_s");

            \Excel::create("dailyStudentReport_" . $currentDate, function ($excel) use ($getStudentsReport, $input, $classes) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getStudentsReport, $input, $classes) {
                    $sheet->loadView('users.report.dailyStudentAnalystReport')->with('getStudentsReport', $getStudentsReport)->with('input', $input)->with('classes', $classes);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('dailyStudentReport'));

            $fileURL = storage_path() . "/dailyStudentReport/dailyStudentReport_" . $currentDate . '.xls';
            \Session::put('attendanceUrl', $fileURL);
        }
        return view('users.report.studentDailyReport',compact('classes','getStudentsReport','input'));

    }




}
