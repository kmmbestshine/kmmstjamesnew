<?php

namespace App\Http\Controllers;

use App\EmployeeAttendance;
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

class EmployeeController extends Controller
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

    /*****************************************************************************
     *                              EMPLOYEE ATTENDANCE MODULE
     *****************************************************************************/

    /** @ To Download Report @ **/
    public function downloadEmployeeReport()
    {
        return response()->download(\Session::get('getAttendanceReportUrl'));
    }

    /** @  View Teacher Attendance Page  @ **updated in 6-11-2017 **/
    public function getTeacherAttendance()
    {
        //return 'hii';
        $getStaffType = \DB::table('staff')->where('school_id',\Auth::user()->school_id)
            ->get();
        $getAllStaffs = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)
            ->get();
        $getCuurentSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active',1)->first();

        $current_year = date('Y');
        $current_month = date('m');

        $getAllTeacherAttendance = \DB::table('teacher_attendance')
            ->where('teacher_attendance.school_id', \Auth::user()->school_id)
            ->where('teacher_attendance.session_id',$getCuurentSession->id)
            ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')
            ->select('teacher_attendance.*','teacher.name','teacher.user_id','teacher.mobile','users.username','staff.staff_type')
            ->orderBy('teacher_attendance.created_at','desc')
            ->get();
        return view('users.employee_attendance.addTeacherAttendance',compact('getAllStaffs','getStaffType','getAllTeacherAttendance'));
    }

    /** @ Get Staffs based on Staff Type @ **/
    public function getStaffTypeDetails()
    {
        //return 'Get Staffs';
        $staffType = \Request::get('srtype');
        $getTeachers = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)
            ->where('type',$staffType)
            ->get();
        return $getTeachers;
    }

    /** @  Check Attendance Details for Employee Exist in Same data @ **/
    public function checkStaffAttendanceDetails()
    {
        //return "Check Attendance Details";
        $staffType = \Request::get('srtype');
        $staffName = \Request::get('stname');
        $preDate = \Request::get('predate');
       // $attendanceSession = \Request::get('session');

        //return $preDate;

        $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();


        $getEmployeeId = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)
            ->where('id',$staffName)
            ->first();
        $employeeName =$getEmployeeId->name;
        //return $getEmployeeId->user_id;

        $checkStaffAttendance = \DB::table('teacher_attendance')
            ->where('school_id',\Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->where('date',$preDate)
            //->where('session_type',$attendanceSession)
            ->where('staff_type',$staffType)
            ->where('employee_id',$getEmployeeId->user_id)
            ->first();

        //return $checkStaffAttendance;

        if($checkStaffAttendance)
        {
            return 'Has Value';
        }
        else
        {
            return $employeeName;
        }

        //var_dump($getEmployeeId->user_id);
    }

    /** @  Post Employee Attendance manually  @ **/
    public function  postTeacherAttendance()
    {
        $input = \Request::all();
        // return $input;
        $userError = ['type' => 'Staff Type',
            'name' => 'Staff Name',
            'present_date' => 'Attendance Date',
            //'session' => 'Session Type',
            'attendance' => 'Attendance'
        ];
        $validator = \Validator::make($input, [
            'type' => 'required',
            'name' => 'required',
            //'session' => 'required',
            'attendance' => 'required',
            'present_date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);

        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            $getCurrentSession = \DB::table('session')->where('school_id', \Auth::user()->school_id)
                ->where('active',1)->first();

            /*foreach($input['name'] as $key => $value)
            {*/
            $getEmployeeId = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)
                //->where('id',$input['name'][$key])
                ->where('id',$input['name'])
                ->first();
            //return $input['attendance'];
            if($input['attendance'] == 'P')
            {
                $timeError = [
                    'in_time' => 'In Time',
                    'out_time' => 'Out Time'
                ];
                $validate = \Validator::make($input, [
                    'in_time' => 'required',
                    'out_time' => 'required'
                ], $timeError);
                $validate->setAttributeNames($timeError);
                if ($validate->fails())
                {
                    return \Redirect::back()->withErrors($validate)->withInput($input);
                }
                else
                {
                    $in_time = $input['in_time'];
                    $out_time = $input['out_time'];
                }
            }
            else
            {
                $in_time ='';
                $out_time='';
            }
            $insertAttendance = \DB::table('teacher_attendance')
                ->insert([
                    'school_id' => \Auth::user()->school_id,
                    'session_id' => $getCurrentSession->id,
                    'employee_id' => $getEmployeeId->user_id,
                    //'session_type' => $input['session'],
                    'attendance' => $input['attendance'],
                    'staff_type' => $input['type'],
                    'in' => $in_time,
                    'out' => $out_time,
                    'date' => $input['present_date']
                ]);
            /*}*/

            if($insertAttendance)
            {
                //return 'success';
                $input['success'] = 'Staff Attendance added Successfully !!!';
            }
            else
            {
                //return 'error';
                $input['error'] = 'Error in Adding Staff Attendance !!!';
            }
        }
        return \Redirect::back()->withInput($input);
    }

    /** @  Post Employee Attendance In Excel Sheet @ **/
    public function postExcelTeacherAttendance(EmployeeAttendance $teacher)
   // public function postExcelTeacherAttendance(Teacher_attendance $teacher)
    {
        $input = \Request::all();
        // return $input;
        $userError = ['excel_attendance' => 'Attendance'];
        $validator = \Validator::make($input, [
            //'excel_attendance' => 'required'
            'excel_attendance' => 'required|mimes:xls,xlsx'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            //return 'success';
            //$rows = Excel::load($input['excel_attendance'])->get();
            return $teacher->doImportEmployeeAttendance($this->user, $input);
        }
        return \Redirect::back()->withInput($input);
    }

    /** @ View Employees Attendance Monthly Report Page  @ **/
    public function viewMonthlyReport()
    {
        $getMonth = \DB::table('month')->get();
        $getStaffType =\DB::table('staff')
            ->where('school_id',\Auth::user()->school_id)
            ->get();
        $getCurrentSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active',1)->first();

        $input = \Request::all();
        //return $input;
        $currentDate = date("d-m-Y_H_i_s");

        /** @ View all Details @ **/
        if(!$input)
        {
            //return 'get all';
            $getAllTeacherAttendance = \DB::table('teacher_attendance')
                ->where('teacher_attendance.school_id', \Auth::user()->school_id)
                ->where('teacher_attendance.session_id',$getCurrentSession->id)
                ->leftJoin('salary','salary.employee_id','=','teacher_attendance.employee_id')
                ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
                ->leftJoin('users','teacher.user_id','=','users.id')
                ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')->select(
                    'teacher_attendance.*',
                    'salary.value as employee_salary',
                    'teacher.name',
                    'users.username',
                    'teacher.user_id',
                    'teacher.mobile',
                    'staff.staff_type'
                )
                ->orderBy('teacher_attendance.created_at','desc')
                ->get();
        }
        /** @ View Employee Attendance Report Based Daily $ Monthly Wise @ **/
        else
        {
            /** @ View Employee Attendance Report For Monthly Wise @ **/
            if($input['monthly_report'])
            {
                $typeError = [
                    'staff_type' => 'Staff Type',
                    'month' => 'Month',
                    'year' => 'Year'
                ];
                $validate = \Validator::make($input, [
                    'staff_type' => 'required',
                    'month' => 'required',
                    'year' => 'required'
                ], $typeError);
                $validate->setAttributeNames($typeError);
                if ($validate->fails())
                {
                    return \Redirect::back()->withErrors($validate)->withInput($input);
                }
                else
                {
                    $getMonthName = \DB::table('month')->where('id', $input['month'])->first();
                    $getAllTeachers = \DB::table('teacher')
                        ->where('teacher.school_id', \Auth::user()->school_id)
                        ->leftJoin('users','teacher.user_id','=','users.id');
                    if($input['staff_type'] != 'all')
                    {
                        $getAllTeachers = $getAllTeachers
                            ->where('teacher.type', $input['staff_type']);
                    }
                    $getAllTeachers = $getAllTeachers->select(
                        'teacher.*','users.username'
                    )
                        ->get();
                    //dd($getAllTeachers);
                    foreach($getAllTeachers as $teachers )
                    {
                       //var_dump($teachers->user_id);
                        $getMonthlyTeacherAttendance = \DB::table('teacher_attendance')
                            ->where('school_id', \Auth::user()->school_id)
                            ->where('session_id',$getCurrentSession->id)
                            ->whereYear('date', '=', $input['year'])
                            ->whereMonth('date', '=', $input['month'])
                            ->where('employee_id', $teachers->user_id)
                            ->get();
                        $teachers->monthlyReport = $getMonthlyTeacherAttendance;
                    }
                    //dd($getMonthlyTeacherAttendance);
                    //dd($getAllTeachers);
                }
            }
            /** @ View Employee Attendance  Daily Report @ **/
            elseif($input['daily_report'])
            {
                //return 'daily report';
                $dailyError = [
                    'daily_staff_type' => 'Staff Type',
                    'daily_date' => 'Date',
                    'daily_status' => 'Status'
                ];
                $validator = \Validator::make($input, [
                    'daily_staff_type' => 'required',
                    'daily_date' => 'required',
                    'daily_status' => 'required'
                ], $dailyError);
                $validator->setAttributeNames($dailyError);
                if ($validator->fails())
                {
                    return \Redirect::back()->withErrors($validator)->withInput($input);
                }
                else
                {
                    $holiday = new Holiday();
                    $is_holiday = $holiday->is_holiday($input['daily_date']);
                    if ($is_holiday)
                    {
                        $input['error'] = '  Not placed attendance at holiday !!! ';
                        return \Redirect::back()->withInput($input);
                    }
                    else
                    {
                        $getDailyTeacherAttendance = \DB::table('teacher_attendance')
                            ->leftJoin('teacher', 'teacher.user_id', '=', 'teacher_attendance.employee_id')
                            ->leftJoin('users', 'teacher.user_id', '=', 'users.id')
                            ->leftJoin('staff', 'staff.id', '=', 'teacher_attendance.staff_type')
                            ->where('teacher_attendance.school_id', \Auth::user()->school_id)
                            ->where('teacher_attendance.session_id', $getCurrentSession->id)
                            ->where('teacher_attendance.date', $input['daily_date'])
                            ->where('teacher_attendance.attendance', $input['daily_status']);

                        if ($input['daily_staff_type'] != 'all_staff')
                        {
                            $getDailyTeacherAttendance = $getDailyTeacherAttendance
                                ->where('teacher_attendance.staff_type', $input['daily_staff_type']);
                        }

                        $getDailyTeacherAttendance = $getDailyTeacherAttendance
                            ->select(
                                'teacher_attendance.*',
                                'teacher.name',
                                'teacher.user_id',
                                'teacher.mobile',
                                'users.username',
                                'staff.staff_type'
                            )
                            ->get();
                    }
                }
            }
        }
        //dd($getDailyTeacherAttendance);
        //dd($getAllTeacherAttendance);

        //Export Excel Sheet For All ,Daily $ Monthly Report
        if($getAllTeacherAttendance)
        {
            //return 'All';
            \Excel::create("all_employee_report_" . $currentDate, function ($excel) use ($getAllTeacherAttendance,$input,$getMonthName,$getDailyTeacherAttendance) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getAllTeacherAttendance,$input,$getMonthName,$getDailyTeacherAttendance) {
                    $sheet->loadView('users.employee_attendance.monthlyWiseExport')->with('getAllTeacherAttendance', $getAllTeacherAttendance)->with('input', $input)->with('getMonthName',$getMonthName)->with('getDailyTeacherAttendance',$getDailyTeacherAttendance);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('monthly_employee_report'));

            $fileURL = storage_path() . "/monthly_employee_report/all_employee_report_" . $currentDate . '.xls';
            \Session::put('getAttendanceReportUrl', $fileURL);
        }
        elseif ($getAllTeachers)
        {
            //return 'Monthly Report';
            \Excel::create("employee_monthly_report_" . $currentDate, function ($excel) use ($getAllTeacherAttendance,$input,$getMonthName,$getDailyTeacherAttendance,$getAllTeachers) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getAllTeacherAttendance,$input,$getMonthName,$getDailyTeacherAttendance,$getAllTeachers) {
                    $sheet->loadView('users.employee_attendance.monthlyWiseExport')->with('getAllTeacherAttendance', $getAllTeacherAttendance)->with('input', $input)->with('getMonthName',$getMonthName)->with('getDailyTeacherAttendance',$getDailyTeacherAttendance)->with('getAllTeachers',$getAllTeachers);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('monthly_employee_report'));

            $fileURL = storage_path() . "/monthly_employee_report/employee_monthly_report_" . $currentDate . '.xls';
            \Session::put('getAttendanceReportUrl', $fileURL);
        }
        else
        {
            //return 'Daily Report';
            \Excel::create("employee_daily_report_" . $currentDate, function ($excel) use ($getAllTeacherAttendance,$input,$getMonthName,$getDailyTeacherAttendance) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getAllTeacherAttendance,$input,$getMonthName,$getDailyTeacherAttendance) {
                    $sheet->loadView('users.employee_attendance.monthlyWiseExport')->with('getAllTeacherAttendance', $getAllTeacherAttendance)->with('input', $input)->with('getMonthName',$getMonthName)->with('getDailyTeacherAttendance',$getDailyTeacherAttendance);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('daily_employee_report'));

            $fileURL = storage_path() . "/daily_employee_report/employee_daily_report_" . $currentDate . '.xls';
            \Session::put('getAttendanceReportUrl', $fileURL);
        }




        return view('users.employee_attendance.viewMonthlyWiseReport',compact('getAllTeachers','getStaffType','getAllTeacherAttendance','getMonthlyTeacherAttendance','getMonthName','getMonth','input','getDailyTeacherAttendance'));
    }

    /** @ View Daily Attendance Report  for Employees  @ **/
    public function viewTeacherAttendanceReport()
    {
        $getStaffType =\DB::table('staff')
            ->where('school_id',\Auth::user()->school_id)
            ->get();
        return view('users.employee_attendance.dailyEmployeeAttendanceReport',compact('getStaffType'));
    }

    /** @ Get Staff Type Based Employee Attendance Daily Report   @ **/
    /*public function getStaffBasedAttendanceReport()
    {
        $input = \Request::all();
        //return $input;
        $typeError = [
            'staff_type' => 'Staff Type',
            'date' => 'Date',
            'status' => 'Status'
        ];
        $validate = \Validator::make($input, [
            'staff_type' => 'required',
            'date' => 'required',
            'status' => 'required'
        ], $typeError);
        $validate->setAttributeNames($typeError);
        if ($validate->fails())
        {
            return \Redirect::back()->withErrors($validate)->withInput($input);
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
            else
            {
                $getCuurentSession = \DB::table('session')
                    ->where('school_id', \Auth::user()->school_id)
                    ->where('active',1)->first();
                $getAllTeacherAttendance = \DB::table('teacher_attendance')
                    ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
                    ->leftJoin('users','teacher.user_id','=','users.id')
                    ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')
                    ->where('teacher_attendance.school_id', \Auth::user()->school_id)
                    ->where('teacher_attendance.session_id',$getCuurentSession->id)
                    ->where('teacher_attendance.date',$input['date'])
                    ->where('teacher_attendance.attendance',$input['status']);

                if($input['staff_type'] != 'all_staff')
                {
                    $getAllTeacherAttendance = $getAllTeacherAttendance
                        ->where('teacher_attendance.staff_type',$input['staff_type']);
                }

                $getAllTeacherAttendance = $getAllTeacherAttendance
                    ->select(
                        'teacher_attendance.*',
                        'teacher.name',
                        'teacher.user_id',
                        'teacher.mobile',
                        'users.username',
                        'staff.staff_type'
                    )
                    ->get();
            }

            //For Excel sheet
            $currentDate = date("d-m-Y_H_i_s");
            \Excel::create("employeeDailyReport_" . $currentDate, function ($excel) use ($getAllTeacherAttendance,$input) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getAllTeacherAttendance,$input) {
                    $sheet->loadView('users.employee_attendance.employeeReportExport')->with('getAllTeacherAttendance', $getAllTeacherAttendance)->with('input', $input);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('employee_attendance_report'));

            $fileURL = storage_path() . "/employee_attendance_report/employeeDailyReport_" . $currentDate . '.xls';
            \Session::put('getAttendanceReportUrl', $fileURL);

        }
        $getStaffType =\DB::table('staff')
            ->where('school_id',\Auth::user()->school_id)
            ->get();
        return view('users.employee_attendance.dailyEmployeeAttendanceReport',compact('getAllTeacherAttendance','input','getStaffType'));

    }*/

    /** @ Get Session Based Employee Attendance   @ **/
   /* public function getSessionBasedAttendance()
    {
        $input = \Request::all();
        //return $input;
        $typeError = [
            'session_type' => 'Session Type'
        ];
        $validate = \Validator::make($input, [
            'session_type' => 'required'
        ], $typeError);
        $validate->setAttributeNames($typeError);
        if ($validate->fails())
        {
            return \Redirect::back()->withErrors($validate)->withInput($input);
        }
        else
        {
            $getCuurentSession = \DB::table('session')
                ->where('school_id', \Auth::user()->school_id)
                ->where('active',1)->first();
            $getAllTeacherAttendance = \DB::table('teacher_attendance')
                ->where('teacher_attendance.school_id', \Auth::user()->school_id)
                ->where('teacher_attendance.session_id',$getCuurentSession->id)
                ->where('session_type',$input['session_type'])
                ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
                ->leftJoin('users','teacher.user_id','=','users.id')
                ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')
                ->select('teacher_attendance.*','teacher.name','teacher.user_id','teacher.mobile','users.username','staff.staff_type')
                ->get();
            //For Excel sheet
            $currentDate = date("d-m-Y_H_i_s");
            \Excel::create("employeeDailyReport_" . $currentDate, function ($excel) use ($getAllTeacherAttendance) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getAllTeacherAttendance) {
                    $sheet->loadView('users.payroll.teacherExport')->with('getAllTeacherAttendance', $getAllTeacherAttendance);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('employee_attendance_report'));

            $fileURL = storage_path() . "/employee_attendance_report/employeeDailyReport_" . $currentDate . '.xls';
            \Session::put('getAttendanceReportUrl', $fileURL);

        }
        return view('users.employee_attendance.viewTeacherAttendanceReport',compact('getAllTeacherAttendance','input'));
    }*/

    /** @  To view Individual Employee Attendance  @ **/
    public function viewTeacherAttendanceId($id)
    {
        //return $id;
        $getCurrentSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active',1)->first();

        $viewAllTeacherAttendance = \DB::table('teacher_attendance')
            ->where('teacher_attendance.school_id', \Auth::user()->school_id)
            ->where('teacher_attendance.session_id',$getCurrentSession->id)
            ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')
            ->select('teacher_attendance.*','teacher.name','teacher.user_id','teacher.mobile','users.username','staff.staff_type')
            ->where('teacher_attendance.id',$id)

            ->first();
        return view('users.employee_attendance.viewTeacherAttendance',compact('viewAllTeacherAttendance'));
    }

    /** @ To view Edit Attendance Page For Individual Employee  @ **/
    public function editTeacherAttendance($id)
    {
        if($id != '')
        {
            $getCuurentSession = \DB::table('session')
                ->where('school_id', \Auth::user()->school_id)
                ->where('active',1)->first();
            $getAllTeacherAttendance = \DB::table('teacher_attendance')
                ->where('teacher_attendance.school_id', \Auth::user()->school_id)
                ->where('teacher_attendance.session_id',$getCuurentSession->id)
                ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
                ->leftJoin('users','teacher.user_id','=','users.id')
                ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')
                ->select('teacher_attendance.*','teacher.name','teacher.user_id','teacher.mobile','users.username','staff.staff_type')
                ->where('teacher_attendance.id',$id)
                ->first();
        }
        return view('users.employee_attendance.editTeacherAttendance',compact('getAllTeacherAttendance'));
    }

    /** @ To Edit/Update Individual Employee Attendance Detail  @ **/
    public function updateTeacherAttendance()
    {
        //return 'update attendance';
        $input = \Request::all();
        //return $input;
        $userError = [
            'present_date' => 'Attendance Date',
           // 'session' => 'Session Type',
            'attendance' => 'Attendance'
        ];
        $validator = \Validator::make($input, [
            //'session' => 'required',
            'attendance' => 'required',
            'present_date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            if($input['attendance'] == 'P')
            {
                $timeError = [
                    'in_time' => 'In Time',
                    'out_time' => 'Out Time'
                ];
                $validate = \Validator::make($input, [
                    'in_time' => 'required',
                    'out_time' => 'required'
                ], $timeError);
                $validate->setAttributeNames($timeError);
                if ($validate->fails())
                {
                    return \Redirect::back()->withErrors($validate)->withInput($input);
                }
                else
                {
                    $in_time = $input['in_time'];
                    $out_time = $input['out_time'];
                }
            }
            else
            {
                $in_time ='';
                $out_time='';
            }
            $updateAttendance = \DB::table('teacher_attendance')
                ->where('id',$input['attendance_id'])
                ->update([
                   // 'session_type' => $input['session'],
                    'attendance' => $input['attendance'],
                    'in' => $in_time,
                    'out' => $out_time,
                    'date' => $input['present_date']
                ]);
            if($updateAttendance)
            {
                //return 'success';
                $input['success'] = 'Staff Attendance Updated Successfully !!!';
            }
            else
            {
                //return 'error';
                $input['error'] = 'You Should edit any field to Update !!!';
            }
        }
        return \Redirect::back()->withInput($input);
    }

    /** @  To Delete Individual Employee Attendance Detail @ **/
    public function deleteTeacherAttendance($id)
    {
        if($id !='')
        {
            $deleteAttendance =  \DB::table('teacher_attendance')->where('id', $id)->delete();
            if($deleteAttendance)
            {
                $msg['success'] = ' Success to Delete Staff Attendance !!! ';
            }
            else
            {
                $msg['error'] = ' Error in Deleting Staff Attendance !!! ';
            }

            return \Redirect::back()->withInput($msg);
        }
    }
}
