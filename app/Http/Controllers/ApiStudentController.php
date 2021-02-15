<?php

namespace App\Http\Controllers;

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
use DateTime;
use DatePeriod;
use DateInterval;

class ApiStudentController extends Controller {

    protected $user;
    protected $student;

    function __construct() {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            $this->student = Students::where('user_id', \Auth::user()->id)->first();
        } catch (\Exception$e) {
            
        }
    }

    public function dashHome() {
        $student = \DB::table('student')->where('id', $this->student->id)
                ->select
                        (
                        'registration_no', 'avatar', \DB::RAW("(select name from teacher where class=student.class_id and section=student.section_id LIMIT 1) as teacherName"), \DB::RAW("(select title from push_notification where role='" . $this->user->type . "' and role_id=student.id LIMIT 1) as notification_title"), \DB::RAW("(select description from push_notification where role='" . $this->user->type . "' and role_id=student.id LIMIT 1) as notification_description"), \DB::RAW("(select image from push_notification where role='" . $this->user->type . "' and role_id=student.id LIMIT 1) as notification_image")
                )
                ->first();
        // dd($this->student->id, date('m'));
        $attendance = \DB::table('attendance')->where('date', 'LIKE', '%' . date('m') . '%')->where('student_id', $this->student->id)->select('id', 'attendance', 'date')->get();
        return api(['attendance' => $attendance, 'student' => $student]);
    }

    // public function getHomework($platform, $date) {
    //     if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
    //         $homework = Homework::where('homework.date', $date)
    //                 ->where('homework.class_id', $this->student->class_id)
    //                 ->where('homework.section_id', $this->student->section_id)
    //                 ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
    //                 ->select('homework.id', 'homework.description', 'homework.date', 'homework.image', 'homework.pdf', 'subject.subject')
    //                 ->get();
    //         return api(['data' => $homework]);
    //     } else {
    //         return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
    //     }
    // }
    //29-09-2017
    public function getHomeworkCount($platform, $date) {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
            //console.log($date);
            $homework = Homework::where('homework.date', $date)
                    ->where('homework.class_id', $this->student->class_id)
                    ->where('homework.section_id', $this->student->section_id)
                    ->where('homework.student_v_status','=','0')
                    ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                    ->select('homework.id', 'homework.description', 'homework.date', 'homework.image', 'homework.pdf', 'subject.subject')
                    ->get();
            return api(['data' => $homework]);
        } else {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
        }
        //echo "vasu";
    }
      public function getHomework($platform, $date) {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
            $homework = Homework::where('homework.date', $date)
                    ->where('homework.class_id', $this->student->class_id)
                    ->where('homework.section_id', $this->student->section_id)
                    ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                    ->select('homework.id', 'homework.description', 'homework.date', 'homework.image', 'homework.pdf', 'subject.subject')
                    ->get();
          //  if(count($homework)>0){

                Homework::where('homework.date', $date)
                    ->where('homework.class_id', $this->student->class_id)
                    ->where('homework.section_id', $this->student->section_id)->update(['student_v_status'=>'1']);

            //}
            return api(['data' => $homework]);
        } else {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
        }
    }

    public function getAttendanceDate($platform, $date) {
        if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
            $attendance = Attendance::where('date', $date)->where('student_id', $this->student->id)
                    ->select('attendance.id', 'attendance.attendance', 'attendance.remarks')
                    ->get();
            return api(['data' => $attendance]);
        } else {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in dd-mm-yyyy']);
        }
    }


   //  public function getAttendanceMonth($platform, $month) {//by mari for v3
   //      if (preg_match("/^(0[1-9]|1[0-2])$/", $month)) {
   //          $sessionDate = '01-' . $month . '-' . date('Y');
   //          $dates = $this->get_inbetween_date($sessionDate, $sessionDate);
   //          $attendance = [];
   //          $passed_date = [];
			// $stu_admindate=\DB::table('student')->where('id','=',$this->student->id)
			// ->where('school_id','=',$this->user->school_id)->first();
			// $is_avail=strtotime($stu_admindate->date_of_admission);
			// $date_arraay=array();
   //                    //print_r($dates);
   //          foreach ($dates as $dat) {
			// 	$check_date=strtotime($dat);
			// 	if($is_avail>$check_date){
			// 		array_push($attendance,['date' => $dat,'am'=>'-','pm' =>'-']);
			// 	}
			// 	else{
			// 		$att_is_taken_am=\DB::table('attendance_status')
			// 		->where('school_id','=',$this->user->school_id)
			// 		->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
			// 		->where('section_id','=',$stu_admindate->section_id)
			// 		->where('attendance_session','=','am')->first();
					
			// 		$att_is_taken_pm=\DB::table('attendance_status')
			// 		->where('school_id','=',$this->user->school_id)
			// 		->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
			// 		->where('section_id','=',$stu_admindate->section_id)
			// 		->where('attendance_session','=','pm')->first();
					
			// 			$atten = Attendance::where('school_id', $this->user->school_id)
			// 			->where('date', $dat)
			// 			->where('student_id','=',$stu_admindate->id)
			// 			->where('attendance','!=','P')
			// 			->where('attendance_session','=','am')
			// 			->first();
						
			// 			$atten2 = Attendance::where('school_id', $this->user->school_id)
			// 			->where('date', $dat)
			// 			->where('student_id','=',$stu_admindate->id)
			// 			->where('attendance','!=','P')
			// 			->where('attendance_session','=','pm')
			// 			->first();
						
			// 		if(empty($att_is_taken_am)){
			// 			$at1="-";
			// 		}
			// 		else{
			// 			if(empty($atten)){
			// 				$at1="P";
			// 			}else if($atten->attendance=='L'){
			// 				$at1=$atten->attendance;
			// 			}else if($atten->attendance=='A'){
			// 				$at1=$atten->attendance;
			// 			}
			// 		}
			// 		if(empty($att_is_taken_pm)){
			// 			$at2="-";
			// 		}
			// 		else{
			// 			if(empty($atten2)){
			// 				$at2="P";
			// 			}else if($atten2->attendance=='L'){
			// 				$at2=$atten2->attendance;
			// 			}else if($atten2->attendance=='A'){
			// 				$at2=$atten2->attendance;
			// 			}
			// 		}
			// 		array_push($attendance,['date' => $dat,'am'=>$at1,'pm' =>$at2]);
			// 	}
   //          }
   //          return api(['data' => $attendance]);
   //      } else {
   //          return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm. Year is according to current session.']);
   //      }
   //  }
     public function getAttendanceMonth($platform, $month) {//
        if (preg_match("/^(0[1-9]|1[0-2])$/", $month)) {
            $sessionDate = '01-' . $month . '-' . date('Y');
            $dates = $this->get_inbetween_date($sessionDate, $sessionDate);
            $attendance = [];
            $passed_date = [];
            $stu_admindate=\DB::table('student')->where('id','=',$this->student->id)
            ->where('school_id','=',$this->user->school_id)->first();
            $is_avail=$stu_admindate->created_at;
            $date_arraay=array();
            foreach ($dates as $dat){
                $att_is_taken_am=\DB::table('attendance_status')
                    ->where('school_id','=',$this->user->school_id)
                    ->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
                    ->where('section_id','=',$stu_admindate->section_id)
                    ->where('attendance_session','=','am')->first();
                    
                $att_is_taken_pm=\DB::table('attendance_status')
                    ->where('school_id','=',$this->user->school_id)
                    ->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
                    ->where('section_id','=',$stu_admindate->section_id)
                    ->where('attendance_session','=','pm')->first();
                    
                $atten = Attendance::where('school_id', $this->user->school_id)
                    ->where('date', $dat)
                    ->where('student_id','=',$stu_admindate->id)
                    ->where('attendance','!=','P')
                    ->where('attendance_session','=','am')
                    ->first();
                        
                $atten2 = Attendance::where('school_id', $this->user->school_id)
                    ->where('date', $dat)
                    ->where('student_id','=',$stu_admindate->id)
                    ->where('attendance','!=','P')
                    ->where('attendance_session','=','pm')
                    ->first();
                        
                if(empty($att_is_taken_am)){
                    $at1="-";
                }
                else{
                    $am_update_time=$att_is_taken_am->created_at;
                    if($att_is_taken_am->updated_at!='')
                        $am_update_time=$att_is_taken_am->updated_at;
                    if($is_avail<$am_update_time){
                        if(empty($atten)){
                            $at1="P";
                        }else if($atten->attendance=='L'){
                            $at1=$atten->attendance;
                        }else if($atten->attendance=='A'){
                            $at1=$atten->attendance;
                        }
                    }
                    else{
                        $at1="-";
                    }
                }
                if(empty($att_is_taken_pm)){
                    $at2="-";
                }
                else{
                    $pm_update_time=$att_is_taken_pm->created_at;
                    if($att_is_taken_pm->updated_at)
                        $pm_update_time=$att_is_taken_pm->updated_at;
                    if($is_avail<$pm_update_time){
                        if(empty($atten2)){
                            $at2="P";
                        }else if($atten2->attendance=='L'){
                            $at2=$atten2->attendance;
                        }else if($atten2->attendance=='A'){
                            $at2=$atten2->attendance;
                        }
                    }else{
                        $at2="-";
                    }
                }
                array_push($attendance,['date' => $dat,'am'=>$at1,'pm' =>$at2]);
                //}
            }
            return api(['data' => $attendance]);
        } else {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm. Year is according to current session.']);
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
        $daterange = new DatePeriod($begin, new DateInterval('P1D'), $end->modify('+1 month'));
        $sessionStart = new DateTime($session->fromDate);
        $sessionEnd = new DateTime($session->toDate);
        $inbetween_date = [];
        foreach ($daterange as $date) {
            if ($date > $sessionStart && $date < $sessionEnd) {

                if ($date_now > $date && $date->format('N') != 7 && !in_array($date, $holiday)) {
                    $inbetween_date[] = $date->format("Y-m-d");
                }
            }
        }
        return $inbetween_date;
    }
public function updatepass(){
		$request = \Request::all();
		print_r($request);
		if($request['newpass']==$request['confirmpass']){
			/* $user_exits=\DB::table('student')->where('student.id','=',$request['username'])
			->join('users','student.user_id','=','users.id')
			->select('users.id','users.hint_password')->first(); */
			//dd();
			$user_exits=\DB::table('users')->where('id','=',$request['username'])
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

   //  public function getAttendanceMonthParent($platform, $student_id, $month) {//mari for v3
   //      if (preg_match("/^(0[1-9]|1[0-2])$/", $month)) {
   //          $sessionDate = '01-' . $month . '-' . date('Y');
   //          $dates = $this->get_inbetween_date($sessionDate, $sessionDate);
   //          $attendance = [];
   //          $passed_date = [];
			// $data=$student_id;
   //          $stu_admindate=\DB::table('student')->where('id','=',$student_id)
			// ->where('school_id','=',$this->user->school_id)->first();
			// $is_avail=strtotime($stu_admindate->date_of_admission);
   //          foreach ($dates as $dat) {
			// 	$check_date=strtotime($dat);
			// 	if($is_avail>$check_date){
			// 		array_push($attendance,['date' => $dat,'am'=>'-','pm' =>'-']);
			// 	}
			// 	else{
			// 		$att_is_taken_am=\DB::table('attendance_status')
			// 			->where('school_id','=',$stu_admindate->school_id)
			// 			->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
			// 			->where('section_id','=',$stu_admindate->section_id)
			// 			->where('attendance_session','=','am')->first();
			// 		$att_is_taken_pm=\DB::table('attendance_status')
			// 			->where('school_id','=',$stu_admindate->school_id)
			// 			->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
			// 			->where('section_id','=',$stu_admindate->section_id)
			// 			->where('attendance_session','=','pm')->first();
			// 		$atten = Attendance::where('school_id','=',$stu_admindate->school_id)
			// 			->where('date', $dat)
			// 			->where('student_id','=',$student_id)
			// 			->where('attendance','!=','P')
			// 			->where('attendance_session','=','am')
			// 			->first();
			// 		$atten2 = Attendance::where('school_id', $stu_admindate->school_id)
			// 			->where('date', $dat)
			// 			->where('student_id','=',$student_id)
			// 			->where('attendance','!=','P')
			// 			->where('attendance_session','=','pm')
			// 			->first();
			// 		if(empty($att_is_taken_am)){
			// 			$at1="-";
			// 		}
			// 		else{
			// 			if(empty($atten)){
			// 				$at1="P";
			// 			}else if($atten->attendance=='L'){
			// 				$at1=$atten->attendance;
			// 			}else if($atten->attendance=='A'){
			// 				$at1=$atten->attendance;
			// 			}
			// 		}
			// 		if(empty($att_is_taken_pm)){
			// 			$at2="-";
			// 		}
			// 		else{
			// 			if(empty($atten2)){
			// 				$at2="P";
			// 			}else if($atten2->attendance=='L'){
			// 				$at2=$atten2->attendance;
			// 			}else if($atten2->attendance=='A'){
			// 				$at2=$atten2->attendance;
			// 			}
			// 		}
			// 		array_push($attendance,['date' => $dat,'am'=>$at1,'pm' =>$at2]);
					
			// 	}
   //          }
   //          return api(['data' => $attendance]);
   //      } else {
   //          return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm. Year is according to current session.']);
   //      }
   //  }

    public function getAttendanceMonthParent($platform, $student_id, $month) {
        if (preg_match("/^(0[1-9]|1[0-2])$/", $month)) {
            $sessionDate = '01-' . $month . '-' . date('Y');
            $dates = $this->get_inbetween_date($sessionDate, $sessionDate);
            $attendance = [];
            $passed_date = [];
            $data=$student_id;
            $stu_admindate=\DB::table('student')->where('id','=',$student_id)
            ->where('school_id','=',$this->user->school_id)->first();
            foreach ($dates as $dat) {
                $att_is_taken_am=\DB::table('attendance_status')
                    ->where('school_id','=',$stu_admindate->school_id)
                    ->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
                    ->where('section_id','=',$stu_admindate->section_id)
                    ->where('attendance_session','=','am')->first();
                    
                $att_is_taken_pm=\DB::table('attendance_status')
                    ->where('school_id','=',$stu_admindate->school_id)
                    ->where('date','=',$dat)->where('class_id','=',$stu_admindate->class_id)
                    ->where('section_id','=',$stu_admindate->section_id)
                    ->where('attendance_session','=','pm')->first();
                $atten = Attendance::where('school_id','=',$stu_admindate->school_id)
                    ->where('date', $dat)
                    ->where('student_id','=',$student_id)
                    ->where('attendance','!=','P')
                    ->where('attendance_session','=','am')
                    ->first();
                $atten2 = Attendance::where('school_id', $stu_admindate->school_id)
                    ->where('date', $dat)
                    ->where('student_id','=',$student_id)
                    ->where('attendance','!=','P')
                    ->where('attendance_session','=','pm')
                    ->first();
                if(empty($att_is_taken_am)){
                        $at1="-";
                    }
                else{
                    $am_update_time=$att_is_taken_am->created_at;
                    if($att_is_taken_am->updated_at!='')
                        $am_update_time=$att_is_taken_am->updated_at;
                    if($stu_admindate->created_at<$am_update_time){  
                        if(empty($atten)){
                            $at1="P";
                        }else if($atten->attendance=='L'){
                            $at1=$atten->attendance;
                        }else if($atten->attendance=='A'){
                            $at1=$atten->attendance;
                        }
                    }
                    else{
                        $at1="-";
                    }
                }
                if(empty($att_is_taken_pm)){
                        $at2="-";
                    }
                else{
                    $pm_update_time=$att_is_taken_pm->created_at;
                    if($att_is_taken_pm->updated_at!='')
                        $pm_update_time=$att_is_taken_pm->updated_at;
                    if($stu_admindate->created_at<$pm_update_time){
                        if(empty($atten2)){
                            $at2="P";
                        }else if($atten2->attendance=='L'){
                            $at2=$atten2->attendance;
                        }else if($atten2->attendance=='A'){
                            $at2=$atten2->attendance;
                        }
                    }else{
                       $at2="-"; 
                    }
                }
                array_push($attendance,['date' => $dat,'am'=>$at1,'pm' =>$at2]);
            }
            return api(['data' => $attendance]);
        } else {
            return \api::notValid(['errorMsg' => 'Date format is not valid. Provide a format in mm. Year is according to current session.']);
        }
    }



    public function getFeedback() {
        $feedbacks = Feedback::where('student_id', $this->student->id)->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')->orderBy('feedback.id', 'DESC')
                ->select('feedback.id', 'teacher.name as teacherName', 'feedback.feedback', 'feedback.date')
                ->orderBy('id', 'DESC')
                ->get();
        return \api::success(['data' => $feedbacks]);
    }

    public function getResult($platform, $examid, $month) {
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
                ->where('result.month', 'LIKE', '%' . $month . '%')
                ->leftJoin('subject', 'result.subject_id', '=', 'subject.id')
                ->leftJoin('teacher', 'result.teacher_id', '=', 'teacher.id')
                ->leftJoin('exam', 'result.exam_type_id', '=', 'exam.id')
                ->select
                        (
                        'result.id', 'exam.exam_type', 'result.month', 'result.date', 'result.max_marks', 'result.pass_marks', 'result.obtained_marks', 'teacher.name as teacherName', 'subject.subject', 'result.result', 'result.grade'
                )
                ->get();
        // dd($result);

        $total = Result::where('result.exam_type_id', $examid)
                ->where('result.student_id', $this->student->id)
                ->where('result.month', 'LIKE', '%' . $month . '%')
                ->leftJoin('exam', 'result.exam_type_id', '=', 'exam.id')
                ->select
                        (
                        \DB::raw('sum(result.max_marks) AS total_marks'), \DB::raw('sum(result.pass_marks) AS pass_marks'), \DB::raw('sum(result.obtained_marks) AS obtained_marks'), 'exam.exam_type', 'result.month'
                )
                ->first();
                  Result::where('month', $month)->where('student_id',$this->student->id)->update(['view_status_s' => 1]);
        if (count($results) > 0 and $results) {
            // $view = \View::make('report', compact('total', 'results'));
            // $html = $view->render();
            // \PDF::loadHtml($html, 'A4', 'portrait')->save('report/report'.$this->student->id.'.pdf');
            // $pdfReport = config('constants.share_link').$this->student->id.'.pdf';

            \Excel::create('report' . $this->student->id, function($excel) use ($total, $results) {

                $excel->sheet('Excel sheet', function($sheet) use ($total, $results) {
                    $sheet->loadView('report')->with('total', $total)
                            ->with('results', $results);
                    $sheet->setOrientation('portrait');
                });
            })->store('pdf', 'report');
            $pdfReport = config('constants.share_link') . '/report' . $this->student->id . '.pdf';
            return api(['data' => $results, 'total' => $total, 'url' => $pdfReport]);
        } else {
            return api::notFound(['errorMsg' => 'Result Not Found!!!']);
        }
    }

    public function getNotice() {
        $notices = \DB::table('notice')->where('type', 'student')->orderBy('id', 'DESC')->get();
        return api(['data' => $notices]);
    }

    public function getKnowledge() {
        $student = \DB::table('student')->where('user_id', $this->user->id)->first();
        $questions = \DB::table('questions')->where('school_id', \Auth::user()->school_id)
                        ->where('class_id', $student->class_id)->get();
        foreach ($questions as $question) {
            $option = \DB::table('options')->where('question_id', $question->id)->get();
            $question->option = $option;
        }
        if (!$questions)
            return api()->notFound(['errorMsg' => 'not enough questions']);
        return api(['data' => $questions]);
    }

}
