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

class ApiParentController extends Controller
{
    protected $user; 
    protected $parent;

    function __construct(){
        try{
            $this->user = JWTAuth::parseToken()->authenticate();
            $this->parent = StuParent::where('user_id', \Auth::user()->id)->first();
            
        }
        catch(\Exception$e){}
    }

    public function getStudents()
    {
        $students = [];
        $getStudents = Students::where('student.parent_id', $this->parent->id)
                    ->where('student.school_id', $this->user->school_id)
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')
                    ->leftJoin('session', 'student.session_id', '=', 'session.id')
                    ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                    ->select
                    (
                        'student.id',
                        'student.name',
                        'student.avatar',
                        'student.roll_no',
                        'student.pick_time',
                        'student.drop_time',
                        'student.registration_no',
                        'parent.name as father',
                        'parent.mother',
                        'parent.mobile',
                        'parent.email',
                        'parent.address',
                        'parent.city',
                        'parent.pin_code',
                        'class.class',
                        'class.id as class_id',
                        'section.section',
                        'section.id as section_id',
                        'section.subjects',
                        'session.session',
                        'student.dob',
                         'student.bus_id',   
                        \DB::RAW("(select count(*) from feedback where student_id=student.id and feedback_by='teacher' and view_status='0') as fcount"),
                        \DB::RAW("(select count(*) from attendance where student_id=student.id and attendance='P' and date LIKE '%".date('m-Y')."%') as present"),
                        \DB::RAW("(select count(*) from attendance where student_id=student.id and attendance='A' and date LIKE '%".date('m-Y')."%') as absent"),
                        \DB::RAW("(select count(*) from attendance where student_id=student.id and attendance='L' and date LIKE '%".date('m-Y')."%') as lvs"),
                        \DB::RAW("(select count(*) from attendance where student_id=student.id and attendance='M' and date LIKE '%".date('m-Y')."%') as medical"),
                        \DB::RAW("(select count(*) from attendance where student_id=student.id and attendance='HF' and date LIKE '%".date('m-Y')."%') as half_day"),
                        \DB::RAW("(select title from push_notification where role='student' and role_id=student.id ORDER BY id DESC LIMIT 1) as notification_title"),
                        \DB::RAW("(select description from push_notification where role='student' and role_id=student.id ORDER BY id DESC LIMIT 1) as notification_description"),
                        \DB::RAW("(select count(*) from feedback where student_id=student.id and feedback_by='teacher') as fcount"),
                        \DB::RAW("(select image from push_notification where role='student' and role_id=student.id ORDER BY id DESC LIMIT 1) as notification_image")
                    )
                    ->get();

        foreach($getStudents as $stu)
        {
            $subjects = \DB::table('subject')->whereIn('id', json_decode($stu->subjects))->select('subject')->get();

            array_push($students, array(
                            'id' => $stu->id,
                            'name' => $stu->name,
                            'image' => $stu->avatar, 
                            'roll_no' => $stu->roll_no, 
                            'registration_no' => $stu->registration_no,
                            'father_name' => $stu->father,
                            'mother_name' => $stu->mother,
                            'mobile' => $stu->mobile,
                            'email' => $stu->email,
                            'address' => $stu->address,
                            'city' => $stu->city,
                            'pinCode' => $stu->pin_code,
                            'pick_time' => $stu->pick_time,
                            'drop_time' => $stu->drop_time,
                            'class' => $stu->class,
                            'section' => $stu->section,
                            'session' => $stu->session,
                            'teacherName' => $stu->teacherName,
                            'present' => $stu->present,
                            'absent' => $stu->absent,
                            'leave' => $stu->lvs,
                            'medical' => $stu->medical,
                            'half_day' => $stu->half_day,
                            'bus_id'=>$stu->bus_id,
                            'notification_title' => $stu->notification_title,
                            'notification_description' => $stu->notification_description,
                            'notification_image' => $stu->notification_image,
                            'subjects' => $subjects,
                             'fcount'=>$stu->fcount
                        ));
        }
        return \api::success(['data' => $students]);
    }

    // changes done by parthiban 30-11-2017
    // public function postLeave()
    // {
    //     $request = \Request::all();
    //     $userError = ['student_id' => 'Student Id', 'leave_from' => 'Leave From Date in dd-mm-yyyy', 'leave_to' => 'Leave To Date in dd-mm-yyyy'];
    //     $validator = \Validator::make($request, [
    //             'student_id'=>'required',
    //             'leave_from'=>'required|date_format:d-m-Y',
    //             'leave_to'=>'required|date_format:d-m-Y'
    //         ], $userError);
    //     $validator->setAttributeNames($userError);
    //     if($validator->fails())
    //     {
    //         return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
    //     }
    //     else
    //     {
    //         $leave = Leave::where('student_id', $request['student_id'])->where('to_leave', $request['leave_to'])->where('from_leave', $request['leave_from'])->first();

    //         if($leave)
    //         {
    //             return api()->notValid(['errorMsg'=>'Leave Request is already submitted']);
    //         }
    //         else
    //         {
    //             if(isset($request['remarks']))
    //             {
    //                 $remarks = $request['remarks'];
    //             }
    //             else
    //             {
    //                 $remarks = '';
    //             }
    //             $attendance_session = array(
    //                 'am'=>$request['attendance_session_am'],
    //                 'pm'=>$request['attendance_session_pm']
    //                 );
    //             Leave::insert([
    //                 'student_id' => $request['student_id'],
    //                 'from_leave' => $request['leave_from'],
    //                 'to_leave' => $request['leave_to'],
    //                 'user_id' => $this->user->id,
    //                 'status' => 'process',
    //                 'by_request' => 'parent',
    //                 'attendance_session' => json_encode($attendance_session),
    //                 'remarks' => $remarks,
    //                 'school_id' => $this->user->school_id
    //             ]);
    //             return api(['data'=>'Leave Request is Submitted Successfully']);
    //         }
    //     }
    // }

    public function postLeave()
    {
        $request = \Request::all();
        $userError = ['student_id' => 'Student Id', 'leave_from' => 'Leave From Date in dd-mm-yyyy', 'leave_to' => 'Leave To Date in dd-mm-yyyy'];
        $validator = \Validator::make($request, [
                'student_id'=>'required',
                'leave_from'=>'required|date_format:d-m-Y',
                'leave_to'=>'required|date_format:d-m-Y'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            $leave = Leave::where('student_id', $request['student_id'])->whereDate('to_leave','=',date('Y-m-d',strtotime($request['leave_to'])))->whereDate('from_leave','=',date('Y-m-d',strtotime( $request['leave_from'])))->first();

            if($leave)
            {
                return api()->notValid(['errorMsg'=>'Leave Request is already submitted']);
            }
            else
            {
                if(isset($request['remarks']))
                {
                    $remarks = $request['remarks'];
                }
                else
                {
                    $remarks = '';
                }
                $attendance_session = array(
                    'am'=>$request['attendance_session_am'],
                    'pm'=>$request['attendance_session_pm']
                    );
                Leave::insert([
                    'student_id' => $request['student_id'],
                    'from_leave' => date('Y-m-d',strtotime($request['leave_from'])),
                    'to_leave' => date('Y-m-d',strtotime($request['leave_to'])),
                    'user_id' => $this->user->id,
                    'status' => 'process',
                    'by_request' => 'parent',
                    'attendance_session' => json_encode($attendance_session),
                    'remarks' => $remarks,
                    'school_id' => $this->user->school_id
                ]);
                return api(['data'=>'Leave Request is Submitted Successfully']);
            }
        }
    }
        
  //29-09-2017
    public function getHomework($platform, $id, $date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
        {
            $sessionDate = $date.'-'.date('Y');
            $stu = Students::where('id', $id)->first();
            if(!$stu)
                return \api::notValid(['errorMsg' => 'Student Id is Invalid']);
                $homework = \DB::table('homework')->where('homework.class_id', $stu->class_id)
                            ->where('homework.section_id', $stu->section_id)
                            ->where('homework.date', $sessionDate)
                            ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                            ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
                            ->select('homework.id', 'homework.description', 'homework.image', 'homework.pdf', 'subject.subject', 'homework.date', 'teacher.name as teacherName')
                            ->get();
                //update by mari 29-09-2017
                if(count($homework)>0){
                    foreach($homework as $key=>$hw_value){
                        $update_view_status=\DB::table('homework')->where('class_id', $stu->class_id)->where('date', $sessionDate)
                        ->update(['parent_v_status'=>'1']);
                        }
                    }

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
             return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm']);
        }
    }
// changes by mari 29.09.2017
    
    public function getHomeworkCount($platform, $id, $date)
    {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
        {
            $sessionDate = $date.'-'.date('Y');
            $stu = Students::where('id', $id)->first();
            if(!$stu)
                return \api::notValid(['errorMsg' => 'Student Id is Invalid']);
                $homework = \DB::table('homework')->where('homework.class_id', $stu->class_id)
                            ->where('homework.section_id', $stu->section_id)
                            ->where('homework.date', $sessionDate)
                            ->where('homework.parent_v_status','=','0')
                            ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                            ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
                            ->select('homework.id', 'homework.description', 'homework.image', 'homework.pdf', 'subject.subject', 'homework.date', 'teacher.name as teacherName')
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
             return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm']);
        }
    }

    // public function getHomework($platform, $id, $date)
    // {
    //     if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])$/",$date))
    //     {
    //         $sessionDate = $date.'-'.date('Y');
    //         $stu = Students::where('id', $id)->first();
    //         if(!$stu)
    //             return \api::notValid(['errorMsg' => 'Student Id is Invalid']);
    //             $homework = \DB::table('homework')->where('homework.class_id', $stu->class_id)
    //                         ->where('homework.section_id', $stu->section_id)
    //                         ->where('homework.date', $sessionDate)
    //                         ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
    //                         ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
    //                         ->select('homework.id', 'homework.description', 'homework.image', 'homework.pdf', 'subject.subject', 'homework.date', 'teacher.name as teacherName')
    //                         ->get();
    //             $data = array(
    //                 'name' => $stu->name,
    //                 'roll_no' => $stu->roll_no,
    //                 'homework' => $homework
    //                     );
    //         if(!$homework)
    //             return \api::notValid(['data' => 'No Rows Found!!!']);
    //         return \api(['data' => $data]);
    //     }
    //     else
    //     {
    //          return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm']);
    //     }
    // }

    public function getAttendance($platform, $id, $date)
    {
        if (preg_match("/^(0[1-9]|1[0-2])$/",$date))
        {
            $student = \DB::table('student')->where('id', $id)->first();
            if(!$student)
                return \api::notValid(['errorMsg' => 'Invalid Parameter']);
            $sessionDate = $date.'-'.date('Y');
            $atten = Attendance::where('student_id', $student->id)->where('date', 'LIKE', '%'.$sessionDate.'%')
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
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm']);
        }
    }

    //  changes done by mari
    public function getFeedbackCount($platform,$parent_id){

        $stuObj = Students::where('parent_id',$parent_id)->first();
        $feedback=Feedback::where('student_id', $stuObj->id)
                    ->where('view_status','=','0')->where('feedback.feedback_by','=','teacher')
                    ->count();
        return \api::success(['data' => $feedback]);

    }

    public function getFeedback($platform, $student_id, $teacher_id)
    {
        $student = \DB::table('student')->where('id', $student_id)->first();
        if(!$student)
                return \api::notValid(['errorMsg' => 'Student Not Found!!!']);

        $teacher = \DB::table('teacher')->where('id', $teacher_id)->first();
        if(!$teacher)
                return \api::notValid(['errorMsg' => 'Teacher Not Found!!!']);
        //     $timestamp = time();
        // $date=array();
        // for ($i = 0 ; $i < 7 ; $i++) {
        //     array_push($date,date('d-m-Y', $timestamp));
        //     $timestamp -= 24 * 3600;
        //     }
        $feedbacks = Feedback::where('student_id', $student_id)
                    ->where('teacher_id', $teacher_id)
                    //->whereIn('date',$date)
                    ->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')
                    ->leftJoin('student', 'feedback.student_id', '=', 'student.id')
                    ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                   // ->where('feedback.feedback_by','=','teacher')
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
                        'parent.name as parent_name'
                    )
                    ->orderBy('feedback.id', 'DESC')
                    ->get();
                    $update_stauts=Feedback::where('student_id',$student_id)
                               ->where('teacher_id',$teacher_id)
                               ->where('feedback_by','=','teacher')
                               ->update(['view_status' => 1]); 
        return \api::success(['data' => $feedbacks]);

        //return \api::success(['data' => $feedbacks]);
    }

    // changes done by parthiban 19-11-2017(sunday)

    // public function getTimeTable($platform, $id)
    // {
    //     $student = \DB::table('student')->where('id', $id)->first();
    //     if(!$student)
    //             return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    //     $timeTables = TimeTable::where('time-table.class_id', $student->class_id)->where('time-table.section_id', $student->section_id)
    //             ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
    //             ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
    //             ->select
    //             (
    //                 'time-table.id',
    //                 'subject.subject',
    //                 'teacher.name as teacherName',
    //                 'time-table.period',
    //                 'time-table.start_time',
    //                 'time-table.end_time',
    //                 'time-table.day'
    //             )
    //             ->orderBy('time-table.id', 'ASC')
    //             ->get();
    //     return \api::success(['data' => $timeTables]);
    // }

    public function getTimeTable($platform, $id)
    {
        $student = \DB::table('student')->where('id', $id)->first();
        if(!$student)
        return \api::notValid(['errorMsg' => 'Invalid Parameter']);

        $daysArray = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $finalArray = [];        
        $periods = ["1","2","3","4","5","6","7","8","9","10"];
        foreach ($daysArray as $k => $days) {
            foreach ($periods as $p => $period) {            
                $timeTablesForDays = TimeTable::where('time-table.class_id', $student->class_id)
                    ->where('time-table.section_id', $student->section_id)
                    ->where('day', $days)
                    ->where('period', $period)            
                    ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                    ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
                    ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
                    ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
                    ->select('time-table.id', 'time-table.period', 'time-table.day', 'time-table.start_time', 'time-table.end_time', 'subject.subject', 'class.class','section.section','teacher.name as teacherName')            
                    ->first();                
                $finalArray[$days][$period] = $timeTablesForDays;
            }
        }  

        return api(['data' => $finalArray]);
    }     
    
    public function getResult($platform, $id, $examid, $month)
    {
        // $results = Exam::with(['ManyResultsParent' => function($query) use ($id)
        // {
        //  $query->where('student_id', $id);
        // }])->get();
        // foreach($results as $key => $result)
        // {
        //  if($result->ManyResultsParent == '[]')
        //  {
        //      unset($results[$key]);
        //  }
        // }
        // return api(['data' => $results]);    
        $results = Result::where('result.exam_type_id', $examid)
                ->where('result.student_id', $id)
                ->where('result.month', 'LIKE', '%'.$month.'%')
                        ->leftJoin('subject', 'result.subject_id', '=', 'subject.id')
                        ->leftJoin('teacher', 'result.teacher_id', '=', 'teacher.id')
                        ->leftJoin('exam', 'result.exam_type_id', '=', 'exam.id')
                        ->select
                        (
                            'result.id', 
                            'exam.exam_type', 
                            'result.month', 
                            'result.date', 
                            'result.max_marks', 
                            'result.pass_marks', 
                            'result.obtained_marks',
                            'teacher.name as teacherName',
                            'subject.subject',
                            'result.result',
                            'result.grade'
                        )
            ->get();
            $total = Result::where('result.exam_type_id', $examid)
                        ->where('result.student_id', $id)
                        ->where('result.month', 'LIKE', '%'.$month.'%')
                        ->leftJoin('exam', 'result.exam_type_id', '=', 'exam.id')
                        ->select
                        (
                            \DB::raw('sum(result.max_marks) AS total_marks'),
                            \DB::raw('sum(result.pass_marks) AS pass_marks'),
                            \DB::raw('sum(result.obtained_marks) AS obtained_marks'),
                            'exam.exam_type',
                            'result.month'
                        )
                        ->first();
                        Result::where('student_id', $id)
                            ->where('month',$month)
                            ->update(['view_status' => 1]);
        if(count($results)>0 and $results)
        {
            // $view = \View::make('report', compact('total', 'results'));

            // $html = $view->render();
            // \PDF::loadHtml($html, 'A4', 'portrait')->save('report/report'.$id.'.pdf');
            // $pdfReport = config('constants.share_link').'/report'.$id.'.pdf';

            \Excel::create('report'.$id, function($excel) use ($total, $results) {

                $excel->sheet('Excel sheet', function($sheet) use ($total, $results) {
                    $sheet->loadView('report')->with('total',$total)
                                                 ->with('results',$results);
                    $sheet->setOrientation('portrait');
                });

            })->store('pdf', 'report');
            $pdfReport = config('constants.share_link').'/report'.$id.'.pdf';

            return api(['data' => $results, 'total' => $total, 'url' => $pdfReport]);
        }
        else
        {
            return api::notFound(['errorMsg' => 'Result Not Found!!!']);
        }
    }

    public function getNotice()
    {
        $notices = \DB::table('notice')->where('type', 'student')->orderBy('id', 'DESC')->get();
        return api(['data' => $notices]);
    }
    
    public function getEmployee($platform, $id)
    {
        $student = Students::where('id', $id)->first();
        $teachers = Employee::where('class', $student->class_id)->where('section', $student->section_id)->select('id', 'name', 'mobile', 'email', 'avatar')->get();
        return \api(['data' => $teachers]);
    }

    public function postFeedback()
    {
        $request = \Request::all();
        $userError = ['teacher_id' => 'Teacher Id', 'student_id' => 'Student Id', 'date' => 'Date in dd-mm-yyyy', 'feedback' => 'Feedback'];
        $validator = \Validator::make($request, [
                'teacher_id' => 'required|numeric',
                'student_id' => 'required|numeric',
                'date' => 'required|date_format:d-m-Y',
                'feedback' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            Feedback::insert([
                'school_id' => $this->user->school_id,
                'student_id' => $request['student_id'],
                'teacher_id' => $request['teacher_id'],
                'feedback' => $request['feedback'],
                'date' => $request['date'],
                'feedback_by' => 'parent',
                'view_status'=>'0'
            ]);
            return api(['data'=>'Feedback is Submitted Successfully']);
        }
    }

    public function getFeeStructure($platform, $student_id)
    {
        $student = Students::where('id', $student_id)->first();
        $amount = Amount::where('class_id', $student->class_id)->first();
        
    }
}