<?php

namespace App\Http\Controllers;

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
use Dompdf\Dompdf;

class PayrollController extends Controller
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

    /********************************************************************
    *                      PAYROLL MODULE
    ********************************************************************/

    /** @ View Payroll Index Page @ **/
    public function viewPayrollIndex()
    {
        $getMonth =\DB::table('month')->get();

        $input = \Request::all();
        $getCurrentSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active',1)->first();
        $getPayrollDetail = \DB::table('employee_payrolls')
            ->where('employee_payrolls.school_id', \Auth::user()->school_id)
            ->where('employee_payrolls.session_id',$getCurrentSession->id)
            ->leftJoin('teacher','teacher.id','=','employee_payrolls.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->leftJoin('month','month.month','=','employee_payrolls.month')
            ->leftJoin('staff','staff.id','=','teacher.type');

        /** @ To View given year & month Payroll Report  @**/
        if($input['submit_payroll'] == 'payroll')
        {
            $userError = ['payroll_month' => 'Month ',
                'payroll_year' => 'Year'
            ];
            $validator = \Validator::make($input, [
                'payroll_month' => 'required',
                'payroll_year' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                $getMonthName = \DB::table('month')
                    ->where('id', $input['payroll_month'])->first();
                $getPayrollDetail=$getPayrollDetail
                    ->where('employee_payrolls.year', '=', $input['payroll_year'])
                    ->where('employee_payrolls.month', '=', $getMonthName->month);
            }
        }

        /** @ To View All Month Payroll Reports  @**/
        $getPayrollDetail=$getPayrollDetail->select(
            'employee_payrolls.*',
            'teacher.name',
            'month.id as pay_month',
            'users.username',
            'teacher.user_id',
            'teacher.mobile',
            'staff.staff_type'
        )
            ->orderBy('teacher.name', 'asc')
            ->get();

        $getTeachers = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)->get();

        /* Export Payroll report in excel sheeet */
        $currentDate = date("d-m-Y_H_i_s");
        \Excel::create("all_employee_payroll_report_" . $currentDate, function ($excel) use ($getMonthName,$getMonth,$getPayrollDetail,$input) {

            $excel->sheet('Excel sheet', function ($sheet) use ($getMonthName,$getMonth,$getPayrollDetail,$input) {
                $sheet->loadView('users.payroll.export.payrollExport')->with('getMonthName',$getMonthName)->with('getMonth',$getMonth)->with('getPayrollDetail',$getPayrollDetail)->with('input', $input)->with('current_year',$current_year);
                $sheet->setOrientation('portrait');
            });
        })->store('xls', storage_path('monthly_payroll_employee_report'));

        $fileURL = storage_path() . "/monthly_payroll_employee_report/all_employee_payroll_report_" . $currentDate . '.xls';
        \Session::put('getAttendanceReportUrl', $fileURL);

        /*get school*/
        $getSchoolName = \DB::table('school')
            ->where('id', \Auth::user()->school_id)->first();
        $getSname  = substr($getSchoolName->school_name, 0, 5);;

        /* Get Payment Id to add new payroll Report */
        $getPayroll_id = \DB::table('employee_payrolls')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->where('id', DB::raw("(select max(`id`) from employee_payrolls)"))
            ->select('id')
            ->get();
        if($getPayroll_id)
        {
            foreach($getPayroll_id as $key)
            {
               $getId = $key->id;
            }
           $lastId = $getId + 1;
        }
        else
        {
            $lastId = 1;
        }
        $lastInsertedID = strtoupper($getSname).'_PAY_00'.$lastId.'_REP';

        return view('users.payroll.index',compact('lastInsertedID','getTeachers','getCurrentMonthName','getMonthName','getMonth','getPayrollDetail','input'));
    }

    /** @ To add new payroll Report  @**/
    public function add_new_payroll()
    {
        $input = \Request::all();
        if ($input['add_new_payroll'] == 'new_payroll')
        {
            if (empty($input['employee_id']))
            {
                $input['error'] = '  Select the Employee Name !!! ';
                return \Redirect::back()->withInput($input);
            }
            else if (empty($input['pay_day']))
            {
                $input['error'] = '  Select the Payment Date !!! ';
                return \Redirect::back()->withInput($input);
            }
            else
            {
                $getCurrentSession = \DB::table('session')->where('school_id', \Auth::user()->school_id)
                    ->where('active', 1)->first();
                $input['pay_month'] = date('m', strtotime($input['pay_day']));
                if($input['pay_month'] == 1)
                {
                    $getYear = date('Y', strtotime($input['pay_day']));
                    $input['pay_year'] = $getYear-1;
                    $getMonthName = \DB::table('month')
                        ->where('id', 12)
                        ->first();
                }
                else
                {
                    $input['pay_year'] = date('Y', strtotime($input['pay_day']));
                    $getMonthName = \DB::table('month')
                        ->where('id', $input['pay_month'] - 1)
                        ->first();
                }

                if(empty($input['total_leave']))
                {
                    $input['total_leave'] = 0;
                }
                if(empty($input['allowance']))
                {
                    $input['allowance'] = 0;
                }
                if(empty($input['bonus']))
                {
                    $input['bonus'] = 0;
                }
                if(empty($input['deduction']))
                {
                    $input['deduction'] = 0;
                }
                if(empty($input['tax']))
                {
                    $input['tax'] = 0;
                }
                $total_net_salary = ($input['gross_value'] - ($input['deduction'] + $input['tax']) );

                $insertPayroll = \DB::table('employee_payrolls')
                    ->insert([
                        'session_id' => $getCurrentSession->id,
                        'school_id' => \Auth::user()->school_id,
                        'employee_id' => $input['employee_id'],
                        'payment_id' => $input['payroll_id'],
                        'payment_date' => $input['pay_day'],
                        'worked_days' => $input['worked_days'],
                        'month' => $getMonthName->month,
                        'year' => $input['pay_year'],
                        'basic' => $input['basic'],
                        'allowance' => $input['allowance'],
                        'overtime' => 0,
                        'bonus' => $input['bonus'],
                        'ptax' => $input['tax'],
                        'deductions' => $input['deduction'],
                        'allowed_leave' => $input['allowed_leave'],
                        'earned_leave' => $input['total_leave'],
                        'gross' => $input['gross_value'],
                        'total_salary' => $total_net_salary
                    ]);
                if ($insertPayroll)
                {
                    $input['success'] = '  Payroll Details Added Successfully !!! ';
                }
                else
                {
                    $input['error'] = ' Error in adding Payroll Details !!! ';
                }

            }
            return \Redirect::back()->withInput($input);
        }
    }

    /* To Get Employee Name where not in payroll list based on month,year */
    public function get_payroll_attendance_report()
    {
        $srdate = \Request::get('srdate');
        $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        $session_id = $getCurrentSession->id;
        $school_id = \Auth::user()->school_id;

        $getMonth = date('m',strtotime($srdate));
        $getYear =date('Y',strtotime($srdate));
        $getEmployee = \DB::table('teacher')
            ->select('*')
            ->whereNotIn('id',function($query) use($getMonth,$getYear,$session_id)
            {
                $query->select('employee_id')->from('employee_payrolls')
                    ->where('session_id','=',$session_id)
                    ->whereMonth('payment_date','=',$getMonth)
                    ->whereYear('payment_date','=',$getYear);
            })
            ->where('school_id',\Auth::user()->school_id)
            ->get();
        return $getEmployee;
    }

    /* To get employee overtime,attendance details to add payroll */
    public function get_payroll_all_details()
    {
        $srteacher = \Request::get('srteacher');
        $payment_day = \Request::get('payment_day');
        $getMonth = date('m',strtotime($payment_day));
        if($getMonth == 1)
        {
            $year = date('Y', strtotime($payment_day));
            $getYear = $year-1;
            $month = 12;
        }
        else
        {
            $getYear = date('Y', strtotime($payment_day));
            $month = $getMonth - 1;
        }

        $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();

        $getEmployeePayrollDetail = \DB::table('teacher_attendance')
            ->where('teacher_attendance.school_id', \Auth::user()->school_id)
            ->where('teacher_attendance.session_id',$getCurrentSession->id)
            ->leftJoin('teacher','teacher.user_id','=','teacher_attendance.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->leftJoin('staff','staff.id','=','teacher_attendance.staff_type')
            //->leftJoin('salary_bonuses','salary_bonuses.date','=','teacher_attendance.date')
            ->whereYear('teacher_attendance.date', '=', $getYear)
            ->whereMonth('teacher_attendance.date', '=', $month)
            ->where('teacher.id', '=', $srteacher)
            ->select(
                DB::raw( "SUM(CASE WHEN teacher_attendance.attendance !='P' THEN 1 ELSE 0 END) AS 'total_leave_taken'" ),
              //  DB::raw("sum(salary_bonuses.bonus) as total_overtime"),
                'teacher_attendance.*',
                'teacher.name',
                'teacher.salary',
                'users.username',
                'teacher.user_id',
                'teacher.mobile',
                'staff.staff_type'
                //'salary_bonuses.bonus',
                //'salary_bonuses.date as bonus_date'
            )
            ->get();

        $getPayrollBonusValue = \DB::table('salary_bonuses')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();

        $getPayrollAllowedLeave = \DB::table('allowed_leaves')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();

         $data['month']=$month;
         $data['year']=$getYear;
         $data['employee_attendance'] = $getEmployeePayrollDetail;
       //  $data['employee_bonus'] = $getPayrollBonusValue;
         $data['employee_allowed_leave'] = $getPayrollAllowedLeave;
         return $data;
    }

    /* To get Employee Professional Tax,Deductions details to add payroll  */
    public function get_payroll_gross_details()
    {
        $srteacher = \Request::get('srteacher');
        $payment_day = \Request::get('payment_day');
        $getMonth = date('m',strtotime($payment_day));
       // $getYear = date('Y',strtotime($payment_day));
        if($getMonth == 1)
        {
            $year = date('Y', strtotime($payment_day));
            $getYear = $year-1;
            $month = 12;
        }
        else
        {
            $getYear = date('Y', strtotime($payment_day));
            $month = $getMonth - 1;
        }
        $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();

        $getEmployeeId = \DB::table('teacher')->where('school_id',\Auth::user()->school_id)
            ->where('id',$srteacher)
            ->select('teacher.salary')
            ->first();

        $getPayrollProfTax = \DB::table('professional_taxes')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->whereRaw('? BETWEEN from_salary AND to_salary', [$getEmployeeId->salary])
            ->get();

        $getPayrollDeductionPercent = \DB::table('salary_deductions')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->select(
                DB::raw("sum(salary_deductions.deduction_percentage) as total_deduction_percent")  ,
                'salary_deductions.*'
            )
            ->get();

        $getPayrollAllowedLeave = \DB::table('allowed_leaves')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();

         $data['month']=$month;
         $data['year']=$getYear;
         $data['employee_details'] = $getEmployeeId;
         $data['employee_pTax'] = $getPayrollProfTax;
         $data['employee_deductions'] = $getPayrollDeductionPercent;
         $data['employee_allowed_leave'] = $getPayrollAllowedLeave;
         return $data;
    }


    /** @ View Single Employee Payroll Report for a month @ **/
    public function get_single_employee_payroll($id,$year,$month)
    {
        $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();

        $get_month = \DB::table('month')->where('id',$month)->first();

        $get_single_employee = \DB::table('employee_payrolls')
            ->where('employee_payrolls.school_id', \Auth::user()->school_id)
            ->where('employee_payrolls.session_id',$getCurrentSession->id)
            ->where('employee_payrolls.employee_id',$id)
            ->where('employee_payrolls.year',$year)
            ->where('employee_payrolls.month',$get_month->month)
            ->leftJoin('teacher','teacher.id','=','employee_payrolls.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->select(
                'employee_payrolls.*',
                'teacher.name',
                'teacher.email',
                'users.username',
                'teacher.user_id'
            )
            ->first();

            /*updated 16-3-2018*/
        $getDeductionPercent = \DB::table('salary_deductions')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();
        //dd($getDeductionPercent) ;
            
        return view('users.payroll.editPayrollReport',compact('get_month','year','month','get_single_employee'));

    }
    /* send payslip to employee */
    public function send_payroll_report($id,$year,$month)
    {
        $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();

        /* Get School Name */
        $getSchoolName = \DB::table('school')
            ->where('id', \Auth::user()->school_id)->first();

        $getCurrentMonthName = \DB::table('month')->where('id', $month)->first();

        $getEmployeeDetail =  \DB::table('teacher')
            ->where('teacher.school_id', \Auth::user()->school_id)
            ->where('teacher.id',$id)
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->leftJoin('staff','staff.id','=','teacher.type')
            ->leftJoin('salary','salary.employee_id','=','teacher.user_id')
            ->select(
                'teacher.*',
                'users.username',
                'salary.value as employee_salary',
                'staff.staff_type'
            )
            ->first();

        $get_single_employee =\DB::table('employee_payrolls')
            ->where('employee_payrolls.school_id', \Auth::user()->school_id)
            ->where('employee_payrolls.session_id',$getCurrentSession->id)
            ->where('employee_payrolls.employee_id',$id)
            ->where('employee_payrolls.year',$year)
            ->where('employee_payrolls.month',$getCurrentMonthName->month)
            ->leftJoin('teacher','teacher.id','=','employee_payrolls.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->select(
                'employee_payrolls.*',
                'teacher.name',
                'users.username',
                'teacher.user_id'
            )
            ->first();

        $getDeductionPercent = \DB::table('salary_deductions')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();

        $email_id =$getEmployeeDetail->email;
        if($get_single_employee)
        {
            $currentDate = date("d-m-Y_H_i_s");
            $getPayrollDetail = $get_single_employee;
            $input['payroll_employee_month'] = $month;
            $input['payroll_employee_year'] = $year;
            $pdf = \PDF::loadView('users.payroll.pdf.invoice_result',compact('getSchoolName','getEmployeeDetail','getPayrollDetail','getCurrentMonthName','getDeductionPercent','input'))->save('../storage/files/payrollReport_'.$currentDate.'.pdf');

            $mailSend=\Mail::send('users.payroll.emails.employee_pdf_report',['pdf'=>$pdf,'getSchoolName' => $getSchoolName,'getEmployeeDetail' => $getEmployeeDetail,'get_single_employee' => $getPayrollDetail,'getDeductionPercent' => $getDeductionPercent,'getCurrentMonthName' => $getCurrentMonthName,'month'=>$month,'year'=>$year],function ($message) use($email_id,$pdf,$currentDate)
            {
                $message->from('shineschoolappusername@gmail.com', 'Shine School');
                $message->to($email_id)->subject('Pay Slip ');
                $message->attach('../storage/files/payrollReport_'.$currentDate.'.pdf');
            });
            if($mailSend)
            {
                $input['success'] = ' Pay Slip has been sent to the Employee !!!';
            }
            else
            {
                $input['error'] = ' Your Message has not been send Successfully !!!';
            }
        }
        return \Redirect::back()->withInput($input);
    }

    /** @  Download Payroll report in pdf format  @ **/
    public function send_employee_payroll_report_pdf()
    {
        $input = \Request::all();

        /*  GET Current Session */
        $getCurrentSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active',1)->first();

        /* GEt School Name */
        $getSchoolName = \DB::table('school')
        ->where('id', \Auth::user()->school_id)->first();

        /* Get Month Name */
        $getCurrentMonthName = \DB::table('month')->where('id', $input['payroll_employee_month'])->first();

        $getEmployeeDetail =  \DB::table('teacher')
            ->where('teacher.school_id', \Auth::user()->school_id)
            ->where('teacher.id',$input['payroll_employee_id'])
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->leftJoin('staff','staff.id','=','teacher.type')
            ->leftJoin('salary','salary.employee_id','=','teacher.user_id')
            ->select(
                'teacher.*',
                'users.username',
                'salary.value as employee_salary',
                'staff.staff_type'
            )
            ->first();

        $getPayrollDetail =\DB::table('employee_payrolls')
            ->where('employee_payrolls.school_id', \Auth::user()->school_id)
            ->where('employee_payrolls.session_id',$getCurrentSession->id)
            ->where('employee_payrolls.employee_id',$input['payroll_employee_id'])
            ->where('employee_payrolls.year',$input['payroll_employee_year'])
            ->where('employee_payrolls.month',$getCurrentMonthName->month)
            ->leftJoin('teacher','teacher.id','=','employee_payrolls.employee_id')
            ->leftJoin('users','teacher.user_id','=','users.id')
            ->select(
                'employee_payrolls.*',
                'teacher.name',
                'users.username',
                'teacher.user_id'
            )
            ->first();

        $getDeductionPercent = \DB::table('salary_deductions')
            ->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();

        $pdf = \PDF::loadView('users.payroll.pdf.invoice_result',compact('getSchoolName','getEmployeeDetail','getPayrollDetail','getCurrentMonthName','getBonusValue','getProfTax','getDeductionPercent','input'));
        return $pdf->download('payrollEmployeeReport.pdf');
    }

    /********************************************************************
     *                      ALLOWED LEAVE
     ********************************************************************/

    /** @ Add Allowed Leave @ **/
    public function add_allowed_leave()
    {
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();

        $input = \Request::all();
        if($input['submit_allowed_leave'] == 'allowed_leave')
        {
            $userError = ['leave_days' => 'Allowed Leave '
            ];
            $validator = \Validator::make($input, [
                'leave_days' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                $check = \DB::table('allowed_leaves')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    //->where('allowed_leave',$input['leave_days']) updated 16-3-2018
                    ->first();
                if(!$check)
                {
                    $insertLeave = \DB::table('allowed_leaves')
                        ->insert([
                            'school_id'=>\Auth::user()->school_id,
                            'session_id'=>$getCurrentSession->id,
                            'allowed_leave'=>$input['leave_days']
                        ]);
                    if($insertLeave)
                    {
                        $input['success']='  Allowed Leave  Inserted Successfully !!!';
                    }
                    else
                    {
                        $input['error']='  Allowed Leave has not been Inserted !!!';
                    }
                }
                else
                {
                    $input['error']='  Allowed Leave Already Exist  !!!';
                }
                return \Redirect::back()->withInput($input);
            }
        }
        $getAllowedLeave = \DB::table('allowed_leaves')
            ->where('school_id',\Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)->get();
        return view('users.payroll.add_allowed_leave',compact('getAllowedLeave'));
    }

    /** @ Delete Allowed Leave @ **/
    public function delete_allowed_leave($id)
    {
        if($id !='')
        {
            $deleteAllowedLeave =  \DB::table('allowed_leaves')
                ->where('id', $id)->delete();
            if($deleteAllowedLeave)
            {
                $msg['success'] = '  Allowed Leave Deleted Successfully !!! ';
            }
            else
            {
                $msg['error'] = ' Error in Deleting Allowed Leave !!! ';
            }

            return \Redirect::back()->withInput($msg);
        }
    }

    /********************************************************************
     *                      BONUS MODULE
     ********************************************************************/

    /** @ Add Bonus @ **/
    public function add_bonus_payroll()
    {
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();

        $input = \Request::all();
        if($input['submit_bonus'] == 'bonus')
        {
            $userError = ['special_date' => 'Date ',
                'start' => 'Start Time',
                'end' => ' End Time',
                'bonus' => 'Bonus ',
                'reason' => 'Note '
            ];
            $validator = \Validator::make($input, [
                'special_date' => 'required',
                'start' => 'required',
                'end' => 'required',
                'bonus' => 'required|numeric',
                'reason' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                //return $input;
                /*$holiday = new Holiday();
                $is_holiday = $holiday->is_holiday($input['special_date']);
                if ($is_holiday)
                {
                    $input['error'] = '  It is an Holiday !!! ';
                    return \Redirect::back()->withInput($input);
                }*/
                $check = \DB::table('salary_bonuses')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    ->where('date',$input['special_date'])
                    ->first();
                if(!$check)
                {
                    $insertBonus = \DB::table('salary_bonuses')
                        ->insert([
                            'school_id'=>\Auth::user()->school_id,
                            'session_id'=>$getCurrentSession->id,
                            'date'=>$input['special_date'],
                            'start'=>$input['start'],
                            'end'=>$input['end'],
                            'bonus'=>$input['bonus'],
                            'reason'=>strtolower($input['reason'])
                        ]);
                    if($insertBonus)
                    {
                        $input['success']='  Overtime Detail Inserted Successfully !!!';
                    }
                    else
                    {
                        $input['error']='   Detail has not been Inserted !!!';
                    }
                }
                else
                {
                    $input['error']='  Overtime Detail Already Exist For Date  '.date('d-m-Y',strtotime($input['special_date'])).' !!!';
                }
                return \Redirect::back()->withInput($input);
            }
        }

        $getBonus = \DB::table('salary_bonuses')->where('school_id',\Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->get();

        return view('users.payroll.addBonus',compact('getBonus'));
    }

    /** @ View edit page for bonus @ **/
    public function edit_bonus($id)
    {
        if($id !='')
        {
            $getCurrentSession = \DB::table('session')
                ->where('school_id',\Auth::user()->school_id)
                ->where('active',1)->first();
            $getBonus =\DB::table('salary_bonuses')
                ->where('session_id',$getCurrentSession->id)
                ->where('id', $id)->first();
        }
        return view('users.payroll.edit_bonus',compact('getBonus'));
    }

    /** @ Update OverTime Values @ **/
    public function update_bonus()
    {
        $input = \Request::all();
        //return $input;
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        if($input['update_bonus'] == 'updating')
        {
            //return $input;
            $userError = ['special_date' => 'Date ',
                'start' => 'Start time',
                'end' => ' End Time',
                'bonus' => 'Bonus ',
                'reason' => 'Reason '
            ];
            $validator = \Validator::make($input, [
                'special_date' => 'required',
                'start' => 'required',
                'end' => 'required',
                'bonus' => 'required|numeric',
                'reason' => 'required'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
               /* $check = \DB::table('bonus')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    ->where('date',$input['special_date'])
                    ->first();*/
                $updateBonus = \DB::table('salary_bonuses')
                      ->where('id',$input['bonus_id'])
                      ->update([
                          'school_id'=>\Auth::user()->school_id,
                          'session_id'=>$getCurrentSession->id,
                          'start'=>$input['start'],
                          'end'=>$input['end'],
                          'bonus'=>$input['bonus'],
                          'reason'=>strtolower($input['reason'])
                      ]);
                //dd($updateBonus);
                if($updateBonus)
                {
                    $input['success']='  OverTime Detail Updated Successfully !!!';
                }
                else
                {
                    $input['error']='  You Should do any modification to update it !!!';
                }
                return \Redirect::back()->withInput($input);
            }
        }
    }

    /** @ Delete Bonus Value @ **/
    public function delete_bonus($id)
    {
        if($id !='')
        {
            $getCurrentSession = \DB::table('session')
                ->where('school_id',\Auth::user()->school_id)
                ->where('active',1)->first();
            $getDate = \DB::table('salary_bonuses')
                ->where('session_id',$getCurrentSession->id)
                ->where('id', $id)->first();
            $deleteBonus =  \DB::table('salary_bonuses')
                ->where('id', $id)->delete();
            if($deleteBonus)
            {
                $msg['success'] = '  OverTime Deleted Successfully For !!! '. ucwords($getDate->date);
            }
            else
            {
                $msg['error'] = ' Error in Deleting  !!! ';
            }

            return \Redirect::back()->withInput($msg);
        }
    }

    /********************************************************************
     *                      DEDUCTION MODULE
     ********************************************************************/

    /** @ View Add Deduction Page @ **/
    public function get_deduction()
    {
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        $getDeduction = \DB::table('salary_deductions')->where('school_id',\Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->orderBy('created_at','desc')
            ->get();
        return view('users.payroll.deducation',compact('getDeduction'));
    }

    /**@ Post Deduction Value @ **/
    public function post_deduction_percentage()
    {
        $input = \Request::all();
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        if($input['submit_deduction'] == 'deduction')
        {
            $userError = ['deduction' => 'Deduction Type ',
                'percentage' => 'Deduction Percentage'
            ];
            $validator = \Validator::make($input, [
                'deduction' => 'required',
                'percentage' => 'required|integer|between:1,100'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                $check = \DB::table('salary_deductions')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    ->where('deduction_type',$input['deduction'])
                    ->first();
                if(!$check)
                {
                    $insertDeduction = \DB::table('salary_deductions')
                        ->insert([
                            'school_id'=>\Auth::user()->school_id,
                            'session_id'=>$getCurrentSession->id,
                            'deduction_type'=>strtolower($input['deduction']),
                            'deduction_percentage'=>$input['percentage'],
                            'date'=>''
                        ]);
                    if($insertDeduction)
                    {
                        $input['success']='Deduction Type Added Successfully  !!!';
                    }
                }
                else
                {
                    $input['error']='Deduction Type Already Exist For this Date !!!';
                }
            }
        }
        return \Redirect::back()->withInput($input);
    }

    /** @ View Edit Page for Deduction @ **/
    public function edit_deduction($id)
    {
        if($id !='')
        {
            $getCurrentSession = \DB::table('session')
                ->where('school_id',\Auth::user()->school_id)
                ->where('active',1)->first();
            $getDeduction =\DB::table('salary_deductions')
                ->where('session_id',$getCurrentSession->id)
                ->where('id', $id)->first();

        }
        return view('users.payroll.edit_deducation',compact('getDeduction'));
    }

    /** @ Update Deduction Type @ **/
    public function update_deduction_percentage()
    {
        $input =\Request::all();
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        if($input['update_deduction'] == 'updated')
        {
            $userError = ['deduction' => 'Deduction Type ',
                'percentage' => 'Deduction Percentage'
            ];
            $validator = \Validator::make($input, [
                'deduction' => 'required',
                'percentage' => 'required|integer|between:1,100'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                /*$check = \DB::table('deduction_percentage')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    ->where('deduction_type',$input['deduction'])
                    ->first();
                if(!$check)
                {*/
                    $updateDeduction = \DB::table('salary_deductions')
                        ->where('id',$input['deduction_id'])
                        ->update([
                            //'deduction_type'=>strtolower($input['deduction']),
                            'deduction_percentage'=>$input['percentage']
                        ]);
                    if($updateDeduction)
                    {
                        $input['success']='  Deduction Type Added Successfully  !!!';
                    }
                    else
                    {
                        $input['error'] = '  You Should edit or modify the field to Update !!1';
                    }
                /*}
                else
                {
                    $input['error']='Deduction Type Already Exist !!!';
                }*/
            }
        }
        return \Redirect::back()->withInput($input);
    }

    /** @ Delete Deduction Type @ **/
    public function delete_deduction($id)
    {
        if($id !='')
        {
            $getCurrentSession = \DB::table('session')
                ->where('school_id',\Auth::user()->school_id)
                ->where('active',1)->first();
            $getType = \DB::table('salary_deductions')
                ->where('session_id',$getCurrentSession->id)
                ->where('id', $id)->first();
            $deleteDeduction =  \DB::table('salary_deductions')
                ->where('id', $id)->delete();
            if($deleteDeduction)
            {
                $msg['success'] = ucwords($getType->deduction_type). '  Deduction Type Deleted Successfully  !!! ';
            }
            else
            {
                $msg['error'] = ' Error in Deleting Deduction Type !!! ';
            }

            return \Redirect::back()->withInput($msg);
        }
    }

    /********************************************************************
     *                      PROFESSIONAL TAX MODULE
     ********************************************************************/

    /** @ View & Add Professional Tax @ **/
    public function add_professional_tax()
    {
        $input = \Request::all();
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        if($input['submit_pt'] == 'submitting')
        {
            $userError = ['from_value' => 'Low Salary ',
                'to_value' => 'High Salary',
                'ptax' => 'Tax'
            ];
            $validator = \Validator::make($input, [
                'from_value' => 'required|numeric',
               // 'to_value' => 'required|numeric|greater_than_field:from_value',
                'to_value' => 'required|numeric',
                'ptax' => 'required|numeric'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                $checkTax = \DB::table('professional_taxes')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    ->where('from_salary',$input['from_value'])
                    ->where('to_salary',$input['to_value'])
                    ->first();
                if(!$checkTax)
                {
                    if($input['from_value'] > $input['to_value'])
                    {
                        $msg['error'] = ' High Value Field Should Be greater than Low Value Field !!! ';
                        return \Redirect::back()->withInput($msg);
                    }
                    $insertTax =\DB::table('professional_taxes')->insert([
                        'school_id'=>\Auth::user()->school_id,
                        'session_id'=>$getCurrentSession->id,
                        'from_salary'=>$input['from_value'],
                        'to_salary'=>$input['to_value'],
                        'tax'=>$input['ptax'],
                    ]);
                    if($insertTax)
                    {
                        $msg['success'] = ' Professional Tax Added Successfully !!! ';
                    }
                    else
                    {
                        $msg['error'] = ' Error in Inserting Professional Tax !!! ';
                    }
                }
                return \Redirect::back()->withInput($msg);

            }
        }
        $getProfessionalTax = \DB::table('professional_taxes')->where('school_id',\Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->get();
        return view('users.payroll.add_professional_tax',compact('getProfessionalTax'));
    }

    /** @ Edit Professional Tax @ **/
    public function edit_professional_tax($id)
    {
        $getCurrentSession = \DB::table('session')
            ->where('school_id',\Auth::user()->school_id)
            ->where('active',1)->first();
        $getProfTax =\DB::table('professional_taxes')->where('school_id',\Auth::user()->school_id)
            ->where('session_id',$getCurrentSession->id)
            ->where('id',$id)->first();

        $input = \Request::all();
        if($input['submit_pt'] == 'updating')
        {
            $userError = ['from_value' => 'Low Salary ',
                'to_value' => 'High Salary',
                'ptax' => 'Tax'
            ];
            $validator = \Validator::make($input, [
                'from_value' => 'required|numeric',
                // 'to_value' => 'required|numeric|greater_than_field:from_value',
                'to_value' => 'required|numeric',
                'ptax' => 'required|numeric'
            ], $userError);
            $validator->setAttributeNames($userError);
            if ($validator->fails())
            {
                return \Redirect::back()->withErrors($validator)->withInput($input);
            }
            else
            {
                if($input['from_value'] > $input['to_value'])
                {
                    $msg['error'] = ' High Value Field Should Be greater than Low Value Field !!! ';
                    return \Redirect::back()->withInput($msg);
                }
                $updateTax =\DB::table('professional_taxes')
                    ->where('id',$input['prof_id'])
                    ->update([
                    'from_salary'=>$input['from_value'],
                    'to_salary'=>$input['to_value'],
                    'tax'=>$input['ptax'],
                ]);
                if($updateTax)
                {
                    $msg['success'] = ' Professional Tax Updated Successfully !!! ';
                }
                else
                {
                    $msg['error'] = ' You Should Edit or Modify a field to Update !!! ';
                }
                return \Redirect::back()->withInput($msg);

            }
        }
        return view('users.payroll.edit_professional_tax',compact('getProfTax'));
    }

    /** @  Delete Professional Tax @ **/
    public function delete_professional_tax($id)
    {
        if($id !='')
        {
            $deleteProfTax =  \DB::table('professional_taxes')
                ->where('id', $id)->delete();
            if($deleteProfTax)
            {
                $msg['success'] = '  Professional Tax Deleted Successfully  !!! ';
            }
            else
            {
                $msg['error'] = ' Error in Deleting Professional Tax !!! ';
            }

            return \Redirect::back()->withInput($msg);
        }
    }

}
