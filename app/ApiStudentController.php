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

class ApiStudentController extends Controller
{
    protected $user; 
    protected $student;

    function __construct(){
        try{
            $this->user = JWTAuth::parseToken()->authenticate();
            $this->student = Students::where('user_id', \Auth::user()->id)->first();
        }
        catch(\Exception$e){}
    }

    public function dashHome()
    {
        $student = \DB::table('student')->where('id', $this->student->id)
                            ->select
                            (
                                'registration_no',
                                'avatar',
                                \DB::RAW("(select name from teacher where class=student.class_id and section=student.section_id LIMIT 1) as teacherName"),
                                \DB::RAW("(select title from push_notification where role='".$this->user->type."' and role_id=student.id LIMIT 1) as notification_title"),
                                \DB::RAW("(select description from push_notification where role='".$this->user->type."' and role_id=student.id LIMIT 1) as notification_description"),
                                \DB::RAW("(select image from push_notification where role='".$this->user->type."' and role_id=student.id LIMIT 1) as notification_image")
                            )
                            ->first();
                            // dd($this->student->id, date('m'));
        $attendance = \DB::table('attendance')->where('date', 'LIKE', '%'.date('m').'%')->where('student_id', $this->student->id)->select('id', 'attendance', 'date')->get();
        return api(['attendance' => $attendance, 'student' => $student]);
    }

    public function getHomework($platform, $date)
    {
        if(preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$date))
        {
            $homework = Homework::where('homework.date', $date)
                        ->where('homework.class_id', $this->student->class_id)
                        ->where('homework.section_id', $this->student->section_id)
                        ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                        ->select('homework.id', 'homework.description', 'homework.date', 'homework.image', 'homework.pdf', 'subject.subject')
            ->get();
            return api(['data' => $homework]);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);   
        }
    }

    public function getAttendanceDate($platform, $date)
    {
        if(preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$date))
        {
            $attendance = Attendance::where('date', $date)->where('student_id', $this->student->id)
                        ->select('attendance.id', 'attendance.attendance', 'attendance.remarks')
                        ->get();
            return api(['data' => $attendance]);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);   
        }
    }

    public function getAttendanceMonth($platform, $month)
    {
        if (preg_match("/^(0[1-9]|1[0-2])$/",$month))
        {
            $sessionDate = $month.'-'.date('Y');
            $attendance = [];
            for ($i=1; $i <=31 ; $i++) { 
                if($i<9)
                {
                    $dd = '0'.$i;
                }
                else
                {
                    $dd = $i;
                }
                $atten = Attendance::where('student_id', $this->user->school_id)
                        ->where('school_id', $this->user->school_id)
                        ->where('date', 'LIKE', '%'.$dd.'-'.$sessionDate)
                            ->first();
                if(!$atten)
                {
                    array_push($attendance, ['date'=>$dd, 'p'=>'', 'l'=>'', 'a'=>'']);
                }
                else
                {
                    if($atten->attendance == 'P')
                    {
                        array_push($attendance, ['date'=>$dd, 'p'=>$atten->attendance, 'l'=>'', 'a'=>'']);    
                    }

                    if($atten->attendance == 'L')
                    {
                        array_push($attendance, ['date'=>$dd, 'p'=>'', 'l'=>$atten->attendance, 'a'=>'']);    
                    }

                    if($atten->attendance == 'A')
                    {
                        array_push($attendance, ['date'=>$dd, 'p'=>'', 'l'=>'', 'a'=>$atten->attendance]);    
                    }
                    
                }
            }
            return api(['data' => $attendance]);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm. Year is according to current session.']);
        }
    }
    public function getAttendanceMonthParent($platform, $student_id, $month)
    {
        if (preg_match("/^(0[1-9]|1[0-2])$/",$month))
        {
            $sessionDate = $month.'-'.date('Y');
            $attendance = [];
            for ($i=1; $i <=31 ; $i++) { 
                if($i<9)
                {
                    $dd = '0'.$i;
                }
                else
                {
                    $dd = $i;
                }
                $atten = Attendance::where('student_id', $student_id)
                        ->where('school_id', $this->user->school_id)
                        ->where('date', 'LIKE', '%'.$dd.'-'.$sessionDate)
                            ->first();
                if(!$atten)
                {
                    array_push($attendance, ['date'=>$dd, 'p'=>'', 'l'=>'', 'a'=>'']);
                }
                else
                {
                    if($atten->attendance == 'P')
                    {
                        array_push($attendance, ['date'=>$dd, 'p'=>$atten->attendance, 'l'=>'', 'a'=>'']);    
                    }

                    if($atten->attendance == 'L')
                    {
                        array_push($attendance, ['date'=>$dd, 'p'=>'', 'l'=>$atten->attendance, 'a'=>'']);    
                    }

                    if($atten->attendance == 'A')
                    {
                        array_push($attendance, ['date'=>$dd, 'p'=>'', 'l'=>'', 'a'=>$atten->attendance]);    
                    }
                    
                }
            }
            return api(['data' => $attendance]);
        }
        else
        {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm. Year is according to current session.']);
        }
    }

    public function getFeedback()
    {
        $feedbacks = Feedback::where('student_id', $this->student->id)->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')->orderBy('feedback.id', 'DESC')
                ->select('feedback.id', 'teacher.name as teacherName', 'feedback.feedback', 'feedback.date')
                ->orderBy('id', 'DESC')
                ->get();
        return \api::success(['data' => $feedbacks]);
    }
    
    public function getResult($platform, $examid, $month)
    {
        // $results = Exam::with('ManyResults')->get();
        // foreach($results as $key => $result)
        // {
        //  if($result->ManyResults == '[]')
        //  {
        //      unset($results[$key]);
        //  }
        // }
        // return api(['data' => $results]);

        $results = Result::where('result.exam_type_id', $examid)
                        ->where('result.student_id', $this->student->id)
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
            // dd($result);

        $total = Result::where('result.exam_type_id', $examid)
                        ->where('result.student_id', $this->student->id)
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
        if(count($results)>0 and $results)
        {
            // $view = \View::make('report', compact('total', 'results'));

            // $html = $view->render();
            // \PDF::loadHtml($html, 'A4', 'portrait')->save('report/report'.$this->student->id.'.pdf');
            // $pdfReport = config('constants.share_link').$this->student->id.'.pdf';

            \Excel::create('report'.$id, function($excel) use ($total, $results) {

                $excel->sheet('Excel sheet', function($sheet) use ($total, $results) {
                    $sheet->loadView('report')->with('total',$total)
                                                 ->with('results',$results);
                    $sheet->setOrientation('portrait');
                });

            })->store('pdf', 'report');
            $pdfReport = config('constants.share_link').'/report'.$this->student->id.'.pdf';
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
}