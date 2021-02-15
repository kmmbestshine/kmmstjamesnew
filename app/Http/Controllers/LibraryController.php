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
use DB;
//use Mail;

class LibraryController extends Controller
{
    protected $user;
    private $active_session;//updated 6-6-2018
    function __construct()
    {
        //updated 6-6-2018
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
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
    *                            LIBRARY MODULE
    *************************************************************************/
    public function getVideo()
        {
            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                ->where('session_id','=',$this->active_session->id)
                ->get();
            return view('users.video.addvideo',compact('classes'));
            
        }
    public function getStudentvideoSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }
    public function fieldsVideo(Request $request) {
        $input = \Request::all();
        //dd($input);
        $exist=\DB::table('videos_event')->where('school_id', \Auth::user()->school_id)->first();
        if($exist){
            $limit_id=\DB::table('videos_event')->where('school_id', \Auth::user()->school_id)->count('id');
        //dd($limit_id);
        if($limit_id >100){
             $input['error'] = 'Your Uploaded File exceed 100 files. So Please delete any uploaded file for upload New File...!';
            return \Redirect::back()->withInput($input);
        }
        }
        
        
         $fileName = time().'.'.$request->file->extension();  
   
        $request->file->move(public_path('video'), $fileName);

        if($input['event_type']=='video'){
            //dd($input['event_type'],'vi111111');
            $ids = \DB::table('videos_event')->insert([
                    'school_id' => \Auth::user()->school_id,
                    'session_id'=> $this->active_session->id,
                    'class_id'=> $input['class'],
                    'section_id'=> $input['section'],
                    'event_type' => $input['event_type'],
                    'vid_event' => $input['event_name'],
                    'video_fil'=> $fileName,
                    'date' => date('d-m-Y', strtotime($input['date']))
            ]);
        }
        elseif($input['event_type']=='audio'){
            //dd($input['event_type'],'au111111');
            $ids = \DB::table('videos_event')->insert([
                    'school_id' => \Auth::user()->school_id,
                    'session_id'=> $this->active_session->id,
                    'class_id'=> $input['class'],
                    'section_id'=> $input['section'],
                    'aud_event' => $input['event_name'],
                    'event_type' => $input['event_type'],
                    'audio_fil'=> $fileName,
                    'date' => date('d-m-Y', strtotime($input['date']))
            ]);
        }else{
            //dd($input['event_type'],'pdf11111');
            $ids = \DB::table('videos_event')->insert([
                    'school_id' => \Auth::user()->school_id,
                    'session_id'=> $this->active_session->id,
                    'class_id'=> $input['class'],
                    'section_id'=> $input['section'],
                    'pdf_event' => $input['event_name'],
                    'event_type' => $input['event_type'],
                    'pdf'=> $fileName,
                    'date' => date('d-m-Y', strtotime($input['date']))
            ]);
        }
   
        
        
        $input['success'] = 'Requested File has been saved successfully';
            return \Redirect::back()->withInput($input);
           // return view('users.video.addvideo',compact('classes'));
            
    }
    public function viewVideo()
    {
        $video_event = \DB::table('videos_event')->where('school_id', \Auth::user()->school_id)
            ->where('session_id','=',$this->active_session->id)
            ->get();
            
        foreach ($video_event as $key => $value) {
            $class_id[]=$value->class_id;
            $section_id[]=$value->section_id;
            $videoevent_name[]=$value->vid_event;
            $audiooevent_name[]=$value->aud_event;
             $pdfevent_name[]=$value->pdf_event;
            $video_file[]=$value->video_fil;
            $audio_file[]=$value->audio_fil;
            $pdf_file[]=$value->pdf;
            $dates[]=$value->date;
            $cre_dates[]=$value->created_at;
            $video_id[]=$value->id;
        }
       $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->select('school_name')->first();
        return view('users.video.viewVideo',compact('schoolname','cre_dates','pdfevent_name','pdf_file','audio_file','video_id','video_event','class_id','section_id','videoevent_name','audiooevent_name','video_file','dates'));
        
    }
    public function videodownloadvideo($id)
    { 
        $video_event=\DB::table('videos_event')->where('school_id', \Auth::user()->school_id)->where('id', $id)->first();
       //dd($video_event);
        if($video_event->video_fil || $video_event->audio_fil || $video_event->pdf)
        {
            if($video_event->video_fil){
                $path = public_path().'/'.'video/'. $video_event->video_fil;
            }elseif($video_event->audio_fil){
                $path = public_path().'/'.'video/'. $video_event->audio_fil;
            }else{
                $path = public_path().'/'.'video/'. $video_event->pdf;
            }
        }

        else {
           
      $video_event=\DB::table('videos_event')->where('school_id', \Auth::user()->school_id)->get();

       foreach ($video_event as $key => $value) {
            $class_id[]=$value->class_id;
            $section_id[]=$value->section_id;
            $videoevent_name[]=$value->vid_event;
            $audiooevent_name[]=$value->aud_event;
            $pdfevent_name[]=$value->pdf_event;
            $video_file[]=$value->video_fil;
            $audio_file[]=$value->audio_fil;
            $pdf_file[]=$value->pdf;
            $dates[]=$value->date;
            $cre_dates[]=$value->created_at;
            $video_id[]=$value->id;
        }
        $msg='Requested File Does not Exiest in our Database!';
        $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->select('school_name')->first();
        return view('users.video.viewVideo',compact('schoolname','cre_dates','pdfevent_name','pdf_file','msg','video_id','video_event','class_id','section_id','videoevent_name','audiooevent_name','video_file','dates','audio_file'));
        }
        
         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        
    }
    public function deletevideodocument()
    {
         $input = \Request::all();

        $document_id=\DB::table('videos_event')->where('school_id', \Auth::user()->school_id)->where('id', $input['video_id'])->delete();
        
        $msg['success'] = 'Successfully  deleted Requested File';
        //dd($msg);
        return \Redirect::back()->withInput($msg);
      
    }
    /** @ Issue Books to student @ **/
    public function getStudentLibrary()
    {
        // return 'abc';
        $reg_no = \Request::get('reg_no');
        $book_no = \Request::get('book_no');
        //echo $this->user->school_id;
        $student = Students::where('student.registration_no', $reg_no)
            ->where('student.session_id',$this->active_session->id)//updated 6-6-2018
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id', 'student.name', 'student.roll_no', 'student.registration_no', 'class.class', 'section.section')
            //->where('student.school_id', $this->user->school_id)
            ->where('student.school_id', \Auth::user()->school_id)
            ->first();
        //dd($student);
        $getBook = \DB::table('library')->where('book_no',$book_no)
            ->where('school_id', \Auth::user()->school_id)->first();
        $getStudent = \DB::table('issue')->where('school_id', \Auth::user()->school_id)->where('issue.student_id', $student->id)
            ->where('issue.book_id',$getBook->id)
            ->where('return_flag', 0)
            //->select('issue.return_flag')
            ->get();
        //var_dump($student);

        if ($getStudent)
        {
            return 'null';
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

    /** @ Issue Books to Teacher @ **/
    public function getTeacherLibrary()
    {
        $username = \Request::get('username');
        $book_no = \Request::get('book_no');
        //echo $username;
        //$library=array();$teacher=array();
        $teacher=\DB::table('users')->where('users.username', $username)
            ->where('users.school_id', \Auth::user()->school_id)
            ->leftJoin('teacher','users.id','=','teacher.user_id')
            ->select('teacher.id','teacher.name','teacher.email','teacher.user_id','users.username')->first();
        //dd($teacher);
        $getBook = \DB::table('library')->where('book_no',$book_no)
            ->where('school_id', \Auth::user()->school_id)->first();
        $getTeacher = \DB::table('issue')->where('school_id', \Auth::user()->school_id)->where('issue.teacher_name',$teacher->user_id)
            ->where('issue.book_id',$getBook->id) ->where('return_flag', 0)->get();

        if ($getTeacher)
        {
            return 'empty';
        }
        else
        {
            $library=\DB::table('issue')->where('issue.teacher_name',$teacher->user_id)
                ->where('issue.return_flag', 0)
                ->leftJoin('library', 'issue.book_id', '=', 'library.id')
                ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                ->select('library.book_no', 'subject.subject', 'issue.issue_date', 'issue.return_date')->get();
            $data['library'] = $library;
            $data['teacher'] = $teacher;
            return $data;
        }
    }

public function getgateregister()
        {
           // dd('jjjjjj');
            
            return view('users.library.gateregister');
            
        }
        public function getgateregisterin()
        {
            return view('users.library.inregister');
            
        }
        public function getgateregisterout()
        {
           
            return view('users.library.outregister');
            
        }

        public function postgateinregister()
        {
            $input = \Request::all();
            date_default_timezone_set("Asia/Kolkata");
           // echo "The time is " . date("h:i:sa");
           // dd(date("h:i:sa"));
            if($input['entering']){
                if($input['user_name']){

               $teacher=\DB::table('users')->where('users.username', $input['user_name'])
            ->where('users.school_id', \Auth::user()->school_id)
            ->leftJoin('teacher','users.id','=','teacher.user_id')
            ->select('teacher.name','teacher.designation','users.username')->first();
            
           // dd($teacher);
                $ids = \DB::table('librarygateregister')->insert([
                    'school_id' => \Auth::user()->school_id,
                    'session_id'=> $this->active_session->id,
                    'date'=> date('d-m-Y'),
                    'type'=> 'staff',
                    'username' => $teacher->username,
                    'name' => $teacher->name,
                    'staff_type'=> $teacher->designation,
                    'intime' => date("h:i:sa"),
                   // 'outtime' => date("h:i:sa"),
                    
            ]);
                $msg['success'] = 'Successfully  Saved Staff In  Time';
        //dd($msg);
        return \Redirect::back()->withInput($msg);
            }else{
                $student = Students::where('student.registration_no', $input['registration_no'])
            ->where('student.session_id',$this->active_session->id)//updated 6-6-2018
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id',
                'student.name',
                'student.registration_no',
                'class.class',
                'section.section')
            ->where('student.school_id', \Auth::user()->school_id)
            ->first();
           // dd($student);
                $ids = \DB::table('librarygateregister')->insert([
                    'school_id' => \Auth::user()->school_id,
                    'session_id'=> $this->active_session->id,
                    'date'=> date('d-m-Y'),
                    'type'=> 'student',
                    'username' => $student->registration_no,
                    'name' => $student->name,
                    'class'=> $student->class,
                    'section'=> $student->section,
                     'intime' => date("h:i:sa"),
                    //'outtime' => date("h:i:sa"),
            ]);
            $msg['success'] = 'Successfully  Saved Student In  Time';
        //dd($msg);
        return \Redirect::back()->withInput($msg);
            }
            
    }else{
       if($input['user_name']){

               $teacher=\DB::table('users')->where('users.username', $input['user_name'])
            ->where('users.school_id', \Auth::user()->school_id)
            ->leftJoin('teacher','users.id','=','teacher.user_id')
            ->select('teacher.name','teacher.designation','users.username')
            ->first();
            $intime1 = \DB::table('librarygateregister')->where('username', $teacher->username)
            ->where('school_id', \Auth::user()->school_id)
            ->latest('intime')->first();

            
           // dd($intime1,$input['user_name']);
                if($intime1->intime == "")
            {
                $msg['error'] = 'Please Enter Staff Login Time';
       
                return \Redirect::back()->withInput($msg);
            }else{

                $ids = \DB::table('librarygateregister')->where('id', $intime1->id)->update([
                    
                    'outtime' => date("h:i:sa"),
            ]);
            }
            $msg['success'] = 'Successfully  Saved Staff Out Time';
        //dd($msg);
        return \Redirect::back()->withInput($msg);
            }else{
                $student = Students::where('student.registration_no', $input['registration_no'])
            ->where('student.session_id',$this->active_session->id)//updated 6-6-2018
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id',
                'student.name',
                'student.registration_no',
                'class.class',
                'section.section')
            ->where('student.school_id', \Auth::user()->school_id)
            ->first();
             $intime1 = \DB::table('librarygateregister')->where('username', $student->registration_no)
            ->where('school_id', \Auth::user()->school_id)
            ->latest('intime')->first();
            if($intime1->intime == "")
            {
                $msg['error'] = 'Please Enter Student Login Time';
       
                return \Redirect::back()->withInput($msg);
            }else{

                $ids = \DB::table('librarygateregister')->where('id', $intime1->id)->update([
                    
                    'outtime' => date("h:i:sa"),
            ]);
            }
            $msg['success'] = 'Successfully  Saved Student Out Time';
        //dd($msg);
        return \Redirect::back()->withInput($msg);
            }
            
            
    }
            
      
        }

    /** @ Return Books to Student @ **/
    public function getStudentReturnBook()
    {
        $reg_no = \Request::get('reg_no');
        $book_no = \Request::get('book_no');
        //echo $book_no;
        $student = Students::where('student.registration_no', $reg_no)
            ->where('student.session_id',$this->active_session->id)//updated 6-6-2018
            ->leftJoin('class', 'student.class_id', '=', 'class.id')
            ->leftJoin('section', 'student.section_id', '=', 'section.id')
            ->select('student.id',
                'student.name',
                'student.roll_no',
                'student.registration_no',
                'class.class',
                'section.section')
            ->where('student.school_id', \Auth::user()->school_id)
            ->first();
        $getBook = \DB::table('library')->where('school_id', \Auth::user()->school_id)->where('book_no',$book_no)->first();
        $getStudent = \DB::table('issue')->where('school_id', \Auth::user()->school_id)->where('issue.student_id', $student->id)
            ->where('issue.book_id',$getBook->id)
            ->where('issue.return_flag',0)
            ->first();
        //var_dump($getStudent);
        if (!$getStudent)
        {
            return 'empty';
        }
        else
        {
            $library = \DB::table('issue')->where('issue.student_id', $student->id)
                ->where('issue.return_flag', 0)
                ->leftJoin('library', 'issue.book_id', '=', 'library.id')
                ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                ->select('library.book_no',
                    'subject.subject',
                    'issue.issue_date',
                    'issue.return_date',
                    'issue.id')->get();
            $data['library'] = $library;
            $data['student'] = $student;
            return $data;
            //var_dump($library);exit;
        }
    }


    /** @ Return Books to Teacher @ **/
    public function getTeacherReturnBook()
    {
        //return 'abf';
        $username = \Request::get('username');
        $book_no = \Request::get('book_no');
        // echo $username;
        // echo $book_no;
        $teacher=\DB::table('users')->where('users.username', $username)
            ->where('users.school_id', \Auth::user()->school_id)
            ->leftJoin('teacher','users.id','=','teacher.user_id')
            ->select('teacher.id','teacher.name','teacher.email','teacher.user_id','users.username')->first();
        $getBookDetail = \DB::table('library')->where('book_no',$book_no)
            ->where('school_id', \Auth::user()->school_id)->first();
        $getTeacher=\DB::table('issue')->where('school_id', \Auth::user()->school_id)->where('teacher_name',$teacher->user_id)
            ->where('issue.book_id',$getBookDetail->id)
            ->where('return_flag',0)->get();

        if(!$getTeacher)
        {
            return 'empty';
        }
        else
        {
            $library = \DB::table('issue')->where('teacher_name', $teacher->user_id)
                ->where('issue.return_flag', 0)
                ->leftJoin('library', 'issue.book_id', '=', 'library.id')
                ->leftJoin('subject', 'library.subject_id', '=', 'subject.id')
                ->select('library.book_no',
                    'subject.subject',
                    'issue.issue_date',
                    'issue.return_date',
                    'issue.id')->get();
            $data['library'] = $library;
            $data['teacher'] = $teacher;
            return $data;
        }
    }


    /************************************************************************
    *                           REPORT MODULE
    *************************************************************************/

    /** @ To Download Report @ **/
    public function reportDownload()
    {
        return response()->download(\Session::get('getReportUrl'));
    }

    /** @ Report for library @ **/
    public function libraryReport()
    {
        //return 'abc';
        $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
        $categoryNo = \Request::get('category');
        if($categoryNo != '')
        {
            $totalNoBooks=0;
            $totalIssue=0;
            $getCategoryBooks = Library::where('library.school_id', $this->user->school_id)
                ->join('book_category', 'library.book_category', '=', 'book_category.id');

            if($categoryNo != 0)
            {
                $getCategoryBooks = $getCategoryBooks->where('book_category', $categoryNo);
            }
            $getCategoryBooks = $getCategoryBooks->select(
                'library.book_no',
                'book_category.category',
                'library.book_name',
                'library.auth_name',
                'library.publisher_name',
                'library.purchase_date',
                'library.no_of_books',
                'library.issued_books',
                'library.available',
                'library.id'
            )
                ->orderBy('library.book_no','asc')
                ->orderBy('book_category.category','asc')
                ->get();

            foreach ($getCategoryBooks as $bookCategory)
            {
                $totalNoBooks = $totalNoBooks + $bookCategory->no_of_books;
                $totalIssue = $totalIssue + $bookCategory->issued_books;
            }
            $totalAvailability = $totalNoBooks - $totalIssue;
            //return $totalNoBooks;
            //return $totalAvailability;
            // return $totalIssue;

            $currentDate = date("d-m-Y_H_i_s");

            \Excel::create("libraryReport_".$currentDate, function($excel) use ($getCategoryBooks, $totalNoBooks, $totalIssue, $totalAvailability)
            {

                $excel->sheet('Excel sheet', function($sheet) use ($getCategoryBooks, $totalNoBooks, $totalIssue, $totalAvailability) {
                    $sheet->loadView('users.report.libraryExport')->with('getCategoryBooks', $getCategoryBooks)->with('totalNoBooks', $totalNoBooks)->with('totalIssue', $totalIssue)->with('totalAvailability', $totalAvailability);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('libraryReport'));

            $fileURL = storage_path() . "/libraryReport/libraryReport_" . $currentDate . '.xls';
            \Session::put('getReportUrl', $fileURL);

        }
        else
        {
            $msg['error'] = 'Please Choose Category';
        }
        return view('users.report.libraryReport', compact('categories', 'getCategoryBooks','totalAvailability','totalIssue','totalNoBooks','categoryNo'))->withInput($msg);

    }

    /** @ Report for Student Analyst @ **/
    public function analystReport()
    {
        $exam_type = \DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 6-6-2018
            ->get();
        $examType = \Request::get('exam_type');
        $studClass = \Request::get('class');
        $studSection = \Request::get('section');

        // updated 4-11-2017
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', '1')
            ->select('id')
            ->first();

        if ($examType != '' && $studClass != '' && $studSection != '')
        {
            // dd($examType);
            $getStudentClass = \DB::table('class')->where('school_id', $this->user->school_id)
                ->where('id', $studClass)->first();
            $getSession = \DB::table('session')->where('school_id', $getStudentClass->school_id)
                ->where('active',1)->first();
            $getExamType = \DB::table('exam')->where('school_id', \Auth::user()->school_id)
                ->where('id', $examType)->first();

            // SELECT s.name from student as s LEFT JOIN result as r ON r.student_id = s.id where s.class_id = 436

            $getStudentDetails = \DB::table('result')->where('result.class_id', $getStudentClass->id)
                ->where('result.section_id', $studSection)
                ->leftJoin('student', 'result.student_id', '=', 'student.id')
                ->leftJoin('class', 'class.id', '=', 'result.class_id')
                ->leftJoin('section', 'section.id', '=', 'result.section_id')
                ->leftJoin('exam', 'exam.id', '=', 'result.exam_type_id')
                ->where('student.session_id',$session->id)//updated 4-11-2017
                ->where('result.school_id',\Auth::user()->school_id);
            if ($examType != 0)
            {
               /*
                * $getStudentDetails = $getStudentDetails
                    ->whereRaw("find_in_set('45',result.obtained_marks)")
                    ->select('result.obtained_marks')
                    ->get();
               */

               $getStudentDetails = $getStudentDetails->where('result.exam_type_id', $examType)
                    ->select(
                    DB::raw( "SUM(CASE WHEN result.obtained_marks < result.pass_marks THEN 1 ELSE 0 END) AS 'total_subjects_fail'" ),
                    DB::raw("sum(result.max_marks) as total_max,sum(result.obtained_marks) as scored_marks,sum(result.pass_marks) as total_pass_mark"),
                    DB::raw("count(result.subject_id) as total_subjects"),
                    'class.class',
                    'section.section',
                    'section.subjects',
                    'exam.exam_type',
                    'student.name',
                    'student.registration_no',
                    'result.max_marks',
                    'result.pass_marks',
                    'result.obtained_marks',
                    'result.result',
                    'result.exam_type_id',
                    'result.grade'
                    )
                    ->orderBy('scored_marks', 'desc')
                    ->groupBy('result.student_id', 'result.exam_type_id', 'result.class_id', 'result.section_id')
                    ->get();
            // dd($getStudentDetails);
            }
            else
            {
                $getStudentDetails = $getStudentDetails->select(
                    DB::raw( "SUM(CASE WHEN result.obtained_marks < result.pass_marks THEN 1 ELSE 0 END) AS 'total_subjects_fail'" ),
                    DB::raw("sum(result.max_marks) as total_max,sum(result.obtained_marks) as scored_marks,sum(result.pass_marks) as total_pass_mark"),
                    DB::raw("count(result.subject_id) as total_subjects"),//updated 20-10-2017
                    'class.class',
                    'section.section',
                    'section.subjects',
                    'exam.exam_type',
                    'student.name',
                    'student.registration_no',
                    'result.max_marks',
                    'result.pass_marks',
                    'result.obtained_marks',
                    'result.result',
                    'result.exam_type_id',
                    'result.grade'
                    )
                    ->orderBy('exam.exam_type', 'desc')
                    ->orderBy('scored_marks', 'desc')
                    ->groupBy('result.student_id', 'result.exam_type_id', 'result.class_id', 'result.section_id')
                    ->get();
            }
            //dd($getStudentDetails);

            if ($getStudentDetails)
            {
                $getStudentSection = \DB::table('section')->where('class_id', $studClass) ->where('id', $studSection)->first();
                // $subjects = \DB::table('subject')->whereIn('id', json_decode($getStudentSection->subjects))->get();
                $subjects = explode(',', str_replace(['[', ']', ' '], '', $getStudentSection->subjects));
            }

            $currentDate = date("d-m-Y_H_i_s");

            \Excel::create("analystReport_" . $currentDate, function ($excel) use ($getStudentDetails, $exam_type, $classes, $getExamType, $subjects, $examType, $getSession) {

                $excel->sheet('Excel sheet', function ($sheet) use ($getStudentDetails, $exam_type, $classes, $getExamType, $subjects, $examType, $getSession) {
                    $sheet->loadView('users.report.analystExport')->with('getStudentDetails', $getStudentDetails)->with('exam_type', $exam_type)->with('classes', $classes)->with('getExamType', $getExamType)->with('subjects', $subjects)->with('examType', $examType)->with('getSession', $getSession);
                    $sheet->setOrientation('portrait');
                });
            })->store('xls', storage_path('analystReport'));

            $fileURL = storage_path() . "/analystReport/analystReport_" . $currentDate . '.xls';
            \Session::put('getReportUrl', $fileURL);
        }
        else
        {
            $msg['error'] = 'Please Choose the Field';
        }
        return view('users.report.studentAnalystReport', compact('studSection','studClass','examType','getStudentDetails', 'exam_type', 'examType', 'classes', 'getExamType', 'subjects', 'getStudentClass', 'getStudentSection'))->withInput($msg);
    }

    public function viewhrsec_Result()
    {
        $examtypes=\DB::table('exam')->where('school_id',\Auth::user()->school_id)->get();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id','=',$this->active_session->id)
            ->get();
        //dd('hi',$examtypes,$classes);
         return view('users.result.hrseclist', compact('classes','examtypes'));
    }
    public function viewfasa_Result()
    {
        $examtypes=\DB::table('exam')->where('school_id',\Auth::user()->school_id)->get();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id','=',$this->active_session->id)
            ->get();
        //dd('hi',$examtypes,$classes);
         return view('users.result.fasalist', compact('classes','examtypes'));
    }
public function viewfasaResultdetails() {//changes by mari 03.10.2017
        $class_id = \Request::get('class');
        $section = \Request::get('section');
        $exam = \Request::get('exam');
//dd('hrsec',$class_id,$section,$exam);
        if ($class_id and $section and $exam) {
            $students = \DB::table('student')->where('class_id', $class_id)->where('school_id', \Auth::user()->school_id)
             ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('section_id', $section)->get();
        $category='FA + SA Marks';
            foreach ($students as $student) {
                $result = \DB::table('result')->where('exam_type_id', $exam)->join('subject', 'result.subject_id', '=', 'subject.id')
                ->where('category', $category)
                ->where('student_id', $student->id)->get();
                $totalObtain = 0;
                $totalFAMarks = 0;
                $totalSAMarks = 0;
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
                    $student->grade = $rs->grade;
                    $student->fa_grade = $rs->fa_grade;
                    $student->sa_grade = $rs->sa_grade;
                    if(is_numeric($rs->obtained_marks)){                        
                        $totalObtain = $totalObtain + $rs->obtained_marks;
                    }else{                        
                        $totalObtain = $totalObtain + 0;
                    } 
                    if(is_numeric($rs->fa_marks)){                        
                        $totalFAMarks = $totalFAMarks + $rs->fa_marks;
                    }else{                        
                        $totalFAMarks = $totalFAMarks + 0;
                    }  
                    if(is_numeric($rs->sa_marks)){                        
                        $totalSAMarks = $totalSAMarks + $rs->sa_marks;
                    }else{                        
                        $totalSAMarks = $totalSAMarks + 0;
                    }                     
                    $max_total= $max_total + $rs->max_marks;
                    $pass_totol=$pass_totol+$rs->pass_marks;
                }
                //dd($totalObtain,$totalPractical,$totalThery);
                $student->totalObtain = $totalObtain;
                $student->totalFAMarks = $totalFAMarks;
                $student->totalSAMarks = $totalSAMarks;
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
        return view('users.result.fasalistdetails', compact('students', 'class_id', 'section', 'exam'));
    }
    public function fasaresultDownload($class, $section, $exam, $id) {
        $students = \DB::table('student')->where('student.class_id', $class)
            ->where('student.school_id', \Auth::user()->school_id)
            ->where('student.section_id', $section)
            ->where('student.id', $id)
            ->first();
        //dd($students);
        $parents = \DB::table('parent')
            ->where('school_id', \Auth::user()->school_id)
            ->where('id', $students->parent_id)
            ->first();
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)->where('active', '1')->first();
            //dd($students);
        $result = \DB::table('subject')
            ->where('subject.school_id', \Auth::user()->school_id)
            ->join('result', 'subject.id', '=', 'result.subject_id')
            ->where('result.exam_type_id', $exam)
            ->where('result.student_id', $id)
            ->get();
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        $grades=\DB::table('grade_system')->where('grade_system.school_id', $this->user->school_id)
            ->join('exam','grade_system.exam_type_id','=','exam.id')
            ->where('grade_system.exam_type_id','=',$exam)
            ->select('exam.exam_type','exam.id','grade_system.id as gid','grade_system.from_marks'
                ,'grade_system.to_marks','grade_system.frfamark'
                ,'grade_system.tofamark','grade_system.frsamark'
                ,'grade_system.tosamark','grade_system.grade','grade_system.fagrade','grade_system.sagrade',
                'grade_system.remarks', //updated 10-5-2018 by priya
                'grade_system.result'
                )
            ->get();
        $no_of_grades=\DB::table('grade_system')->where('grade_system.school_id', $this->user->school_id)
            ->join('exam','grade_system.exam_type_id','=','exam.id')
            ->where('grade_system.exam_type_id','=',$exam)
            ->select('exam.exam_type','exam.id','grade_system.id as gid','grade_system.from_marks'
                ,'grade_system.to_marks','grade_system.grade',
                'grade_system.remarks', //updated 10-5-2018 by priya
                'grade_system.result')->count();
        //dd($no_of_grades);
        $classesDate=Exam::where('school_id', $this->user->school_id)->where('id', $exam)->first();
        $from=$classesDate->from;
        $to=$classesDate->to;
        //dd('hi',$students,$result,$exams,$grades,'attend',$attendances,'from',$from,'to',$to);
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
         $currentSchool=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
         /********* start *******/
        
            $from = date('Y-m-d', strtotime($from));
            $to = date('Y-m-d', strtotime($to));
            
               $students1 = \DB::table('student')->where('id', $id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id', \Auth::user()->school_id)->get();
                $get_cls=\DB::table('class')
                    ->where('class.school_id','=',\Auth::user()->school_id)
                    ->where('class.session_id',$this->active_session->id)//updated 14-4-2018    
                    ->where('class.id','=',$students1[0]->class_id)
                    ->join('section','class.school_id','=','section.school_id')
                    ->where('section.id','=',$students1[0]->section_id)
                    ->where('section.class_id','=',$students1[0]->class_id)
                    ->select('section.section','class.class')->first();
                $input['class_name']=$get_cls->class;
                $input['section_name']=$get_cls->section;
                $attendances = \DB::table('attendance')
                    ->where('attendance.student_id', $students1[0]->id)
                    ->where('attendance.school_id', \Auth::user()->school_id)
                    ->whereBetween('attendance.date', array($from, $to))
                    ->join('student', 'attendance.student_id', '=', 'student.id')
                    ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
                    ->get();
                $total_workingdays=\DB::table('attendance_status')->where('school_id',\Auth::user()->school_id)
                        //->where('date',$date)->where('attendance_session','am')
                        ->whereBetween('date', array($from, $to))
                        ->where('class_id', $class)->where('section_id', $section)
                        ->count();
                        //dd($total_workingdays);
                
                $input['from']=$from;
                $input['to']=$to;
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
                //dd($attendance_date,$input['from'],$input['to']);
                    if($attendance_date == NULL){
                        $input['error'] = 'no attendance at that date';
                        return \Redirect::back()->withInput($input);
                    }
                    
            foreach ($attendance_date as $date) {
                foreach ($students1 as $key => $value) {
                    foreach ($am_attendance as $att_key => $att_value) {
                        $exist = \DB::table('attendance_status')->
                        where('school_id',\Auth::user()->school_id)
                        ->where('date',$date)->where('attendance_session','am')
                        ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
                        ->first();
                    if(!empty($exist)){
                        $update_am_time=$exist->created_at;
                        if($exist->updated_at!=''&&$exist->updated_at!=0){
                            $update_am_time=$exist->updated_at;
                           // dd('ddddddddd');
                        }
                        //dd('am',$update_am_time);
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
                        }else{
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
                        //dd('pm',$update_pm_time);
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
                        }else{
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
        $pdf = \PDF::loadView('users.result.invoice_fasaresult',compact('no_of_grades','parents','classesDate','input','session','total_workingdays','students','currentSchool','exams','grades','attendances', 
            'am_totalLeave', 'am_totalPresent', 'am_totalAbsent', 'pm_totalLeave', 'pm_totalPresent', 'pm_totalAbsent', 'students1', 'pm', 'am', 'attendance_date'));
        return $pdf->download('MarkReports.pdf');
    }

     public function viewhrsecResultdetails() {//changes by mari 03.10.2017
        $class_id = \Request::get('class');
        $section = \Request::get('section');
        $exam = \Request::get('exam');
//dd('hrsec',$class_id,$section,$exam);
        if ($class_id and $section and $exam) {
            $students = \DB::table('student')->where('class_id', $class_id)->where('school_id', \Auth::user()->school_id)
             ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('section_id', $section)->get();
        $category='HigherSecondary';
            foreach ($students as $student) {
                $result = \DB::table('result')->where('exam_type_id', $exam)->join('subject', 'result.subject_id', '=', 'subject.id')
                ->where('category', $category)
                ->where('student_id', $student->id)->get();
                $totalObtain = 0;
                $totalThery = 0;
                $totalPractical = 0;
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
                    if(is_numeric($rs->practical_marks)){                        
                        $totalPractical = $totalPractical + $rs->practical_marks;
                    }else{                        
                        $totalPractical = $totalPractical + 0;
                    }  
                    if(is_numeric($rs->theory_marks)){                        
                        $totalThery = $totalThery + $rs->theory_marks;
                    }else{                        
                        $totalThery = $totalThery + 0;
                    }                     
                    $max_total= $max_total + $rs->max_marks;
                    $pass_totol=$pass_totol+$rs->pass_marks;
                }
                //dd($totalObtain,$totalPractical,$totalThery);
                $student->totalObtain = $totalObtain;
                $student->totalPractical = $totalPractical;
                $student->totalThery = $totalThery;
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
        return view('users.result.hrseclistdetails', compact('students', 'class_id', 'section', 'exam'));
    }
    public function resultDownload($class, $section, $exam, $id) {
        $students = \DB::table('student')->where('student.class_id', $class)
            ->where('student.school_id', \Auth::user()->school_id)
            ->where('student.section_id', $section)
            ->where('student.id', $id)
            ->first();
        //dd($students);
        $parents = \DB::table('parent')
            ->where('school_id', \Auth::user()->school_id)
            ->where('id', $students->parent_id)
            ->first();
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)->where('active', '1')->first();
            //dd($students);
        $result = \DB::table('subject')
            ->where('subject.school_id', \Auth::user()->school_id)
            ->join('result', 'subject.id', '=', 'result.subject_id')
            ->where('result.exam_type_id', $exam)
            ->where('result.student_id', $id)
            ->get();
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        $grades=\DB::table('grade_system')->where('grade_system.school_id', $this->user->school_id)
            ->join('exam','grade_system.exam_type_id','=','exam.id')
            ->where('grade_system.exam_type_id','=',$exam)
            ->select('exam.exam_type','exam.id','grade_system.id as gid','grade_system.from_marks'
                ,'grade_system.to_marks','grade_system.grade',
                'grade_system.remarks', //updated 10-5-2018 by priya
                'grade_system.result'
                )
            ->get();
        $no_of_grades=\DB::table('grade_system')->where('grade_system.school_id', $this->user->school_id)
            ->join('exam','grade_system.exam_type_id','=','exam.id')
            ->where('grade_system.exam_type_id','=',$exam)
            ->select('exam.exam_type','exam.id','grade_system.id as gid','grade_system.from_marks'
                ,'grade_system.to_marks','grade_system.grade',
                'grade_system.remarks', //updated 10-5-2018 by priya
                'grade_system.result')->count();
        //dd($no_of_grades);
        $classesDate=Exam::where('school_id', $this->user->school_id)->where('id', $exam)->first();
        $from=$classesDate->from;
        $to=$classesDate->to;
        //dd('hi',$students,$result,$exams,$grades,'attend',$attendances,'from',$from,'to',$to);
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
         $currentSchool=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
         /********* start *******/
        
            $from = date('Y-m-d', strtotime($from));
            $to = date('Y-m-d', strtotime($to));
            
               $students1 = \DB::table('student')->where('id', $id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('school_id', \Auth::user()->school_id)->get();
                $get_cls=\DB::table('class')
                    ->where('class.school_id','=',\Auth::user()->school_id)
                    ->where('class.session_id',$this->active_session->id)//updated 14-4-2018    
                    ->where('class.id','=',$students1[0]->class_id)
                    ->join('section','class.school_id','=','section.school_id')
                    ->where('section.id','=',$students1[0]->section_id)
                    ->where('section.class_id','=',$students1[0]->class_id)
                    ->select('section.section','class.class')->first();
                $input['class_name']=$get_cls->class;
                $input['section_name']=$get_cls->section;
                $attendances = \DB::table('attendance')
                    ->where('attendance.student_id', $students1[0]->id)
                    ->where('attendance.school_id', \Auth::user()->school_id)
                    ->whereBetween('attendance.date', array($from, $to))
                    ->join('student', 'attendance.student_id', '=', 'student.id')
                    ->select('attendance.id', 'attendance.attendance', 'attendance.attendance_session', 'attendance.date', 'student_id', 'attendance.remarks', 'attendance.attendance_by', 'student.name as student_name')
                    ->get();
                $total_workingdays=\DB::table('attendance_status')->where('school_id',\Auth::user()->school_id)
                        //->where('date',$date)->where('attendance_session','am')
                        ->whereBetween('date', array($from, $to))
                        ->where('class_id', $class)->where('section_id', $section)
                        ->count();
                        //dd($total_workingdays);
                
                $input['from']=$from;
                $input['to']=$to;
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
                   // dd('kkk',$attendances);
                foreach ($attendances as $key => $value) {
                    if ($value->attendance_session == 'am') {
                        $am_attendance[] = $value;
                    } elseif ($value->attendance_session == 'pm') {
                        $pm_attendance[] = $value;
                    }
                 }
                 
                $attendance_date = $this->get_inbetween_date($input['from'], $input['to']);
                //dd($attendance_date,$input['from'],$input['to']);
                    if($attendance_date == NULL){
                        $input['error'] = 'no attendance at that date';
                        return \Redirect::back()->withInput($input);
                    }
                    
            foreach ($attendance_date as $date) {
                foreach ($students1 as $key => $value) {
                    foreach ($am_attendance as $att_key => $att_value) {
                        $exist = \DB::table('attendance_status')->
                        where('school_id',\Auth::user()->school_id)
                        ->where('date',$date)->where('attendance_session','am')
                        ->where('class_id', $value->class_id)->where('section_id', $value->section_id)
                        ->first();
                    if(!empty($exist)){
                        $update_am_time=$exist->created_at;
                        if($exist->updated_at!=''&&$exist->updated_at!=0){
                            $update_am_time=$exist->updated_at;
                           // dd('ddddddddd');
                        }
                        //dd('am',$update_am_time);
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
                        }else{
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
                        //dd('pm',$update_pm_time);
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
                        }else{
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
        //dd($attendances);
       // dd($no_of_grades,$parents,$classesDate,$input,$session,$total_workingdays,$students,$currentSchool,$exams,$grades,$attendances,$am_totalLeave,$am_totalPresent,$am_totalAbsent,$pm_totalLeave,$pm_totalPresent,$pm_totalAbsent,$students1,$pm,$am,$attendance_date);
        $pdf = \PDF::loadView('users.result.invoice_hrsecresult1',compact('no_of_grades','parents','classesDate','input','session','total_workingdays','students','currentSchool','exams','grades','attendances', 
            'am_totalLeave', 'am_totalPresent', 'am_totalAbsent', 'pm_totalLeave', 'pm_totalPresent', 'pm_totalAbsent', 'students1', 'pm', 'am', 'attendance_date'));
        return $pdf->download('MarkReports.pdf');
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

    /** @ Report for Teacher Analyst @ **/
    public function teacherReport()
    {
        //return 'Teacher Analyst';
        $exam_type = \DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 6-6-2018
            ->get();
        $getTeachers =\DB::table('teacher')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.report.teacherAnalystReport', compact('getTeachers', 'exam_type', 'classes'));
    }

    public function getTeacherReport()
    {
        $input = \Request::all();
        // dd($input['exam_type']);

        $userError = ['exam_type' => 'Exam Type '];
        $validator = \Validator::make($input, [
            'exam_type' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);

        if ($validator->fails())
        {
            //return 'error';
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        elseif($input['class'] && $input['section'])
        {
            //return 'class & section';

            /** @ To get report based on examtype , class and section@ **/
            $getTeacherDetails =\DB::table('result')
                ->leftJoin('teacher','result.teacher_id', '=', 'teacher.id')
                ->leftJoin('users', 'teacher.user_id', '=', 'users.id')
               // ->leftJoin('student', 'result.student_id', '=', 'student.id')
                ->leftJoin('class', 'class.id', '=', 'result.class_id')
                ->leftJoin('section', 'section.id', '=', 'result.section_id')
                ->leftJoin('subject', 'subject.id', '=', 'result.subject_id')
                ->leftJoin('exam', 'exam.id', '=', 'result.exam_type_id')
                ->where('result.class_id', $input['class'])
                ->where('result.section_id', $input['section'])
               // ->where('student.session_id', $session->id)
                ->where('result.school_id',\Auth::user()->school_id);

            if($input['exam_type'] != 0 )
            {
                //return 'single examtype Report';
                $getTeacherDetails = $getTeacherDetails->where('result.exam_type_id', $input['exam_type']);
            }

            $getTeacherDetails =$getTeacherDetails->select(
                DB::raw( "SUM(CASE WHEN result.obtained_marks >= 80 AND result.obtained_marks !='AB' AND result.obtained_marks !='-' AND result.obtained_marks !='Absent' AND result.obtained_marks !='Ab' AND result.obtained_marks !='ab' THEN 1 ELSE 0 END) AS 'top_students'" ),
                DB::raw( "SUM(CASE WHEN result.obtained_marks >= result.pass_marks AND result.obtained_marks !='AB' AND result.obtained_marks !='-' AND result.obtained_marks !='Absent' AND result.obtained_marks !='Ab' AND result.obtained_marks !='ab' THEN 1 ELSE 0 END) AS 'total_students_pass'" ),
                DB::raw( "SUM(CASE WHEN result.obtained_marks < result.pass_marks AND result.obtained_marks !='AB' AND result.obtained_marks !='-' AND result.obtained_marks !='Absent' AND result.obtained_marks !='Ab' AND result.obtained_marks !='ab' THEN 1 ELSE 0 END) AS 'total_students_fail'" ),
                DB::raw("SUM(CASE WHEN result.obtained_marks = 'AB' OR result.obtained_marks ='-' OR result.obtained_marks ='Absent' OR result.obtained_marks ='Ab'OR result.obtained_marks ='ab' THEN 1 ELSE 0 END) AS 'absent_students'"),
                //DB::raw("SUM(CASE WHEN result.obtained_marks = 'AB'  THEN 1 ELSE 0 END) AS 'absent_students'"),
                DB::raw("sum(result.max_marks) as total_max,sum(result.obtained_marks) as scored_marks,sum(result.pass_marks) as total_pass_mark"),
                'class.class',
                'section.section',
                'exam.exam_type',
                'teacher.name',
                'users.username',
                'result.date',
                'subject.subject',
                'result.max_marks',
                'result.pass_marks',
                'result.obtained_marks',
                'result.result',
                'result.total_students',
                'result.grade'
            )
                ->orderBy('scored_marks', 'desc')
                 ->groupBy('result.teacher_id','result.exam_type_id','result.subject_id','result.section_id')
                //->groupBy('result.exam_type_id','result.subject_id')
                ->get();
            /** @ End report based on examtype , class and section @ **/

            //dd($getTeacherDetails);
        }
        // elseif($input['teacher_id'])
        else
        {
            //return 'teacher';

            /** @ To get report based on examtype , All Teachers @ **/
            $getTeacherDetails =\DB::table('result')
                ->leftJoin('teacher','result.teacher_id', '=', 'teacher.id')
                ->leftJoin('users', 'teacher.user_id', '=', 'users.id')
                ->leftJoin('class', 'class.id', '=', 'result.class_id')
                ->leftJoin('section', 'section.id', '=', 'result.section_id')
                ->leftJoin('subject', 'subject.id', '=', 'result.subject_id')
                ->leftJoin('exam', 'exam.id', '=', 'result.exam_type_id')
                ->where('result.school_id',\Auth::user()->school_id);

            if($input['exam_type'] != 0 )
            {
                // return 'single examtype Report';
                $getTeacherDetails = $getTeacherDetails->where('result.exam_type_id', $input['exam_type']);
            }

            $getTeacherDetails = $getTeacherDetails->select(
                DB::raw( "SUM(CASE WHEN result.obtained_marks >= 80 / (100 / result.max_marks) AND result.obtained_marks !='AB' AND result.obtained_marks !='-' AND result.obtained_marks !='Absent' AND result.obtained_marks !='Ab' AND result.obtained_marks !='ab' THEN 1 ELSE 0 END) AS 'top_students'" ),
                DB::raw( "SUM(CASE WHEN result.obtained_marks >= result.pass_marks AND result.obtained_marks !='AB' AND result.obtained_marks  !='-' AND result.obtained_marks !='Absent' AND result.obtained_marks !='Ab' AND result.obtained_marks !='ab'  THEN 1 ELSE 0 END) AS 'total_students_pass'" ),
                DB::raw( "SUM(CASE WHEN result.obtained_marks < result.pass_marks AND result.obtained_marks !='AB' AND result.obtained_marks !='-' AND result.obtained_marks !='Absent' AND result.obtained_marks !='Ab' AND result.obtained_marks !='ab' THEN 1 ELSE 0 END) AS 'total_students_fail'" ),
                //DB::raw("count(result.student_id) as exam_students,sum(result.max_marks) as total_max,sum(result.obtained_marks) as scored_marks,sum(result.pass_marks) as total_pass_mark"),
                DB::raw("SUM(CASE WHEN result.obtained_marks = 'AB' OR result.obtained_marks ='-' OR result.obtained_marks ='Absent' OR result.obtained_marks ='Ab'OR result.obtained_marks ='ab' THEN 1 ELSE 0 END) AS 'absent_students'"),
                //DB::raw("SUM(CASE WHEN result.obtained_marks = 'AB' THEN 1 ELSE 0 END) AS 'absent_students'"),
                DB::raw("sum(result.max_marks) as total_max,sum(result.obtained_marks) as scored_marks,sum(result.pass_marks) as total_pass_mark"),
                /* end */
                'class.class',
                'section.section',
                'exam.exam_type',
                'teacher.name',
                'users.username',
                'result.date',
                'subject.subject',
                'result.max_marks',
                'result.pass_marks',
                'result.obtained_marks',
                'result.result',
                'result.total_students',
                'result.grade'
            )
                ->orderBy('scored_marks', 'desc')
                //->groupBy('result.exam_type_id','result.subject_id')
                ->groupBy('result.teacher_id','result.exam_type_id','result.subject_id','result.section_id')
                ->get();
            /** @ End report based on examtype & all Teachers @ **/

        }
         //dd($getTeacherDetails);

        $teacher_id =$input['teacher_id'];

        //Get Active Session
        $getSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)->where('active',1)->first();

        //For Excel sheet
        $currentDate = date("d-m-Y_H_i_s");
        \Excel::create("teacherAnalystReport_" . $currentDate, function ($excel) use ($getTeacherDetails, $getSession,$teacher_id) {

            $excel->sheet('Excel sheet', function ($sheet) use ($getTeacherDetails, $getSession,$teacher_id) {
                $sheet->loadView('users.report.teacherExport')->with('getTeacherDetails', $getTeacherDetails)->with('getSession', $getSession)->with('teacher_id', $teacher_id);
                $sheet->setOrientation('portrait');
            });
        })->store('xls', storage_path('teacherReport'));

        $fileURL = storage_path() . "/teacherReport/teacherAnalystReport_" . $currentDate . '.xls';
        \Session::put('getReportUrl', $fileURL);

        $exam_type = \DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)->get();

        return view('users.report.teacherAnalystReport', compact('teacher_id','getTeacherDetails', 'exam_type','classes'));

    }

    /** @ Get Section For Class @ **/
    public function getStudentSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }
    public function viewStudentfeeSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }

     /** @ Get Section For Class @ **/
    public function getStudenthomevisitrepSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }

    /** @ Get Section For Class @ **/
    public function getStudenthomevisitSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }
     /** @ Get Section For Class @ **/
    public function getReporthomevisitSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }

    public function singStu_homevisitStudent()
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

    public function poststudenthomevisitdetails() 
     {
        $input = \Request::all();
       
        $studentsid=$input['student'];
        $classid=$input['class'];
        $sectionid=$input['section'];
        
                    
                    $students = \DB::table('student')
                            ->where('student.id','=', $input['student'])->join('parent', 'student.parent_id', '=', 'parent.id')
                            ->select('student.name as student_name', 'parent.mobile','parent.address','parent.name')
                            ->first();
                    $classdata=\DB::table('class')->select('class')->where('id',$classid)->first();
                    $sectionData = \DB::table('section')->select('section')->where('id',$sectionid)->first();

            //dd($classdata1->class);
        $class=$classdata->class;
        $section=$sectionData->section;
        $school = school::where('id', Auth::user()->school_id)->first();
        $received_by=\Auth::user()->username;
        if($input['type'] == 'homevisit'){
            $listpoints = array("Is the student obedient at home?", "Is he/she using mobile phones or social media?","Does he/she speak in English with his/her siblings?", 
             "Are the parents aware of coming year changes such as fees, uniform, books etc?");
        $troublelist = array("trouble1", "trouble2","trouble3", 
            "trouble4","trouble5", "trouble6","trouble7");
        $parentslist = array("suggestion1", "suggestion2","suggestion3", 
            "suggestion4","suggestion5", "suggestion6","suggestion7");
        
        $yeslist=array("yes", "yes","yes","yes","yes", "yes","yes","yes", "yes","yes");
        $usererror = [
            'class' => 'class ',
            'section' => 'Section',
            'student' => 'Student '
        ];
        $validator = \Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'student' => 'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', "Given field is incorrect");
        }
        else{
            return view('users.homevisit.addhomevisit', compact('parentslist','troublelist','sectionid','classid','students','class','section','listpoints','school','received_by','yeslist','studentsid'));
        }
    }else{
        
        $usererror = [
            'class' => 'class ',
            'section' => 'Section',
            'student' => 'Student '
        ];
        $validator = \Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'student' => 'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if ($validator->fails())
        {
            return redirect()->back()->with('error', "Given field is incorrect");
        }
        else{
            return view('users.homevisit.adddailyhomevisit', compact('parentslist','troublelist','sectionid','classid','students','class','section','listpoints','school','received_by','yeslist','studentsid'));
        }
    }
        
        
     }
public function posthomevisitdetails() 
     {
       $input = \Request::all();
      // dd($input);
       if($input['type'] == 'dailyvisit'){
       // dd('daily visit');
       
        $school = school::where('id', Auth::user()->school_id)->first();
        $teacher_name=\Auth::user()->username;
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
        $currentDate = date("d-m-Y");
        $sessionid=$session->id;
        
        //dd($input);
        $result=DB::table('homevisitchcklist')->insert(
            array(
            
            'school_id' => Auth::user()->school_id,
            'session' => $sessionid,
            'class_id' => $input['classid'],
            'section_id' => $input['sectionid'],
            'student_id' => $input['studentsid'],
            'date' => $currentDate,
            'teacher_name' => $teacher_name,

             'whatsapp_no' => $input['whatsapp_no'],
            'fees' => $input['fees'],
            'c3' => $input['c3'],
            'onlinetest' => $input['onlinetest'],
            'lkgadm' => $input['lkgadm'],
            'stdadm' => $input['stdadm'],
            'album' => $input['album'],
            'remarks' => $input['remarks'],
            'type' => $input['type'],
            
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
                    ));
      
        //dd('inserted');
       $msg['success'] = 'Daily Home Visit Check List Inserted Succesfully';
        return \Redirect::back()->withInput($msg);
       }else{
        $description=array("nil","nil","nil","nil");
        array_push($description, $input['designation1'],$input['designation2'],$input['designation3'],$input['designation4'],$input['designation5'],$input['designation6']);
        array_push($input['select_type21'], $input['select_type1'],$input['select_type2'],$input['select_type3'],$input['select_type4'],$input['select_type5'],$input['select_type6']);
        $enq_points=$input['points'];
        $enq_yes_no_status=$input['select_type21'];
        //$trouble_points=$input['trouble'];
        $trouble_points=$input['trou_points'];
        $troub_status=$input['select_type22'];
        $parents_img=$input['image'];
        $school = school::where('id', Auth::user()->school_id)->first();
        $teacher_name=\Auth::user()->username;
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)->select('id')
            ->first();
        $currentDate = date("d-m-Y");
        $sessionid=$session->id;
        
        foreach($enq_points as $key =>$value)
            {
        $result=DB::table('homevisitchcklist')->insert(
            array(
            
            'school_id' => Auth::user()->school_id,
            'session' => $sessionid,
            'class_id' => $input['classid'],
            'section_id' => $input['sectionid'],
            'student_id' => $input['studentsid'],
            'date' => $currentDate,
            'teacher_name' => $teacher_name,
            'enq_points' => $enq_points[$key],
            'en_status' => $enq_yes_no_status[$key],
            'enq_descript'=>$description[$key],
            'trouble_stud' => $trouble_points[$key],
            'troub_status'=>$troub_status[$key],
            'stud_remarks' => $input['trouble'],
            'par_image'=>$parents_img,
            'parents_points'=>$input['sugestion'],
            //'parents_status' => $parentsyes_list[$key],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
                    ));
        }
        //dd('inserted');
       $msg['success'] = 'Home Visit Check List Inserted Succesfully';
        return \Redirect::back()->withInput($msg);
       }
        
     }

     public function poststudenthomevisitreportdetails() 
     {
        $input = \Request::all();
       
        
        //dd('hi',$input);

            
            $selected_class=$input['class'];
            $selected_section=$input['section'];
            
            
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();

        $getSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active','1')
            ->select('id','session')
            ->first();
        //$classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    //->where('session_id','=',$this->active_session->id)
                    
                    //->get();
           // $class = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    //->where('session_id','=',$this->active_session->id)
                    //->where('id','=',$input['class'])
                    //->first();
            //$section = \DB::table('section')->where('school_id', \Auth::user()->school_id)
                    //->where('session_id','=',$this->active_session->id)
                   // ->where('id','=',$input['section'])
                   // ->first();
                    //dd('class',$class->class,$section->section);

             
        if ($input) 
        {
            

            //dd('from',$input['from'],'from',$input['to']);
            $getStudent = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->where('student.class_id', $input['class'])
                ->where('student.section_id', $input['section'])->select('student.id', 'student.name' ,'student.section_id','parent.mobile','class.class','section.section')->get();
            //dd('hiii',$getStudent);
        }

            
            foreach ($getStudent as $student ) {
               

                    $checkvisitExist =\DB::table('homevisit')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class_id',$input['class'])
                    ->where('section_id',$input['section'])
                    ->where('student_id',$student->id)->first();
                    //dd($student,$checkvisitExist);
                    if($checkvisitExist)
                    {
                    $getvisit = \DB::table('homevisit')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$input['class'])
                        ->where('section_id',$input['section'])
                        ->where('student_id',$student->id)->get();
                    }
                    
                    
                    
                    foreach ($getvisit as $home ) {
                    $student_name=$home->name;
                    
                    }
                    dd($student->name);
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
                
             
        return view('users.report.sion.fee_collection1234', compact('school','total_paidAmt','total_studentAmt','total_balancAmt','getStudent','classes','selected_staff','selected_class','selected_term','selected_from','selected_to'));
        
     }
    /** @ Get Section For Class @ **/
    public function getStudentfeeSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }

     /** @ Get Section For Class @ **/
   public function getStates()
    {
        $srbus = \Request::get('srbus');
       
        $getRoutes = \DB::table('route_details')->where('bus_id',$srbus)->get();
        return $getRoutes;
    }
     public function getroutef()
    {
        $srbuss = \Request::get('srbuss');
        //return $srbuss;exit;
        $getRoutess = \DB::table('route_details')->where('bus_id',$srbuss)->get();
        return $getRoutess;
    }
    public function singStu_feeStudent()
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

    public function singStu_feebusStudent()
    {
        $srbus = \Request::get('srbus');
        $srroute = \Request::get('srroute');
        //return $srroute; exit;
        
        $getBoarding = \DB::table('boarding')->where('bus_id',$srbus)
            ->where('route_id',$srroute)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('school_id',\Auth::user()->school_id)
            ->select('id','boarding')
            ->get();
        return $getBoarding;
    }

    /*********************************************************************************
     *                            ADD MARKS MODULE
     **********************************************************************************/

    /** @ View Add mark page @ **/
    public function getStudentsMarks()
    {
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 6-6-2018
            ->get();
        $exam_type =\DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        $teachers =\DB::table('teacher')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.result.studentDetails', compact('teachers','classes','exam_type'));
    }

    /**  @ Get Exam Type Detail  @ **/
    public function addStudentsMarksSection()
    {
        /*
         * updated by priya
         * $class_id = \Request::get('srcclass');
        $section_id = \Request::get('srcsection');
        $getTeacher = \DB::table('teacher')
            ->where('class',$class_id)
            ->where('section',$section_id)
            ->select('teacher.id','teacher.name')
            ->get();
        return $getTeacher;*/

        $getExamType =\DB::table('exam')
            ->where('school_id', \Auth::user()->school_id)->get();
        return $getExamType;
    }

    /**  @ Get Teacher Detail  @ **/
    public function getTeachersDetails()
    {

        $class_id = \Request::get('srcclass');
        $section_id = \Request::get('srcsection');
        /*$getTeacher = \DB::table('teacher')
            ->where('school_id', \Auth::user()->school_id)->get();*/
        $getTeacher = \DB::table('time-table')
            ->where('time-table.class_id',$class_id)
            ->where('time-table.section_id',$section_id)
            ->where('time-table.school_id', \Auth::user()->school_id)
            ->leftJoin('teacher','teacher.id','=','time-table.teacher_id')
            ->select('teacher.id','teacher.name')
            ->groupBy('teacher.id')
            ->get();
        return $getTeacher;
    }

    /** @ Get Subject for teacher @ **/
    public function addStudentsExamResult()
    {
        //return 'Get Subject';
        $class_id = \Request::get('srclass');
        $section_id = \Request::get('srcsection');
        $teacher_id = \Request::get('srcteacher');
        $exam_id = \Request::get('srcexamtype');

        /*
         * Updated 3-11-2017 by priya
         * $getSubject = \DB::table('time-table')
            ->where('class_id',$class_id)
            ->where('section_id',$section_id)
            ->where('teacher_id',$teacher_id)
            ->leftJoin('subject','subject.id','=','time-table.subject_id')
            ->select('subject.id','subject.subject')
            ->get();
        return $getSubject;
        */

        $getSubject = \DB::table('time-table')
            ->select('*')
            ->whereNotIn('subject_id',function($query) use($class_id,$section_id,$teacher_id,$exam_id)
            {
                $query->select('subject_id')->from('result')
                    ->where('class_id',$class_id)
                    ->where('section_id',$section_id)
                    ->where('exam_type_id',$exam_id)
                    ->where('teacher_id',$teacher_id);
            })
            ->leftJoin('subject','subject.id','=','time-table.subject_id')
            ->where('class_id',$class_id)
            ->where('section_id',$section_id)
            ->where('teacher_id',$teacher_id)
            ->groupBy('time-table.subject_id')
            ->get();
        return $getSubject;
    }

     public function postStudentsExamResultverify()
    {
        //return 'post student result';
        $input = \Request::all();
        //dd($input);
        
            //return 'success';
            $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
                ->where('active', '1')
                ->select('id')
                ->first();
//dd('matric');
            //Get Present students from attendance
            $exam_date = $input['getExamDate'];
            $getAbsentStudent = \DB::table('student')
                ->where('student.school_id',\Auth::user()->school_id)
                ->where('student.session_id',$session->id)
                ->where('student.class_id',$input['class'])
                ->where('student.section_id',$input['section'])
                ->leftJoin('attendance','attendance.section_id','=','student.section_id')
                ->whereDate('attendance.date','=',$exam_date)
                ->where('attendance.attendance_session',$input['atd_session'])
                ->select('student.*','attendance.student_id as absent_student')
                ->groupBy('student.id')/* updated 22-11-2017 by priya*/
                ->get();
            if($getAbsentStudent)
            {
                //return 'value';
                $getStudents = $getAbsentStudent;
            } 
            else
            {
                //return 'no value';
                $getStudents = \DB::table('student')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$session->id)
                    ->where('class_id',$input['class'])
                    ->where('section_id',$input['section'])
                    ->get();
            }
            //dd($getStudents);

            //Get Pass & Max Marks obtained in exam type
            $getPassMarks = \DB::table('exam')->where('id',$input['examtype']) ->get();

            //Get Class & Section Name
            $getStudentClass = \DB::table('class')->where('school_id',\Auth::user()->school_id)
                 ->where('session_id',$this->active_session->id)//updated 14-4-2018   
                ->where('id',$input['class'])
                ->first();
            $getStudentSection = \DB::table('section')->where('school_id',\Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('id',$input['section'])
                ->where('class_id',$input['class'])
                ->first();

      
        //Get All Class & Exam Type
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $exam_type =\DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        $obtaiend_marks= $input['obtained_marks'];
        $grade= $input['grade'];
        $result= $input['result'];
        $exam_type = $input['examtype'];
        // dd($getStudentClass,$getStudentSection,$input,$obtaiend_marks,$getPassMarks,$input['examtype'],$exam_date);
        
            
        return view('users.result.addStudentMarkverify', compact('exam_type','exam_date','getStudentClass','getStudentSection','exam_type','getStudents','classes','result_by','getPassMarks','getSubject','input','getAbsentStudent','obtaiend_marks','grade','result'));
            
      
       
    }

    /** @ Get Students Detail with Class & Section @ **/
    public function getStudentsResultDetail()
    {
        $input = \Request::all();
        //dd($input);

        $userError = ['class' => 'Class',
            'section' => 'Section',
            'exam' => 'Exam',
            'teacher' => 'Teacher',
            'subject' => 'Subject',
            'atd_session' => 'Session',
            'exam_date' => 'Exam Date'
        ];
        $validator = \Validator::make($input, [
            'class' => 'required',
            'section' => 'required',
            'exam' => 'required',
            'teacher' => 'required',
            'subject' => 'required',
            'atd_session' => 'required',
            'exam_date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
         if($input['markcategry']=='Matriculation'){
            if ($validator->fails())
        {
            // return 'error';
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            //return 'success';
            $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
                ->where('active', '1')
                ->select('id')
                ->first();
//dd('matric');
            //Get Present students from attendance
            $exam_date = $input['exam_date'];
            $getAbsentStudent = \DB::table('student')
                ->where('student.school_id',\Auth::user()->school_id)
                ->where('student.session_id',$session->id)
                ->where('student.class_id',$input['class'])
                ->where('student.section_id',$input['section'])
                ->leftJoin('attendance','attendance.section_id','=','student.section_id')
                ->whereDate('attendance.date','=',$exam_date)
                ->where('attendance.attendance_session',$input['atd_session'])
                ->select('student.*','attendance.student_id as absent_student')
                ->groupBy('student.id')/* updated 22-11-2017 by priya*/
                ->get();
            if($getAbsentStudent)
            {
                //return 'value';
                $getStudents = $getAbsentStudent;
            } 
            else
            {
                //return 'no value';
                $getStudents = \DB::table('student')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$session->id)
                    ->where('class_id',$input['class'])
                    ->where('section_id',$input['section'])
                    ->get();
            }
            //dd($getStudents);

            //Get Pass & Max Marks obtained in exam type
            $getPassMarks = \DB::table('exam')->where('id',$input['exam']) ->get();

            //Get Class & Section Name
            $getStudentClass = \DB::table('class')->where('school_id',\Auth::user()->school_id)
                 ->where('session_id',$this->active_session->id)//updated 14-4-2018   
                ->where('id',$input['class'])
                ->first();
            $getStudentSection = \DB::table('section')->where('school_id',\Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('id',$input['section'])
                ->where('class_id',$input['class'])
                ->first();

        }
        //Get All Class & Exam Type
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $exam_type =\DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.result.addStudentMark', compact('getStudentClass','getStudentSection','exam_type','getStudents','classes','result_by','getPassMarks','getSubject','input','getAbsentStudent'));
            
        }elseif($input['markcategry']=='Higher Secondary'){
            if ($validator->fails())
        {
            // return 'error';
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            //return 'success';
            $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
                ->where('active', '1')
                ->select('id')
                ->first();
                //dd('hr sec');
            //Get Present students from attendance
            $exam_date = $input['exam_date'];
            $getAbsentStudent = \DB::table('student')
                ->where('student.school_id',\Auth::user()->school_id)
                ->where('student.session_id',$session->id)
                ->where('student.class_id',$input['class'])
                ->where('student.section_id',$input['section'])
                ->leftJoin('attendance','attendance.section_id','=','student.section_id')
                ->whereDate('attendance.date','=',$exam_date)
                ->where('attendance.attendance_session',$input['atd_session'])
                ->select('student.*','attendance.student_id as absent_student')
                ->groupBy('student.id')/* updated 22-11-2017 by priya*/
                ->get();
            if($getAbsentStudent)
            {
                //return 'value';
                $getStudents = $getAbsentStudent;
            } 
            else
            {
                //return 'no value';
                $getStudents = \DB::table('student')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$session->id)
                    ->where('class_id',$input['class'])
                    ->where('section_id',$input['section'])
                    ->get();
            }
            //dd($getStudents);

            //Get Pass & Max Marks obtained in exam type
            $getPassMarks = \DB::table('exam')->where('id',$input['exam']) ->get();

            //Get Class & Section Name
            $getStudentClass = \DB::table('class')->where('school_id',\Auth::user()->school_id)
                 ->where('session_id',$this->active_session->id)//updated 14-4-2018   
                ->where('id',$input['class'])
                ->first();
            $getStudentSection = \DB::table('section')->where('school_id',\Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('id',$input['section'])
                ->where('class_id',$input['class'])
                ->first();

        }
        //Get All Class & Exam Type
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $exam_type =\DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.result.addStudenthrsecMark', compact('getStudentClass','getStudentSection','exam_type','getStudents','classes','result_by','getPassMarks','getSubject','input','getAbsentStudent'));
        }else{
        if ($validator->fails())
        {
            // return 'error';
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            //return 'success';
            $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
                ->where('active', '1')
                ->select('id')
                ->first();
                //dd('hr sec');
            //Get Present students from attendance
            $exam_date = $input['exam_date'];
            $getAbsentStudent = \DB::table('student')
                ->where('student.school_id',\Auth::user()->school_id)
                ->where('student.session_id',$session->id)
                ->where('student.class_id',$input['class'])
                ->where('student.section_id',$input['section'])
                ->leftJoin('attendance','attendance.section_id','=','student.section_id')
                ->whereDate('attendance.date','=',$exam_date)
                ->where('attendance.attendance_session',$input['atd_session'])
                ->select('student.*','attendance.student_id as absent_student')
                ->groupBy('student.id')/* updated 22-11-2017 by priya*/
                ->get();
            if($getAbsentStudent)
            {
                //return 'value';
                $getStudents = $getAbsentStudent;
            } 
            else
            {
                //return 'no value';
                $getStudents = \DB::table('student')->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$session->id)
                    ->where('class_id',$input['class'])
                    ->where('section_id',$input['section'])
                    ->get();
            }
            //dd($getStudents);

            //Get Pass & Max Marks obtained in exam type
            $getPassMarks = \DB::table('exam')->where('id',$input['exam']) ->get();

            //Get Class & Section Name
            $getStudentClass = \DB::table('class')->where('school_id',\Auth::user()->school_id)
                 ->where('session_id',$this->active_session->id)//updated 14-4-2018   
                ->where('id',$input['class'])
                ->first();
            $getStudentSection = \DB::table('section')->where('school_id',\Auth::user()->school_id)
                ->where('session_id',$this->active_session->id)//updated 14-4-2018
                ->where('id',$input['section'])
                ->where('class_id',$input['class'])
                ->first();

        }
        //Get All Class & Exam Type
        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        $exam_type =\DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.result.addStudentFASAMark', compact('getStudentClass','getStudentSection','exam_type','getStudents','classes','result_by','getPassMarks','getSubject','input','getAbsentStudent'));
        }}
        public function viewstudentwisefeedetails()
    {
       $input = \Request::all();
       //dd($input);
       if ($input) 
        {
            $getFee_stu=\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('class_id',$input['class'])
                    ->where('bus_route','=',null)
                    ->where('route_id','=',null)
                    ->where('bal_status','=', null)
                    ->where('student_id','!=',null)
                    ->where('reg_no','!=',null)
                    ->groupBy('student_id')
                    ->get();
            
            foreach ($getFee_stu as $key => $value) {
               $StudentFeeid[]=$value->student_id;
            }
               // dd($StudentFeeid);
            foreach ($StudentFeeid as $key => $value) {
               //$visitedStuID[]=$value->student_id;
               $getStudent[] = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->where('student.class_id', $input['class'])
                ->where('student.section_id', $input['section'])
                ->where('student.id', $value)->select('student.id', 'student.name' ,'student.section_id','parent.mobile','class.class','section.section','student.class_id')->get();
     
            }
           
        }
       
       //dd($getStudent);
            foreach ($getStudent as $student ) {
                foreach ($student as $student1) {
 
                    $getfee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$student1->class_id)
                        ->where('bus_route','=',null)
                        ->where('route_id','=',null)
                        ->where('bal_status','=', null)
                        //->where('section_id',$input['section'])
                        ->where('student_id',$student1->id)->get();
                    $student1->getfee = $getfee;
                //dd($student1);
                    foreach ($getfee as $key => $value) {
                        //$class[]=$value->class;
                        $payment_type[]=$value->payment_type;
                        $fees_name[]=$value->fees_name;
                        $amount[]=$value->amount;
                        $dates[]=$value->created_at;
                        $fee_id[]=$value->id;
                        //$dates = $value->created_at;
                    $nice_date[] = date('d-m-Y', strtotime( $value->created_at ));
                        //dd($nice_date);

                        //$student1->class = $class;
                    $student1->payment_type = $payment_type;
                    $student1->fees_name = $fees_name;
                    $student1->amount = $amount;
                    $student1->fee_id = $fee_id;

                      
                    $student1->dates = $nice_date;
                //dd($student1);
                }
                $counts=count($fees_name);
                $student1->counts = $counts;
                
                }
                    }
                   //dd($student1); 
                $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('id','=',$input['class'])
                    ->first();
            $section = \DB::table('section')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('id','=',$input['section'])
                    ->first();
             return view('users.fee_structure.sion.singlestudent_paymentstr', compact('getStudent','school','classes','section'));   
    }

    public function viewbuswisebusfeedetails()
    {
       $input = \Request::all();
       
       if ($input) 
        {
            $getFee_stu=\DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//20-4-2018
                    ->where('bus_id',$input['bus'])
                    ->where('route_id',$input['routes'])
                    ->where('bal_status','=', null)
                    ->groupBy('student_id')
                    ->get();
            foreach ($getFee_stu as $key => $value) {
               $StudentFeeid[]=$value->student_id;
               $getStudent[] = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->where('student.id', $value->student_id)->select('student.id', 'student.name' ,'student.section_id','class.class','section.section','student.class_id')->get();
     
            }
        }
       
       //dd($getStudent,$getStudent1);
            foreach ($getStudent as $student ) {
                foreach ($student as $student1) {
 
                    $getfee = \DB::table('sionfee_structure')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$student1->class_id)
                        ->where('bus_id',$input['bus'])
                        ->where('route_id',$input['routes'])
                        ->where('student_id',$student1->id)->get();

                $student1->getfee = $getfee;
                     }
                    }
                $school = \DB::table('school')
                    ->where('id', \Auth::user()->school_id)
                    ->first();
            $routes1 = \DB::table('route_details')->where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('id',$input['routes'])
            ->first();
            //dd($routes1);
            $busses1 = \DB::table('busdetails')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->where('id',$input['bus'])
                        ->first();
                 $classes = \DB::table('class')->where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
            $busses = \DB::table('busdetails')->where('school_id', \Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)
                        ->get();
             return view('users.fee_structure.sion.singlestudent_buspaymentstr', compact('getStudent','school','busses1','routes1','classes','busses'));    
    }
     public function viewgetStates()
    {
        $srbus = \Request::get('srbus');
        //return $srbus;exit;
        $getRoutes = \DB::table('route_details')->where('bus_id',$srbus)->get();
        return $getRoutes;
    }

        public function postStudentsExamhrsecResult()
    {
        //return 'post student result';
        $input = \Request::all();
        //dd('hrsec',$input);
        $current_month = date('F',strtotime($input['getExamDate']));
        //echo $input['student_id'][0];
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', '1')
            ->select('id')
            ->first();

      
        foreach($input['student_id'] as $key => $value)
            {
                
                if($input['obtained_marks'][$key] == '')
                {
                    $input['error'] = "You Can't leave the Obtained Marks field empty !!!";
                    return \Redirect::back()->withInput($input);
                }

            }
            foreach($input['student_id'] as $key => $value)
            {
                if($input['obtained_marks'][$key] == 'AB')
                {
                    $input['result'][$key] = 'fail';
                    $input['grade'][$key] = 'NA';
                }
                $addResult = \DB::table('result')
                    ->insert([
                        'school_id' => \Auth::user()->school_id,
                        // 'session_id' => $session->id,
                        'class_id' => $input['class'],
                        'section_id' => $input['section'],
                        'exam_type_id' => $input['examtype'],
                        'total_students' => $input['countStudent'],
                        'month' => $current_month,
                        'subject_id' => $input['subject'],
                        'student_id' => $input['student_id'][$key],
                        'teacher_id' => $input['teacher'],
                        'date' => $input['getExamDate'],
                        'max_marks' => $input['max_marks'],
                        'pass_marks' => $input['pass_marks'],
                        'obtained_marks' => $input['obtained_marks'][$key],
                        'practical_marks' => $input['practical_marks'][$key],
                        'theory_marks' => $input['theory_marks'][$key],
                        'result' => $input['result'][$key],
                        //'grade' => $input['grade'][$key],
                        'category' => 'HigherSecondary',
                        'result_by' => 'School'
                    ]);
            }

        if($addResult)
        {
            $input['success'] = 'Marks Added To the Students Successfully !!!';
        }
        else
        {
            $input['error'] = 'Error in adding Marks To Students !!!';
        }

        return \Redirect::back()->withInput($input);
    }

     public function postStudentsExamfasaResult()
    {
        //return 'post student result';
        $input = \Request::all();
        //dd('hrsec',$input);
        $current_month = date('F',strtotime($input['getExamDate']));
        //echo $input['student_id'][0];
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', '1')
            ->select('id')
            ->first();

      
        foreach($input['student_id'] as $key => $value)
            {
                
                if($input['faobtained_marks'][$key] == '')
                {
                    $input['error'] = "You Can't leave the FA Obtained Marks field empty !!!";
                    return \Redirect::back()->withInput($input);
                }
                if($input['saobtained_marks'][$key] == '')
                {
                    $input['error'] = "You Can't leave the SA Obtained Marks field empty !!!";
                    return \Redirect::back()->withInput($input);
                }

            }
            foreach($input['student_id'] as $key => $value)
            {
                if($input['faobtained_marks'][$key] == 'AB')
                {
                    $input['result'][$key] = 'fail';
                    $input['fagrade'][$key] = 'NA';
                }
                if($input['saobtained_marks'][$key] == 'AB')
                {
                    $input['result'][$key] = 'fail';
                    $input['sagrade'][$key] = 'NA';
                }
                $addResult = \DB::table('result')
                    ->insert([
                        'school_id' => \Auth::user()->school_id,
                        // 'session_id' => $session->id,
                        'class_id' => $input['class'],
                        'section_id' => $input['section'],
                        'exam_type_id' => $input['examtype'],
                        'total_students' => $input['countStudent'],
                        'month' => $current_month,
                        'subject_id' => $input['subject'],
                        'student_id' => $input['student_id'][$key],
                        'teacher_id' => $input['teacher'],
                        'date' => $input['getExamDate'],
                        'max_marks' => $input['max_marks'],
                        'pass_marks' => $input['pass_marks'],
                        'obtained_marks' => $input['obtained_marks'][$key],
                        'fa_marks' => $input['faobtained_marks'][$key],
                        'sa_marks' => $input['saobtained_marks'][$key],
                        'result' => $input['result'][$key],
                        'grade' => $input['grade'][$key],
                        'fa_grade' => $input['fagrade'][$key],
                        'sa_grade' => $input['sagrade'][$key],
                        'category' => 'FA + SA Marks',
                        'result_by' => 'School'
                    ]);
            }

        if($addResult)
        {
            $input['success'] = 'FA+SA Marks Added To the Students Successfully !!!';
        }
        else
        {
            $input['error'] = 'Error in adding Marks To Students !!!';
        }

        return \Redirect::back()->withInput($input);
    }

    /** @ Get Grade & Result Based on Exam Type & Obtained Marks For Students @ **/
    public function getGradeSystem()
    {
        //return 'hhgg';
        $exam_id = \Request::get('srcexamtype');
        $obtained_marks = \Request::get('obtained_marks');
        /*$getResult = \DB::table('grade_system')
            ->where('exam_type_id',$exam_id)
            //->whereBetween($obtained_marks, ['from_marks', 'to_marks'])
            ->whereRaw('? BETWEEN from_marks AND to_marks', [$obtained_marks])
            ->get();
        if(empty($getResult))
        {
            return 'Result not Available';
        }
        else
        {
            return $getResult;
        }*/
        /* updated 22-11-2017 by priya */
        if(is_numeric($obtained_marks))
        {
            $getResult = \DB::table('grade_system')
                ->where('exam_type_id',$exam_id)
                //->whereBetween($obtained_marks, ['from_marks', 'to_marks'])
                ->whereRaw('? BETWEEN from_marks AND to_marks', [$obtained_marks])
                ->get();
            if(empty($getResult))
            {
                return 'Result not Available';
            }
            else
            {
                return $getResult;
            }
        }
        else
        {
            return 'No Grade';
        }
        /* End */
    }
    public function getfaGradeSystem()
    {
        //return 'hhgg';
        $exam_id = \Request::get('srcexamtype');
       
        $faobtained_marks = \Request::get('faobtained_marks');
        
        //return $faobtained_marks;
        if(is_numeric($faobtained_marks))
        {
            $getFAGrade = \DB::table('grade_system')
                ->where('exam_type_id',$exam_id)
                //->whereBetween($obtained_marks, ['from_marks', 'to_marks'])
                ->whereRaw('? BETWEEN frfamark AND tofamark', [$faobtained_marks])
                ->get();
            if(empty($getFAGrade))
            {
                return 'Result not Available';
            }
            else
            {
                return $getFAGrade;
            }
        }
        else
        {
            return 'No Grade';
        }

       
    }
    public function getsaGradeSystem()
    {
        //return 'hhgg';
        $exam_id = \Request::get('srcexamtype');
       
        $saobtained_marks = \Request::get('saobtained_marks');
        
        
        if(is_numeric($saobtained_marks))
        {
            $getResult = \DB::table('grade_system')
                ->where('exam_type_id',$exam_id)
                //->whereBetween($obtained_marks, ['from_marks', 'to_marks'])
                ->whereRaw('? BETWEEN frsamark AND tosamark', [$saobtained_marks])
                ->get();
            if(empty($getResult))
            {
                return 'Result not Available';
            }
            else
            {
                return $getResult;
            }
        }
        else
        {
            return 'No Grade';
        }

       
    }
    public function constructindex()
    {
       return view('users.construction.constructindex');
    }

    public function addBuilding()
    {
        $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
        //dd($schoolname);
       return view('users.construction.addBuilding', compact('schoolname'));
    }
    public function addBuildingDetails()
    {
       $input = \Request::all();
        $date = date('d-m-Y', strtotime($input['date']));
        
       if($input)
        {
            if(isset($input['image']))
            {
                $image = $input['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName= $image->getClientOriginalName();
                $directory = 'construction';
                $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
                $image= \Image::make($image);
                $image->resize(500,null, function ($constraint)
                {
                    $constraint->aspectRatio();
                })->save($directory. '/' . $filename);
                $imagefile = $directory.'/'.$filename;
            }
            else
            {
                $imagefile = '';
            }

            if(isset($input['pdf']))
            {
                $pdf = $input['pdf'];
                $ex = $pdf->getClientOriginalExtension();
                $name = $pdf->getClientOriginalName();
                $destinationPath = 'construction';
                $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                $upload_pdf = $pdf->move($destinationPath, $pdfname);
                $pdffile = $destinationPath.'/'.$pdfname;
            }
            else
            {
                $pdffile = '';
            }
            
            \DB::table('add_building')->insert([
                    'name' => $input['build_name'],
                    'area' => $input['build_area'],
                    'location' => $input['build_loc'],
                    'description' => $input['description'],
                    'date' => $input['date'],
                    'image'=> $imagefile,
                    'pdf' => $pdffile,
                    'school_id' => $this->user->school_id
                ]);
            $input['success'] = 'Building has been saved successfully';
            return \Redirect::back()->withInput($input);
        }
    }
    public function getBuilding()
    {
        $build_details=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();

        foreach ($build_details as $key => $value) {
           $build_name[]=$value->name;
           $area[]=$value->area;
           $location[]=$value->location;
           $description[]=$value->description;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $build_id[]=$value->id;
        }
       
       return view('users.construction.view.viewBuilding', compact('build_name','area','location','description','imagefile','pdffile','dates','build_id'));
    }
    public function downloadBuilding($id)
    {
         
        $build_details=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->where('id', $id)->first();
        if($build_details->image)
        {
             $path = public_path().'/'. $build_details->image;
        }else {
           
      $build_details=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();

        foreach ($build_details as $key => $value) {
           $build_name[]=$value->name;
           $area[]=$value->area;
           $location[]=$value->location;
           $description[]=$value->description;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $build_id[]=$value->id;
        }
        $msg='Requested File Does not Exiest in our Database!';
       return view('users.construction.view.viewBuilding', compact('msg','build_name','area','location','description','imagefile','pdffile','dates','build_id'));
        }
         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        //dd($build_details->image);
    }

    public function pdfdownloadBuilding($id)
    {
         
        $build_details=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->where('id', $id)->first();
        if($build_details->pdf)
        {
             $path = public_path().'/'. $build_details->pdf;
        }else {
           
      $build_details=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();

        foreach ($build_details as $key => $value) {
           $build_name[]=$value->name;
           $area[]=$value->area;
           $location[]=$value->location;
           $description[]=$value->description;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $build_id[]=$value->id;
        }
        $msg='Requested File Does not Exiest in our Database!';
       return view('users.construction.view.viewBuilding', compact('msg','build_name','area','location','description','imagefile','pdffile','dates','build_id'));
        }
         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        //dd($build_details->image);
    }

    public function deleteBuilding()
    {
         $input = \Request::all();
        $build_details=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->where('id', $input['buildId'])->delete();
        
        $msg['success'] = 'Success to delete TimeTable';
        return \Redirect::back()->withInput($msg);
    }
    public function addBuildingwork()
    {  
         $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
        return view('users.construction.addBuildingwork', compact('schoolname','build_name'));
    }
    public function addBuildingworkDetails()
    {
       $input = \Request::all();
       $work_types=$input['work_type'];
       $contractor_names=$input['contractor_name'];
       $phone_nos=$input['phone_no'];
       $addresses=$input['address'];
       $ph_no_exist =\DB::table('add_buildwork')
       ->where('school_id', \Auth::user()->school_id)
       ->where('build_id', $input['build_name'])
       ->where('phoneno', $input['phone_no'])
       ->first();

        if ($ph_no_exist) {
            $input['error'] = 'Contractor Phone No Already Exists';
            return \Redirect::back()->withInput($input);
        }
       if($input['build_name'])
       {
        foreach ($work_types as $key => $value) {
                 DB::table('add_buildwork')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'work_type'=> $work_types[$key],
               'contractor_name'=> $contractor_names[$key],
               'phoneno'=> $phone_nos[$key],
               'address'=> $addresses[$key],
               'build_id'=> $input['build_name']
                )
                 );
       }
    }
     $input['success'] = 'Building Work has been saved successfully';
            return \Redirect::back()->withInput($input);
}

public function getBuildingworktype()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
       return view('users.construction.view.viewBuildingworks', compact('build_name'));
    }
    
    public function viewBuildingworktype()
    {
        $input = \Request::all();
        $work_details=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $input['build_nameid'])->get();

        foreach ($work_details as $key => $value) {
           $work_type[]=$value->work_type;
           $contractor_name[]=$value->contractor_name;
           $phoneno[]=$value->phoneno;
           $address[]=$value->address;
           $buildwork_id[]=$value->id;
          
        }
         $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->where('id', $input['build_nameid'])->first();
         $building_name=$build_name->name;
       return view('users.construction.view.view_buildworkdetails', compact('building_name','work_type','contractor_name','phoneno','address','build_name','buildwork_id'));
    }

    public function deleteBuildingworktype()
    {
         $input = \Request::all();
        $build_details=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('id', $input['buildworkId'])->delete();
        $msg = 'Success to delete Building Work';
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
       return view('users.construction.view.viewBuildingworks', compact('msg','build_name'));
    }
    public function addworkContractor()
    {
         $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
       return view('users.construction.addworkContractordetails', compact('schoolname','build_name'));
    }
    public function addworkContractordetails()
    {
        $build_id = \Request::get('build_nameid');
    $build_detail=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->get();
     
       return view('users.construction.addworkContractor', compact('build_detail'));
    }
    public function addBuildinglabourDetails()
    {
       
       $input = \Request::all();
       $work_nameid=$input['work_nameid'];
       $contractor_nameid=$input['contractor_name'];
       $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
       $build_detail=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('id', $work_nameid )->first();

       $school_id=$schoolname->id;
       $build_id=$build_detail->build_id;
       $contractor_name=$build_detail->contractor_name;
       $labour_names=$input['labour_name'];
       $phone_nos=$input['phone_no'];
       $addresses=$input['address'];
       $workerscategory=$input['workerscategory'];
       $ph_no_exist =\DB::table('add_contractor')
       ->where('school_id', \Auth::user()->school_id)
       ->where('build_id', $build_id)
       ->where('phone_no', $input['phone_no'])
       ->first();
       //dd($ph_no_exist);
        if ($ph_no_exist) {
            $msg1 = 'Labour Phone No Already Exists';
            $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
        return view('users.construction.addworkContractordetails', compact('msg1','schoolname','build_name'));
        }
       if($work_nameid==$contractor_nameid)
       {
        foreach ($labour_names as $key => $value) {
                 DB::table('add_contractor')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'build_id'=> $build_id,
               'work_id'=> $work_nameid,
               'user_type'=> $workerscategory[$key],
               'contractor_name'=> $contractor_name,
               'labour_name'=> $labour_names[$key],
               'phone_no'=> $phone_nos[$key],
               'address'=> $addresses[$key],
              
                )
                 );
       }
    }
if($work_nameid==$contractor_nameid)
       {
        $msg = 'Labour Details saved successfully';
    }else{
        $msg='Work Category is not matching Contractor name';
}
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
return view('users.construction.addworkContractordetails', compact('msg','schoolname','build_name'));
}
public function getLabour()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
       return view('users.construction.view.viewLabourtwo', compact('build_name'));
    }
    public function get_by_buildid()
    {
   $input = \Request::all();
    if ($input['build_id']) {
         $contractordet = \DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $input['build_id'])->get();
        
    }
    return view('users.construction.view.viewLabourthree', compact('contractordet'));
}
public function get_by_contractor_id()
{
   $input = \Request::all();
   //dd($input);
    if ($input['contractor_name']) {
       
         $labourdet = \DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('work_id', $input['contractor_name'])->get();
        
    }
    foreach ($labourdet as $key => $value) {
        $build_id=$value->build_id;
        $work_id=$value->work_id;
        $contractor_name=$value->contractor_name;
        $labour_name[]=$value->labour_name ;
        $phone_no[]=$value->phone_no ;
        $user_type[]=$value->user_type ;
        $address[]=$value->address ;
        $labour_id[]=$value->id;
    }
    //dd($labourdet);
    $contractordet = \DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('id', $work_id)->first();
    $work_type=$contractordet->work_type;
    $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->where('id', $build_id)->first();
    $build_name=$build_name->name;
   
    //dd('hi',$work_type,$contractor_name,$labour_name,$phone_no,$user_type,$address,$build_name);
    return view('users.construction.view.view_labourdetails', compact('labour_id','work_type','contractor_name','labour_name','phone_no','user_type','address','build_name'));
}
public function deletelabourname()
    {
         $input = \Request::all();
        $build_details=\DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('id', $input['labour_id'])->delete();
        
        $msg['success'] = 'Successfully  deleted Labour name';
        return \Redirect::back()->withInput($msg);
        
    }
    public function deletelabourpayment()
    {
         $input = \Request::all();
         //dd($input);
        $build_details=\DB::table('contractor_payment')->where('school_id', \Auth::user()->school_id)->where('id', $input['ids'])->delete();
        
        $msg['success'] = 'Successfully  deleted Payment!!!';
        return \Redirect::back()->withInput($msg);
        
    }
    public function addlabourPayment()
    {
       $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
       return view('users.construction.addContractorPayment', compact('schoolname','build_name'));
    }
    public function step2labourPayment()
    {
        $build_id = \Request::get('build_nameid');
    $build_detail=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->get();
       return view('users.construction.addstep2Contractorpayment', compact('build_detail'));
    }
    public function postLabourpayment()
    {
       $input = \Request::all();
       $work_nameid=$input['work_nameid'];
       $contractor_nameid=$input['contractor_name'];
        $labour_details=\DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('work_id', $work_nameid )->get();

        foreach ($labour_details as $key => $value) {
           //dd($key,$value->id);
            $labour_name[]=$value->labour_name;
            $labour_id[]=$value->id;
            $contractor_name=$value->contractor_name;
            $workers_category[]=$value->user_type;
        }
     
      return view('users.construction.addContractorPaymentstructure', compact('workers_category','labour_name','contractor_name','labour_id','work_nameid'));
}
public function selectelabourPayment()
    {
       $input = \Request::all();
       $labourid=$input['labour_id'];
       $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
       $labour_details=\DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('work_id', $input['work_nameid'] )->get();
       //dd($input,$labour_details);
       foreach ($labour_details as $key => $value) {
           $build_id=$value->build_id;
           $work_id=$value->work_id;
           $contractor_id=$value->work_id;
           $phone_no[]=$value->phone_no;
           $user_type[]=$value->user_type;
           $labour_name[]=$value->labour_name;
           
       }
       if($input['labour_id']){
        foreach ($input['labour_id'] as $key => $value) {
           $labour_id[]=$value;
       }

       foreach ($labour_id as $key => $value) {
         $labour_phoneno=\DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('id', $value )->get();
       }
       foreach ($labour_phoneno as $key => $value1) {
           $labour_phno[]=$value1->phone_no;
           $contractor_name=$value1->contractor_name;
          
       }
       foreach ($input['checkboxamt'] as $key => $value) {
        //if(!empty($value)){
            $checkbox_amt[]=$value;
       // }

        }
        foreach ($checkbox_amt as $key => $value) {
                 DB::table('contractor_payment')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'build_id'=> $build_id,
               'work_typeid'=> $work_id,
               'contractor_id'=> $contractor_id,
               'labour_id'=> $labour_id[$key],
               'date'=> $input['date'],
               'amount'=> $value,
               'phone_no'=>$phone_no[$key],
               'labour_name'=>$labour_name[$key],
               'contractor_name'=>$contractor_name,
               'user_type'=>$user_type[$key]
              
                )
                 );
       }

       }
       if($input['con_checkboxamt']){
        $contractor_details=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('id', $input['work_nameid'] )->first();
       
        DB::table('contractor_payment')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'build_id'=> $build_id,
               'work_typeid'=> $work_id,
               'contractor_id'=> $contractor_id,
               //'labour_id'=> $labourid,
               'date'=> $input['date'],
               //'amount'=> $amt,
               'phone_no'=>$contractor_details->phoneno,
               'contractor_name'=>$input['contractor_name'],
               'contractor_amt'=>$input['con_checkboxamt'],
               //'user_type'=>$user_type
              
                )
                 );
       }
        $msg='Payment is Saved Successfully';
    $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
       return view('users.construction.addContractorPayment', compact('schoolname','build_name','msg'));
}
public function getdailywages()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.construction.view.viewLabourpayment', compact('build_name'));
    }

    public function getdailywagesdetails()
    {
        $input = \Request::all();
        $data =\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $input['build_id'])->get();
        
       return view('users.construction.view.viewLabourpaymentdetails', compact('data'));
    }
    public function viewPaymentcontractorwise()
    {
        $input = \Request::all();

        $data =\DB::table('contractor_payment')->where('school_id', \Auth::user()->school_id)->where('contractor_id', $input['contractor_id'])->where('work_typeid', $input['work_id'])->get();
        //dd($input,$input['contractor_id'],$input['work_id'],$data);
        foreach ($data as $key => $value) {
            $labour_id[]=$value->labour_id;
            $contractor_id[]=$value->contractor_id;
           $payment_date[]=$value->date;
           $worktype_id[]=$value->work_typeid;
           $labour_amt[]=$value->amount;
           $contractor_amt[]=$value->contractor_amt;
           $ids[]=$value->id;
           $user_type[]=$value->user_type;
           $labour_name[]=$value->labour_name;
           $contractor_name=$value->contractor_name;
           $phone_no[]=$value->phone_no;
        }
        
        
        //dd($data,$payment_date,$worktype_id,$labour_amt,$contractor_amt,$labour_id,$contractor_id,$labour_details,$labour_name,$contractor_name,$user_type);
       return view('users.construction.view.listLabourpaymentdetails', compact('phone_no','payment_date','labour_amt','contractor_amt','labour_name','contractor_name',
        'user_type','labour_id','contractor_id','ids'));
       
    }
    public function constructionSalary()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
         $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
         //dd($build_name);
       return view('users.construction.addContractorSalary', compact('schoolname','build_name'));
    }
    public function selectlabour_paymentsearch()
    {
        $input = \Request::all();
        $build_id=$input['build_id'];
        $phone_no=$input['mobile_no'];

        $getPayment=\DB::table('contractor_payment')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->where('phone_no', $phone_no )->get();
       

        foreach ($getPayment as $key => $value) {

            $workDate[]=$value->date;
            $labourAmount[]=$value->amount;
            $all_payment_id[]=$value->id;
            $worktype_id=$value->work_typeid;
            $total_labouramt+=$value->amount;
           
        }
        //dd($all_payment_id);
        if(!empty($all_payment_id)){
            foreach ($all_payment_id as $key => $value) {
                //dd($value,$build_id);
                $all_paidamt[]=\DB::table('contractor_paidAmt')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->where('fee_id', $value )->get();
              // $all_paidamt1[] = \DB::table('contractor_paidamt')->where('school_id', \Auth::user()->school_id)->get();
            }
           // dd($all_paidamt,$all_payment_id);
            $allpaid_ids=array();
            $total_paidAmt=0;

            foreach($all_paidamt as $firstlevelids){
                        foreach($firstlevelids as $paidids) {
                            $allpaid_feeName[]=$paidids->fee_name;
                            //$allpaid_date[]=$paidids->date;
                            $allpaid_ids[]=$paidids->fee_id;
                            $allpaid_amt[]=$paidids->amount;
                            $allpaid_date[]=$paidids->date;
                            $allpaid_recvdby[]=$paidids->recived_by;
                            $allpaid_paymentmode[]=$paidids->payment_mode;
                            $allpaid_cheqNo[]=$paidids->cheque_no;
                            $allpaid_cheqDate[]=$paidids->cheque_date;
                            $allpaid_bankname[]=$paidids->bank_name;
                            $allpaid_onlineTfno[]=$paidids->transaction_no;
                            $allpaid_onlinebkName[]=$paidids->online_bankname;
                            $total_paidAmt+=$paidids->amount;
                        }
                    }

                    $allunpaid_ids= array_diff($all_payment_id, $allpaid_ids);
                    //dd($allunpaid_ids);
                     if(!empty($allunpaid_ids))
                    {
                        $all_unpaidamt=array();
                    foreach ($allunpaid_ids as $key => $value) {
                            $all_unpaidamt[] = DB::table('contractor_payment')->where('school_id', \Auth::user()->school_id)->where('build_id',$build_id)->where('id',$value)->get();
                        }
                        }
                    if(!empty($all_unpaidamt))
                {
                    foreach ($all_unpaidamt as $key ) {
                        foreach ($key as $amt) {
                            $unpaid_feename[]=$amt->fees_name;
                            $unpaid_amt[]=$amt->amount;
                            $unpaid_totamt+=$amt->amount;
                            $unpaid_date[]=$amt->date;
                            $unpaid_ids[]=$amt->id;
                        }
                    }
                }
                $tot_bal_amt=$total_labouramt- $total_paidAmt;
                    
        }
        $getWorktype=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->where('id', $worktype_id )->get();
        foreach ($getWorktype as $key => $value) {
           $worktype=$value->work_type;
           $contractorName=$value->contractor_name;
        }
        $getBuild_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->where('id', $build_id )->first();

        $getlabour_name=\DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->where('phone_no', $phone_no )->first();
       
       return view('users.construction.paysingle_labour_payment', compact('allpaid_ids','allpaid_feeName','allpaid_amt','allpaid_date',
    'allpaid_paymentmode','allpaid_cheqNo','allpaid_cheqDate','allpaid_bankname','allpaid_onlineTfno','allpaid_onlinebkName','total_paidAmt',
    'unpaid_feename','unpaid_amt','unpaid_totamt','unpaid_date','unpaid_ids'));
    }
    public function selectlabour_paymentlist()
    {
        $input = \Request::all();
        $payment_ids=$input['unpaid_ids'];
        //dd('hi',$input,$payment_ids);
        $getPayment=array();
       if(!empty($payment_ids)){
        foreach ($payment_ids as $payment_id) {
           $getPayment[]=\DB::table('contractor_payment')->where('school_id', \Auth::user()->school_id)->where('id', $payment_id )->get();
        }
       }

       $payment_details = array();
       foreach($getPayment as $merfee) 
            {
            $payment_details = array_merge($payment_details, $merfee);
            }

            foreach($payment_details as $amt)
                {  

                $payment_date[]=$amt->date;
                $labour_amt[]=$amt->amount;
                $paid_lab_totalAmt+=$amt->amount;
                $work_typeid=$amt->work_typeid;
                
                }
               // dd($payment_details,$labour_amt,$paid_lab_totalAmt);
         $getWorktype=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('id', $work_typeid )->get();
        foreach ($getWorktype as $key => $value) {
           $worktype=$value->work_type;
           $contractorName=$value->contractor_name;
        }
        return view('users.construction.paylabour_selected_amount', compact('payment_date','labour_amt','paid_lab_totalAmt','payment_ids','worktype','contractorName'));
    
    }
    public function selectlabour_paymentreceipt()
    {
        //dd('hi');
        $input = \Request::all();
        $idNos= $input['idNos'];
        $payment_date= $input['payment_date'];
        $worktype= $input['worktype'];
        $labour_amt= $input['labour_amt'];
        $paidAmt= $input['paidAmt'];
        $total= $input['total'];
        $pmMode= $input['pmMode'];
        $cheqno= $input['cheqno'];
        $cheqdate= $input['cheqdate'];
        $bank_name= $input['bank_name'];
        $trans_no= $input['trans_no'];
        $bank_name1= $input['bank_name1'];
        $revdby= $input['revdby'];
        $paydate= $input['paydate'];
        
        //dd('hi',$worktype,$labour_amt,$paidAmt,$pmMode,$cheqno,$cheqdate,$bank_name,$trans_no,$bank_name1,$revdby,$paydate);
        //dd('route',$route,'bus_no',$bus_no);
         $pay_details=array();
           //$termsTypes = array();
         $worktype = array();
           $labour_amt = array();
// To find Fee Structure Amount

    foreach($idNos as $ids)
    {
   $pay_details[] = DB::table('contractor_payment')->where('school_id', \Auth::user()->school_id)->where('id',$ids)->get();
    }
    //dd($pay_details);
     $payment_details = array();
    foreach($pay_details as $merfee)
     {
        $payment_details = array_merge($payment_details, $merfee);
    }
 
    foreach($payment_details as $collection)
    {
    $LabourStr_Amt[]=$collection->amount;
    $work_typeid=$collection->work_typeid ;
    $build_id=$collection->build_id ;
    $contractor_id=$collection->contractor_id;
    $date=$collection->date ;
    $contractor_name=$collection->contractor_name;
    $phone_no=$collection->phone_no;
    $user_type=$collection->user_type;
    $labour_amt=$collection->amount;
    $contractor_amt=$collection->contractor_amt;
    $labour_id=$collection->labour_id;
    $labour_name =$collection->labour_name;
    }

    $Lab_structure_Amount=array_sum($LabourStr_Amt);
   
    $session=$this->active_session->id;
    if(empty($contractor_amt))
    {
        $contractor_amt=0;
    }
    //dd($contractor_amt,$labour_amt);
// To find Paid Amount 
    $paidAmount=$paidAmt;
// To find payable-paid = balance ( last element of array)
 if($paidAmount == $Lab_structure_Amount)
    {
        $payfullAmt=$LabourStr_Amt;
        //dd('full',$payfullAmt);
    }
    else
    {
        $balance=$Lab_structure_Amount - $paidAmount;
        }

   // }

//To  insert balance amount
         foreach ($idNos as $key => $value) {
               
               $checkFeeid = DB::table('contractor_paidAmt')->where('school_id', \Auth::user()->school_id)
               ->where('fee_id',$value)->get();
            }
            //dd('hi',$balance,count(array_filter($checkFeeid)));
 if(count(array_filter($checkFeeid)) == 0)
        {
     if($balance != null)
    {
        $status=1;
        
             DB::table('contractor_payment')->insert(
                array(
                'school_id' => Auth::user()->school_id,
                'work_typeid' => $work_typeid,
                'build_id' => $build_id,
                'contractor_id'=>$contractor_id,
                'labour_id' =>$labour_id,
                'date' =>$date,
                'contractor_amt' =>$contractor_amt,
                'amount'=>$balance,
                'bal_status'=>$status,
                'phone_no'=>$phone_no,
                'contractor_name'=>$contractor_name,
                'user_type'=>$user_type,
                'labour_name'=>$labour_name,
                ));
                 }
                }else{
            dd("This Fees was already paid. So You can't again pay");
                }
    // To continu work
if($labour_id){
    $allpaidamt = DB::table('contractor_paidAmt')->where('school_id', \Auth::user()->school_id)->where('labour_id',$labour_id)->get();
}else{
    $allpaidamt = DB::table('contractor_paidAmt')->where('school_id', \Auth::user()->school_id)->where('contractor_id',$contractor_id)->get();
}
 
    foreach($allpaidamt as $key =>$value)
    {
        $checkfeeId=$value->fee_id;
    }
    
    
if($checkfeeId !='0' ) 
    {
        $bal_amt=0;
    }

            $paid = ($bal_amt == 0 ? true : false );
//dd($invoice);
             if(count(array_filter($checkFeeid)) == 0)
            {
    foreach($idNos as $key =>$value)
            {
        $result=DB::table('contractor_paidAmt')->insert(
            array(
            'school_id' => Auth::user()->school_id,
            'paid' => $paid,
            'fee_id' => $idNos[$key],
            'work_type' => $work_typeid,
            'build_id' => $build_id,
            'contractor_id' => $contractor_id,
            'labour_id' => $labour_id,
            'amount' => $LabourStr_Amt[$key],
            //'balance_amount'=>$termsNo[$key],
            'phone_no'=>$phone_no,
            'usertype' => $user_type,
            //'fee_name' => $revdby,
            'date' =>$date,
            'recived_by' =>$input['revdby'],
           //'paid_by' => $transno,
           'cheque_no'=> $cheqno,
           'cheque_date'=> $cheqdate,
           'bank_name' => $bank_name,
           'online_bankname'=>$bank_name1,
           'transaction_no'=>$trans_no,
           'payment_mode' =>$pmMode,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
                    ));
        }
}else{
        dd('This Fee was already paid. So you can not pay again');
    }
    $payableAmt=array_sum($LabourStr_Amt);
    $school = school::where('id', Auth::user()->school_id)->first();
            //dd('hi');
    return view('users.construction.payment_recipt', compact('date','user_type','school','LabourStr_Amt','contractor_amt','idNos','payableAmt','paidAmount','balance','pmMode'));
    
    }

     public function addSupplier()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
       return view('users.construction.supplier.addMatterialSupplier', compact('build_name'));
    }
    public function postSupplier()
    {
        $input = \Request::all();
        
        $build_id=$input['build_nameid'];
       $suppliername=$input['suppliername'];
       $phone_no=$input['phone_no'];
       $address=$input['address'];
        \DB::table('material_supplier')->insert([
                    'build_id' => $build_id,
                    'ventor_name' => $input['suppliername'],
                    'phone_no' => $input['phone_no'],
                    'address' => $input['address'],
                    'school_id' => $this->user->school_id
                ]);
        //dd('hi');
            $input['success'] = 'Supplier details saved successfully';
            return \Redirect::back()->withInput($input);
    }
    public function addPurchase()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
        $supplier_name=\DB::table('material_supplier')->where('school_id', \Auth::user()->school_id)->get();
       return view('users.construction.supplier.materialPurchase', compact('build_name','supplier_name'));
    }
    public function postPurchase()
    {
         $input = \Request::all();

         $recpt_no=$input['recpt_no'];
          $build_id=$input['build_id'];
           $supplier_id=$input['supplier_id'];
            $pdate=$input['pdate'];
             $product_name=$input['product_name'];
              $company_name=$input['company_name'];
               $Quantity=$input['Quantity'];
                $price=$input['price'];

         if($input['product_name'])
       {
        foreach ($price as $key => $value) {
                 DB::table('add_product')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'build_id'=> $build_id,
               'receipt_no'=> $recpt_no,
               'product_name'=> $product_name[$key],
               'supplier_name'=> $supplier_id,
               'product_company'=> $company_name[$key],
                'price'=> $value, 
                 'quantity'=> $Quantity[$key],
                 'pur_date'=> $pdate
                )
                 );
       }
    }
     $input['success'] = 'Purchase Bill saved successfully';
            return \Redirect::back()->withInput($input);
    }
    public function addIssuematerial()
    {
        $build_name=\DB::table('add_building')->where('school_id', \Auth::user()->school_id)->get();
       return view('users.construction.supplier.addMatterialIssue', compact('build_name'));
        
    }
    public function addIssuematerialdetails()
    {
         $build_id = \Request::get('build_id');
          //dd('hi',$build_id);
           $build_detail=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->get();
           $product_detail=\DB::table('add_product')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->get();

       return view('users.construction.supplier.addMatterialIssueone', compact('build_detail','product_detail'));
        
    }
    public function postIssuematerialdetails()
    {
        $input = \Request::all();
        $worktype_id=$input['worktype_id'];
        $contractor_id=$input['contractor_id'];
        $idate=$input['idate'];
        $product_id=$input['product_id'];
        $company_id=$input['company_id'];
        $Quantity=$input['Quantity'];
        
        $build_det=\DB::table('add_contractor')->where('school_id', \Auth::user()->school_id)->where('work_id', $worktype_id )->first();

        $build_id=$build_det->build_id;

        if($input['product_id'])
       {
        foreach ($product_id as $key => $value) {
                 DB::table('issue_product')->insert(
                array(
                'school_id' => Auth::user()->school_id,
               'build_id'=> $build_id,
               'work_typeid'=> $worktype_id,
               'product_id'=> $product_id[$key],
               'contractor_id'=> $contractor_id,
               'product_companyid'=> $company_id[$key],
                'issue_date'=> $idate, 
                 'issue_qty'=> $Quantity[$key],
                 
                )
                 );
       }
    }
            $build_detail=\DB::table('add_buildwork')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->get();
           $product_detail=\DB::table('add_product')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->get();
    
     $msg = 'Issue details saved successfully';
           return view('users.construction.supplier.addMatterialIssueone', compact('msg','build_detail','product_detail'));

    }
    public function schoolfeeindex()
    {
       return view('users.fee_structure.sion.schoolfeeindex');
    }

    public function homevisitindex()
    {
        //dd('hi');
       return view('users.homevisit.homevisitindex');
    }


    public function homevisitcreate()
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
           //dd($classes,$value->id,$sessionid,$sessionname);
        return view('users.homevisit.createhomevisit', compact('classes', 'session','sessionid','sessionname'));
    }
    public function homevisitreport()
    {
       $classes = addClass::where('school_id', Auth::user()->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->get();
        return view('users.homevisit.reportindex', compact('classes'));
    }

    public function homevisitreportdetails()
    {
       $input = \Request::all();
//dd($input);
       
       if(empty($input)){
        $getdailyvisit = \DB::table('homevisitchcklist')->where('school_id', \Auth::user()->school_id)
                        ->where('session','=',$this->active_session->id)//20-4-2018
                       // ->where('class_id',$input['class'])
                       // ->where('section_id',$input['section'])
                       // ->where('teacher_name',$student->id)
                        ->groupBy('teacher_name')
                        //->where('student_id',$student->id)
                        ->get();
               // dd($getdailyvisit);
            foreach ($getdailyvisit as $key => $value) {
               $homevisited_teacher[]=$value->teacher_name;
            }


            foreach ($homevisited_teacher as $key => $teacher_id) {

                $getschool = \DB::table('users')->where('users.school_id', \Auth::user()->school_id)
                        //->where('users.session_id','=',$this->active_session->id)//20-4-2018
                        ->join('school', 'users.school_id', '=', 'school.id')
                        //->join('teacher', 'users.id', '=', 'teacher.user_id')
                        ->where('users.username',$teacher_id)
                        ->get();
               //$visitedStuID[]=$value->student_id;
               $getStudent[] = \DB::table('homevisitchcklist')->where('homevisitchcklist.school_id', $this->user->school_id)
                ->where('homevisitchcklist.session',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'homevisitchcklist.class_id', '=', 'class.id')
                ->join('section', 'homevisitchcklist.section_id', '=', 'section.id')
                ->join('student', 'homevisitchcklist.student_id', '=', 'student.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->where('teacher_name', $teacher_id)
                //->where('student.section_id', $input['section'])
               // ->where('student.id', $value)
                ->select('student.id', 'student.name' ,'section.section','parent.mobile','parent.name as parent_name','parent.mother','parent.address','homevisitchcklist.c3','homevisitchcklist.date','homevisitchcklist.remarks','homevisitchcklist.album','homevisitchcklist.stdadm','homevisitchcklist.lkgadm','homevisitchcklist.onlinetest','homevisitchcklist.fees','homevisitchcklist.whatsapp_no','class.class','section.section')->get();
               
                $getteachers = \DB::table('users')->where('users.school_id', \Auth::user()->school_id)
                        //->where('users.session_id','=',$this->active_session->id)//20-4-2018
                        ->join('teacher', 'users.id', '=', 'teacher.user_id')
                        //->join('teacher', 'users.id', '=', 'teacher.user_id')
                        ->where('users.username',$teacher_id)
                        ->get();
                      if($getschool) {
                        $getteachers = array_merge($getschool, $getteachers);
                    } else{
                        $getteachers = $getteachers;
                    }
           
            }
            foreach ($getteachers as $key => $value) {

                if($value->school_name){
                    $teachers_name[]= $value->school_name;
                }else{
                    $teachers_name[]= $value->name;
                }
                
            }
           // dd($teachers_name,$getStudent);
            return view('users.homevisit.dailyhomevisitReportdet', compact('getStudent','teachers_name')); 
                       // dd($getStudent);
       }else{

        if ($input) 
        {
            $getvisit = \DB::table('homevisitchcklist')->where('school_id', \Auth::user()->school_id)
                        ->where('session','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$input['class'])
                        ->where('section_id',$input['section'])->groupBy('student_id')
                        //->where('student_id',$student->id)
                        ->get();
            foreach ($getvisit as $key => $value) {
               $homevisitStudentid[]=$value->student_id;
            }
                //dd($homevisitStudentid);
            foreach ($homevisitStudentid as $key => $value) {
               //$visitedStuID[]=$value->student_id;
               $getStudent[] = \DB::table('student')->where('student.school_id', $this->user->school_id)
                ->where('student.session_id',$this->active_session->id)//updated 14-4-2018
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->where('student.class_id', $input['class'])
                ->where('student.section_id', $input['section'])
                ->where('student.id', $value)->select('student.id', 'student.name' ,'student.section_id','parent.mobile','class.class','section.section')->get();
     
            }
           
        }
       
       
            foreach ($getStudent as $student ) {
                foreach ($student as $student1) {
 //dd($student1->id);
                    $getvisits = \DB::table('homevisitchcklist')->where('school_id', \Auth::user()->school_id)
                        ->where('session','=',$this->active_session->id)//20-4-2018
                        ->where('class_id',$input['class'])
                        ->where('section_id',$input['section'])
                        ->where('student_id',$student1->id)->get();
                    foreach ($getvisits as $key => $value) {
                        $en_point[]=$value->enq_points;
                        $en_status[]=$value->en_status;
                        $en_descrip[]=$value->enq_descript;
                        $trou_point[]=$value->trouble_stud;
                        $trou_status[]=$value->troub_status;
                        $trou_descrip[]=$value->stud_remarks;
                        $paren_image=$value->par_image;
                        $parents_point=$value->parents_points;
                        $teacher_name=$value->teacher_name;
                        $date=$value->created_at;
                        $createDate=date('h:i:s a d/m/Y', strtotime($date));

                        $student1->enq_point = $en_point;
                    $student1->enq_staut = $en_status;
                    $student1->enq_descr = $en_descrip;
                    $student1->tr_point = $trou_point;
                    $student1->tr_staut = $trou_status;
                    $student1->tr_descr = $trou_descrip;
                    $student1->par_point = $parents_point;
                    $student1->par_img = $paren_image;
                    $student1->created_at = $createDate;
                    
                   //dd('kk',$student1->name,$student1->id,$getvisit,$student1);
                }

                }
                    }
                $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
            $user_detail = \DB::table('users')
            ->where('school_id', \Auth::user()->school_id)
            ->where('username','=',$teacher_name)
            ->first();
            $teacher_details = \DB::table('teacher')
            ->where('school_id', \Auth::user()->school_id)
            ->where('user_id','=',$user_detail->id)
            ->first();
            //dd($teacher_details);
            $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('id','=',$input['class'])
                    ->first();
            $section = \DB::table('section')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)
                    ->where('id','=',$input['section'])
                    ->first();
             return view('users.homevisit.homevisitReportdet', compact('teacher_details','getStudent','school','classes','section')); 
       }   
    }
    public function addofficedocu()
    {
       return view('users.office_docu.addofficedocu');
    }
    public function postofficedocu()
    {
        $input = \Request::all();
        $date = date('d-m-Y', strtotime($input['create_date']));
        $exp_date = date('d-m-Y', strtotime($input['exp_date']));
        //dd($input,$date,$exp_date);
       if($input)
        {
            if(isset($input['image']))
            {
                $image = $input['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName= $image->getClientOriginalName();
                $directory = 'officdocu';
                $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
                $image= \Image::make($image);
                $image->resize(500,null, function ($constraint)
                {
                    $constraint->aspectRatio();
                })->save($directory. '/' . $filename);
                $imagefile = $directory.'/'.$filename;
            }
            else
            {
                $imagefile = '';
            }

            if(isset($input['pdf']))
            {
                $pdf = $input['pdf'];
                $ex = $pdf->getClientOriginalExtension();
                $name = $pdf->getClientOriginalName();
                $destinationPath = 'officdocu';
                $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                $upload_pdf = $pdf->move($destinationPath, $pdfname);
                $pdffile = $destinationPath.'/'.$pdfname;
            }
            else
            {
                $pdffile = '';
            }
            
            \DB::table('office_document')->insert([
                    'school_name' => $input['school_name'],
                    'category' => $input['catgory'],
                    'name' => $input['docu_name'],
                    'exp_date' => $exp_date,
                    'date' => $date,
                    'image'=> $imagefile,
                    'pdf' => $pdffile,
                    'school_id' => $this->user->school_id
                ]);
            $input['success'] = 'Office Document has been saved successfully';
            return \Redirect::back()->withInput($input);
        }
    }
    
    public function downloadimagedocument($id)
    {
         
        $office_details=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->where('id', $id)->first();
        if($office_details->image)
        {
             $path = public_path().'/'. $office_details->image;
        }else {
           
      $office_details=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->get();

        foreach ($office_details as $key => $value) {
           $docu_name[]=$value->name;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $docu_id[]=$value->id;
        }
        $msg='Requested File Does not Exiest in our Database!';
       return view('users.office_docu.viewofficedocument', compact('msg','docu_name','imagefile','pdffile','dates','docu_id'));
        }
         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        //dd($build_details->image);
    }
    public function pdfdownloadoffice($id)
    {
         
        $office_details=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->where('id', $id)->first();
        if($office_details->pdf)
        {
             $path = public_path().'/'. $office_details->pdf;
        }else {
           
      $office_details=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->get();

        foreach ($office_details as $key => $value) {
           $docu_name[]=$value->name;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $docu_id[]=$value->id;
        }
        $msg='Requested File Does not Exiest in our Database!';
       return view('users.office_docu.viewofficedocument', compact('msg','docu_name','imagefile','pdffile','dates','docu_id'));
        }
         if ( file_exists( $path ) ) {
            // Send Download
            return response()->download($path);
        } 
        //dd($build_details->image);
    }
    public function deleteofficedocument()
    {
         $input = \Request::all();
        $document_id=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->where('id', $input['docu_id'])->delete();
        
        $msg['success'] = 'Successfully  deleted Office Document';
        //return \Redirect::back()->withInput($msg);
        $office_details=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->get();

        foreach ($office_details as $key => $value) {
           $docu_name[]=$value->name;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $docu_id[]=$value->id;
        }
       
       return view('users.office_docu.viewofficedocument', compact('msg','docu_name','imagefile','pdffile','dates','docu_id'));
        
    }
    public function getschoolnameforoffice()
    {

       return view('users.office_docu.getofficedocu');
    }
    public function getofficedocu()
    {
        $input = \Request::all();
        $schoolname=$input['school_name'];
        $category=$input['catgory'];
        $docu_details=\DB::table('office_document')->where('school_id', \Auth::user()->school_id)->where('school_name', $input['school_name'])->where('category', $input['catgory'])->get();
//dd($docu_details);
        foreach ($docu_details as $key => $value) {
            $school_name=$value->school_name;
            $category=$value->category;
           $docu_name[]=$value->name;
           $imagefile[]=$value->image;
           $pdffile[]=$value->pdf;
           $dates[]=$value->date;
           $docu_id[]=$value->id;
           $exp_date[]=$value->exp_date;
        }
       //dd($docu_details);
       return view('users.office_docu.viewofficedocument', compact('exp_date','category','school_name','docu_name','imagefile','pdffile','dates','docu_id'));
    }

    public function getStudentcollectSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }
     public function get_bus_route()
    {
        $input = \Request::all();
        
        $getRoutes = \DB::table('route_details')->where('school_id', \Auth::user()->school_id)->where('bus_id',$input['bus'])->get();
        foreach ($getRoutes as $key => $value) {
           $routename[]=$value->route_name;
           $busid=$value->bus_id;
        }
        $getbus = \DB::table('busdetails')->where('school_id', \Auth::user()->school_id)->where('id',$input['bus'])->first();
        //dd($routename,$busid,$getbus,$getRoutes);
        return view('users.fee_structure.sion.busrouteDetails', compact('routename','busid','getbus'));
    }
    /** @ Get Section For Class @ **/
    public function getStudentidcardSection()
    {
        $classId = \Request::get('srclass');
        //return $classId;exit;
        $getStudent = \DB::table('section')->where('class_id',$classId)->get();
        return $getStudent;
    }
    public function Stu_idcardStudent()
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
     public function singStu_collectStudent()
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
    /** @ Post All students Mark @ **/
    public function postStudentsExamResult()
    {
        //return 'post student result';
        $input = \Request::all();
        $current_month = date('F',strtotime($input['getExamDate']));
        //echo $input['student_id'][0];
        $session = \DB::table('session')->where('school_id', \Auth::user()->school_id)
            ->where('active', '1')
            ->select('id')
            ->first();

        foreach($input['student_id'] as $key => $value)
        {
            /*if($input['obtained_marks'][$key] != '' && $input['result'][$key] != '' && $input['grade'][$key])
                {*/
                    /* updated 22-11-2017 by priya */
                    /*if(!is_numeric($input['obtained_marks'][$key]))
                    {
                        $input['obtained_marks'][$key] = 'AB';
                        $input['result'][$key] = 'AB';
                        $input['grade'][$key] = 'AB';
                    }*/
                if($input['obtained_marks'][$key] == '')
                {
                    $input['error'] = "You Can't leave the Obtained Marks field empty !!!";
                    return \Redirect::back()->withInput($input);
                }
        }
        foreach($input['student_id'] as $key => $value)
            {
                $addResult = \DB::table('result')
                    ->insert([
                        'school_id' => \Auth::user()->school_id,
                        // 'session_id' => $session->id,
                        'class_id' => $input['class'],
                        'section_id' => $input['section'],
                        'exam_type_id' => $input['examtype'],
                        'total_students' => $input['countStudent'],
                        'month' => $current_month,
                        'subject_id' => $input['subject'],
                        'student_id' => $input['student_id'][$key],
                        'teacher_id' => $input['teacher'],
                        'date' => $input['getExamDate'],
                        'max_marks' => $input['max_marks'],
                        'pass_marks' => $input['pass_marks'],
                        'obtained_marks' => $input['obtained_marks'][$key],
                        'result' => $input['result'][$key],
                        'grade' => $input['grade'][$key],
                        'result_by' => 'School'
                    ]);
            }

        if($addResult)
        {
            $input['success'] = 'Marks Added To the Students Successfully !!!';
        }
        else
        {
            $input['error'] = 'Error in adding Marks To Students !!!';
        }

        return \Redirect::back()->withInput($input);
      
    }
   


}
