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
use App\FeeFrequency;
use App\FeeStructure;
use App\FeeSummary;
use App\Feedback;
use App\Gallery;
use App\Grade;
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
use App\Salary;
use App\Subject;
use App\TimeTable;
use App\User;

class MasterController extends Controller
{
    protected $user;
/** update 14-4-2018 **/
    private $active_session;

    function __construct()
    {
        /** @ Update 14-4-2018 @ **/
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
        if(\Auth::check())
        {
            $this->user = \Auth::user();

            $school_image = School::where('id', \Auth::user()->school_id)->first();
            //dd($school_image);
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

            //view()->share(compact('school_image', 'roler'));
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
                // dd($userplansadded);
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

    public function masterView()
    {
        return view('users.master.masterMain');
    }

    public function changePass()
    {
        $id = \Auth::user()->id;
        return view('users.master.change_password', compact('id'));
    }

    public function postPass($id)
    {
        $input = \Request::get('password');

        \DB::table('users')->where('id', $id)->update([
            'password'=>\Hash::make($input),
            'hint_password'=>$input
        ]);
        $msg['success'] = 'Success to Change Password';
        return \Redirect::route('changePass')->withInput($msg);
    }

    // Session 
    public function masterSession()
    {
        $sessions = Session::where('school_id', $this->user->school_id)->get();
        return view('users.master.session.session', compact('sessions'));
    }

    public function postSession(Request $request, Session $session)
    {
        $usererror = ['session' => 'Session','fromDate' => 'fromDate','toDate'=>'toDate'];
        $validator = \Validator::make($request->all(), [
            'session' => 'required',
            'fromDate'=>'required',
            'toDate'=>'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $session->doPostSession($request, $this->user);
    }

    public function getSessions(Session $session)
    {
        return $session->doGetSessions($this->user);
    }

    public function deleteSession(Session $session, $id)
    {
        return $session->doDeleteSession($id);
    }

    public function editSession(Session $session, $id)
    {
        return $session->doEditSession($id);
    }

    public function updateSession(Request $request, Session $session)
    {
        $usererror = ['id' => 'Session Id', 'session' => 'Session'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'session' => 'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $session->doUpdateSession($request, $this->user);
    }

    /*public function operateSession($id)
    {
        $session = Session::where('id', $id)->first();
        if($session->active == 0)
        {
            Session::where('school_id',$this->user->school_id)->where('active',1)->update(['active' => 0]);
            Session::where('id', $id)->update(['active' => 1]);
            $input['success'] = 'Session is activated Successfully';
        }
        else
        {
            Session::where('id', $id)->update(['active' => 0]);
            $input['success'] = 'Session is deactivated Successfully';
        }
        return Redirect::back()->withInput($input);
    }*/
    /** @ updated 14-4-2018 by priya @ **/
    public function  operateSession($id)
    {
        $getThisIDValue = Session::where('id',$id)->first();
        if($getThisIDValue->active == 0)
        {
            $checkDetails =Session::where('school_id',\Auth::user()->school_id)
                ->where('active',1)->count();
            if($checkDetails >= 1)
            {
                $getActiveId = Session::where('active',1)->first();
                $input['error']='  Sorry !!! Already Session '.$getActiveId->session.' is in Active !!!';
            }
            else
            {
                Session::where('id',$id)
                    ->update([
                        'active' => 1
                    ]);
                $input['success']='  '.$getThisIDValue->session.' is Activated Now Successfully !!!';
            }
        }
        else
        {
            Session::where('id',$id)
                ->update([
                    'active'=> 0
                ]);
            $input['success']='  '.$getThisIDValue->session.' is Deactivated Now Successfully!!!';
        }
        return \Redirect::back()->withInput($input);
    }

    public function masterClass()
    {
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        return view('users.master.class.class', compact('classes'));
    }

    public function postClass(Request $request, addClass $class)
    {
        $usererror = ['class' => 'Class'];
        $validator = \Validator::make($request->all(), [
            'class' => 'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $class->doPostClass($request, $this->user);
    }

    public function deleteClass(addClass $class, $id)
    {
        return $class->doDeleteClass($id);
    }

    public function editClass(addClass $class, $id)
    {
        return $class->doEditClass($id);
    }

    public function updateClass(Request $request, addClass $class)
    {
        $usererror = ['id' => 'Class Id', 'class' => 'Class'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'class' => 'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $class->doUpdateClass($request, $this->user);
    }

    public function masterSection()
    {
        $classes = addClass::where('school_id', $this->user->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        $allsubjects = Subject::where('school_id', $this->user->school_id)->orderBy('subject', 'ASC')->get();
        $allsection = Section::where('section.school_id', $this->user->school_id)
            ->where('section.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('class', 'section.class_id', '=', 'class.id')
            ->select
            (
                'section.id',
                'section.section',
                'class.id as class_id',
                'class.class',
                'section.subjects'
            )
            ->get();
        foreach($allsection as $section)
        {
            $subjects = Subject::whereIn('id', json_decode($section->subjects))->get();
            $sections[] = array(
                'id' => $section->id,
                'section' => $section->section,
                'class_id' => $section->class_id,
                'class' => $section->class,
                'subjects' => $subjects
            );
        }
        return view('users.master.section.section', compact('sections', 'classes', 'allsubjects'));
    }

    public function postSection(Section $section)
    {
        $input = \Request::all();
        $userError = ['Class' => 'Class Id', 'section' => 'Section', 'subjects' => 'Subjects'];
        $validator = \Validator::make($input,[
            'class' => 'required',
            'section' => 'required',
            'subjects' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $section->doPostSection($input, $this->user);
    }

    public function deleteSection(Section $section, $id)
    {
        return $section->doDeleteSection($id);
    }

    public function editSection(Section $section, $id)
    {
        return $section->doEditSection($id, $this->user);
    }

    public function updateSection(Request $request, Section $section)
    {
        $input = \Request::all();
        $userError = ['id' => 'Section Id', 'Class' => 'Class Id', 'section' => 'Section', 'subjects' => 'Subjects'];
        $validator = \Validator::make($input,[
            'id' => 'required|numeric',
            'class'=>'required|numeric',
            'section'=>'required',
            'subjects' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $section->doUpdateSection($input, $this->user);
    }

    public function masterSubject()
    {
        $subjects = Subject::where('school_id', $this->user->school_id)->get();
        return view('users.master.subject.subject', compact('subjects'));
    }

    public function getSection()
    {
        $classId = \Request::get('srclass');
        $sections = Section::where('class_id', $classId)->where('school_id', $this->user->school_id)->get();
        return $sections;
    }

    public function postSubject(Request $request, Subject $subject)
    {
        $userError = ['subject' => 'Subject'];
        $validator = \Validator::make($request->all(),[
            'subject' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $subject->doPostSubject($request, $this->user);
    }

    public function deleteSubject(Subject $subject, $id)
    {
        return $subject->doDeleteSubject($id);
    }

    public function editSubject(Subject $subject, $id)
    {
        return $subject->doEditSubject($id, $this->user);
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $userError = ['id' => 'Subject Id', 'subject' => 'Subject'];
        $validator = \Validator::make($request->all(),[
            'id' => 'required|numeric',
            'subject' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $subject->doUpdateSubject($request, $this->user);
    }

    public function masterExam()
    {
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        return view('users.master.exam.exam', compact('exams'));
    }
    // public function gradesystem(request $request){
    //     foreach($request["exam"] as $exkey=>$exvalue){
    //         $check_grade=\DB::table('grade_system')->where('school_id','=',Auth::user()->school_id)
    //         ->where('exam_type_id','=',$request['exam'][$exkey])
    //         ->where('from_marks','=',$request['frommarks'][$exkey])
    //         ->where('to_marks','=',$request['tomarks'][$exkey])
    //         ->where('grade','=',$request['grade'][$exkey])
    //         //->where('result','=',$request['result'][$exkey])
    //         ->first();
    //         if(empty($check_grade)){
    //             \DB::table('grade_system')->insert([
    //             'school_id' => Auth::user()->school_id,
    //             'exam_type_id' => $request['exam'][$exkey],
    //             'from_marks'=> $request['frommarks'][$exkey],
    //             'to_marks' =>$request['tomarks'][$exkey],
    //             'grade'=>$request['grade'][$exkey],
    //             'result'=>$request['result'][$exkey]
    //             ]);
    //             $input['success'] = 'Grade Type is saved successfully';
    //         }
    //     }
    //     //return $this->masterExam();
    //     return \Redirect::back()->withInput($input);
    // }
   // public function gradesystem(request $request){//change by mari 27-09-2017
     //   $userError = ['exam'=>'Exam type','frommarks'=>'From Marks','tomarks'=>'To Marks','grade'=>'grade','result'=>'Result'];
     //   $validator = \Validator::make($request->all(),[
     //       'exam'=>'required',
     //       'frommarks'=>'required',
     //       'tomarks'=>'required',
     //       'grade'=>'required',
     //       'result'=>'required'
     //   ], $userError);
     //   $validator->setAttributeNames($userError);
      //  if($validator->fails())
      //      return \Redirect::back()->withErrors($validator);
      //  else{
      //      $mark_exceed=\DB::table('exam')
      //          ->where('school_id','=',Auth::user()->school_id)->where('id','=',$request['exam'][0])
      //          ->first();

         //   foreach($request["exam"] as $exkey=>$exvalue){
         //       if(($request['frommarks'][$exkey]>$mark_exceed->max_marks)||($request['tomarks'][$exkey]>$mark_exceed->max_marks)){
         //           $msg['error'] = 'Grade Marks should below Maximum marks';
          //          return \Redirect::back()->withInput($msg);
          //      }
          //      if((($request['frommarks'][$exkey]>=$mark_exceed->pass_marks)||($request['tomarks'][$exkey]>=$mark_exceed->pass_marks))&&$request['result'][$exkey]=='Fail'){
          //          $msg['error'] = "Given mark is greater than passmark";
          //          return \Redirect::back()->withInput($msg);
          //      }
          //      else if((($request['frommarks'][$exkey]<$mark_exceed->pass_marks)||($request['tomarks'][$exkey]<=$mark_exceed->pass_marks))&&$request['result'][$exkey]=='Pass'){
          //          $msg['error'] = "Given mark is Less than passmark";
          //          return \Redirect::back()->withInput($msg);
          //      }
          //      $check_grade=\DB::table('grade_system')->where('school_id','=',Auth::user()->school_id)
          //          ->where('exam_type_id','=',$request['exam'][$exkey])
          //          ->where('from_marks','=',$request['frommarks'][$exkey])
          //          ->where('to_marks','=',$request['tomarks'][$exkey])
          //          ->where('grade','=',$request['grade'][$exkey])
                    //->where('result','=',$request['result'][$exkey])
         //           ->first();
          //      if(empty($check_grade)){
           //          $grade_from_exits=\DB::table('grade_system')
          //              ->where('school_id','=',Auth::user()->school_id)
          //              ->where('exam_type_id','=',$request['exam'][$exkey])
          //              ->where('to_marks','=',$request['frommarks'][$exkey])
          //              ->first();
          //          if(!empty($grade_from_exits)){
          //              $msg['error']="From mark is not equal to Previous To Marks";
          //              return \Redirect::back()->withInput($msg);

           //             }
           //         \DB::table('grade_system')->insert([
           //             'school_id' => Auth::user()->school_id,
           //             'exam_type_id' => $request['exam'][$exkey],
           //             'from_marks'=> $request['frommarks'][$exkey],
           //             'to_marks' =>$request['tomarks'][$exkey],
           //             'grade'=>$request['grade'][$exkey],
           //             'result'=>$request['result'][$exkey],
           //             'remarks'=>$request['remarks'][$exkey] //updated 10-5-2018
           //         ]);
           //         $input['success'] = 'Grade Type is saved successfully';
           //     }
           // }
       // }
        //return $this->masterExam();
      //  return \Redirect::back()->withInput($input);
   // }
    public function gradesystem(request $request){//change by mari 27-09-2017
        $userError = ['exam'=>'Exam type','frommarks'=>'From Marks','tomarks'=>'To Marks','grade'=>'grade','result'=>'Result'];
        $validator = \Validator::make($request->all(),[
            'exam'=>'required',
            'frommarks'=>'required',
            'tomarks'=>'required',
            'grade'=>'required',
            'result'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        else{
            $mark_exceed=\DB::table('exam')
                ->where('school_id','=',Auth::user()->school_id)->where('id','=',$request['exam'][0])
                ->first();

            foreach($request["exam"] as $exkey=>$exvalue){
                if(($request['frommarks'][$exkey]>$mark_exceed->max_marks)||($request['tomarks'][$exkey]>$mark_exceed->max_marks)){
                    $msg['error'] = 'Grade Marks should below Maximum marks';
                    return \Redirect::back()->withInput($msg);
                }
                if((($request['frommarks'][$exkey]>=$mark_exceed->pass_marks)||($request['tomarks'][$exkey]>=$mark_exceed->pass_marks))&&$request['result'][$exkey]=='Fail'){
                    $msg['error'] = "Given mark is greater than passmark";
                    return \Redirect::back()->withInput($msg);
                }
                else if((($request['frommarks'][$exkey]<$mark_exceed->pass_marks)||($request['tomarks'][$exkey]<=$mark_exceed->pass_marks))&&$request['result'][$exkey]=='Pass'){
                    $msg['error'] = "Given mark is Less than passmark";
                    return \Redirect::back()->withInput($msg);
                }
                $check_grade=\DB::table('grade_system')->where('school_id','=',Auth::user()->school_id)
                    ->where('exam_type_id','=',$request['exam'][$exkey])
                    ->where('from_marks','=',$request['frommarks'][$exkey])
                    ->where('to_marks','=',$request['tomarks'][$exkey])
                    ->where('grade','=',$request['grade'][$exkey])
                    //->where('result','=',$request['result'][$exkey])
                    ->first();
                if(empty($check_grade)){
                     $grade_from_exits=\DB::table('grade_system')
                        ->where('school_id','=',Auth::user()->school_id)
                        ->where('exam_type_id','=',$request['exam'][$exkey])
                        ->where('to_marks','=',$request['frommarks'][$exkey])
                        ->first();
                    if(!empty($grade_from_exits)){
                        $msg['error']="From mark is not equal to Previous To Marks";
                        return \Redirect::back()->withInput($msg);

                        }
                    \DB::table('grade_system')->insert([
                        'school_id' => Auth::user()->school_id,
                        'exam_type_id' => $request['exam'][$exkey],
                        'from_marks'=> $request['frommarks'][$exkey],
                        'to_marks' =>$request['tomarks'][$exkey],
                        'grade'=>$request['grade'][$exkey],
                        'result'=>$request['result'][$exkey],
                        'remarks'=>$request['remarks'][$exkey] //updated 10-5-2018
                    ]);
                    $input['success'] = 'Grade Type is saved successfully';
                }
            }
        }
        //return $this->masterExam();
        return \Redirect::back()->withInput($input);
    }

    public function fasagradesystem(request $request){//change by mari 27-09-2017
        $input = \Request::all();
       
            foreach ($input['frommarks'] as $key => $value) {
              \DB::table('grade_system')->insert([
                        'school_id' => Auth::user()->school_id,
                        'exam_type_id' => $input['exam'],
                         'from_marks'=> $input['frommarks'][$key],
                        'to_marks' =>$input['tomarks'][$key],
                         'frfamark'=> $input['fromfamarks'][$key],
                        'tofamark' =>$input['tofamarks'][$key],
                        'frsamark'=> $input['fromsamarks'][$key],
                        'tosamark' =>$input['tosamarks'][$key],
                        'fagrade'=>$input['fagrade'][$key],
                        'sagrade'=>$input['sagrade'][$key],
                        'grade'=>$input['grade'][$key],
                        'result'=>$input['result'][$key],
                        'remarks'=>$input['remarks'][$key] //updated 10-5-2018
                    ]);
            }
                    
                    $input['success'] = 'Grade Type is saved successfully';
        return \Redirect::back()->withInput($input);
    }


    public function deleteresultgrade($id){
        //$ge=\DB::table('grade_system')->where('id','=',$id)->first();
        $dlet=\DB::table('grade_system')->where('id','=',$id)->delete();
        $input['success'] = 'Grade Type is deleted successfully';
        return \Redirect::back()->withInput($input);
    }
   
   public function createGrade($id){
        $examtype=$id;
        $exams = Exam::where('school_id', $this->user->school_id)->where('id',$examtype)->first();
        $grades=\DB::table('grade_system')->where('grade_system.school_id', $this->user->school_id)
            ->join('exam','grade_system.exam_type_id','=','exam.id')
            ->where('grade_system.exam_type_id','=',$examtype)
            ->select('exam.exam_type','exam.id','grade_system.id as gid','grade_system.from_marks'
                ,'grade_system.to_marks','grade_system.grade',
                'grade_system.remarks', //updated 10-5-2018 by priya
                'grade_system.result'
                )
            ->get();
        return view('users.master.exam.grade',compact('exams','grades'));
    }
    public function createfasaGrade($id){
        $examtypeid=$id;
        $examtype = Exam::where('school_id', $this->user->school_id)->where('id',$examtypeid)->first();
        //dd($examtype->exam_type);
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        $grades=\DB::table('grade_system')->where('grade_system.school_id', $this->user->school_id)
            ->join('exam','grade_system.exam_type_id','=','exam.id')
            ->where('grade_system.exam_type_id','=',$examtypeid)
            ->select('exam.exam_type','exam.id','grade_system.id as gid','grade_system.from_marks'
                ,'grade_system.to_marks','grade_system.grade','grade_system.frfamark'
                ,'grade_system.tofamark','grade_system.fagrade','grade_system.frsamark'
                ,'grade_system.tosamark','grade_system.sagrade',
                'grade_system.remarks', //updated 10-5-2018 by priya
                'grade_system.result'
                )
            ->get();
        return view('users.master.exam.fasagrade',compact('exams','grades','examtype'));
    }

    public function postExamType(Request $request, Exam $exam) // mari 27-09-2017
    {
        $userError = ['exam_type'=>'Exam type','pass_marks'=>'Pass Marks','max_marks'=>'Maximum Marks'];
        $validator = \Validator::make($request->all(),[
            'exam_type'=>'required',
            'pass_marks'=>'required|numeric',
            'max_marks'=>'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $exam->doPostExamType($request, $this->user);
    }

    public function getExamTypes(Exam $exam)
    {
        return $exam->doGetExamTypes($this->user);
    }

    public function deleteExamType(Exam $exam, $id)
    {
        return $exam->doDeleteExamType($id);
    }

    public function editExamType(Exam $exam, $id)
    {
        return $exam->doEditExamType($id);
    }

    public function updateExamType(Request $request, Exam $exam)
    {
        //change by mari 27-09-2017 

        $userError = ['id' => 'Exam Type Id', 'exam_type'=>'Exam type'];
        $validator = \Validator::make($request->all(),[
            'id' => 'required|numeric',
            'exam_type'=>'required',
            'pass_marks'=>'required|numeric',
            'max_marks'=>'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $exam->doUpdateExamType($request, $this->user);
    }

    public function masterStaff()
    {
        $staffs = Staff::where('school_id', $this->user->school_id)->get();
        return view('users.master.staff.staff', compact('staffs'));
    }

    public function postStaffType(Request $request, Staff $staff)
    {
        $userError = ['staff_type'=>'Staff Type'];
        $validator = \Validator::make($request->all(), [
            'staff_type'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $staff->doPostStaffType($request, $this->user);
    }

    public function deleteStaffType(Staff $staff, $id)
    {
        return $staff->doDeleteStaffType($id);
    }

    public function editStaffType(Staff $staff, $id)
    {
        return $staff->doEditStaffType($id);
    }

    public function updateStaffType(Request $request, Staff $staff)
    {
        $userError = ['id' => 'Staff Type Id', 'staff_type'=>'Staff Type'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'staff_type'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $staff->doUpdateStaffType($request, $this->user);
    }

    public function masterEvents()
    {
        $events = Events::where('school_id', $this->user->school_id)->get();
        return view('users.master.events.event', compact('events'));
    }

    public function postEvents(Request $request, Events $event)
    {
        $userError = ['events'=>'Events'];
        $validator = \Validator::make($request->all(), [
            'events'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $event->doPostEvents($request, $this->user);
    }

    public function deleteEvents(Events $event, $id)
    {
        return $event->doDeleteEvents($id);
    }

    public function editEvents(Events $event, $id)
    {
        return $event->doEditEvents($id);
    }

    public function updateEvents(Request $request, Events $event)
    {
        $userError = ['id' => 'Event Id', 'events'=>'Events'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'events'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $event->doUpdateEvents($request, $this->user);
    }

    public function masterCaste()
    {
        $castes = Caste::where('school_id', $this->user->school_id)->get();
        return view('users.master.caste.caste', compact('castes'));
    }

    public function postCaste(Request $request, Caste $caste)
    {
        $userError = ['caste' => 'Caste'];
        $validator = \Validator::make($request->all(), [
            'caste'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $caste->doPostCaste($request, $this->user);
    }

    public function deleteCaste(Caste $caste, $id)
    {
        return $caste->doDeleteCaste($id);
    }

    public function editCaste(Caste $caste, $id)
    {
        return $caste->doEditCaste($id);
    }

    public function updateCaste(Request $request, Caste $caste)
    {
        $userError = ['id' => 'Caste Id', 'caste' => 'Caste'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'caste'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $caste->doUpdateCaste($request, $this->user);
    }

    public function masterGrade()
    {
        $grades = Grade::where('school_id', $this->user->school_id)->get();
        return view('users.master.grade.grade', compact('grades'));
    }

    public function postGrade(Request $request, Grade $grade)
    {
        $userError = ['min' => 'Minimum Number', 'max' => 'Maximum Number', 'grade' => 'Grade'];
        $validator = \Validator::make($request->all(), [
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'grade' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $grade->doPostGrade($request, $this->user);
    }

    public function deleteGrade(Grade $grade, $id)
    {
        return $grade->doDeleteGrade($id);
    }

    public function editGrade(Grade $grade, $id)
    {
        return $grade->doEditGrade($id);
    }

    public function updateGrade(Request $request, Grade $grade)
    {
        $userError = ['id' => 'Grade Id', 'min' => 'Minimum Number', 'max' => 'Maximum Number', 'grade' => 'Grade'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'grade' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $grade->doUpdateGrade($request, $this->user);
    }

    public function masterReligion()
    {
        $religions = Religion::where('school_id', $this->user->school_id)->get();
        return view('users.master.religion.religion', compact('religions'));
    }

    public function postReligion(Request $request, Religion $religion)
    {
        $userError = ['religion' => 'Religion'];
        $validator = \Validator::make($request->all(), [
            'religion'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $religion->doPostReligion($request, $this->user);
    }

    public function deleteReligion($id)
    {
        $stu_exist = Students::where('religion',$id)->first();
        if(count($stu_exist)>0){
            $input['error'] = "Religion can't be deleted. Religion mapped to student";
            return \Redirect::back()->withInput($input);
        }
        Religion::where('id', $id)->delete();
        $input['success'] = 'Religion is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }


    public function editReligion($id)
    {
        $religion = Religion::where('id', $id)->first();
        return view('users.master.religion.edit', compact('religion'));
    }

    public function updateReligion(Religion $religion, Request $request)
    {
        $userError = ['id' => 'Religion Id', 'religion' => 'Religion'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'religion'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $religion->doUpdateReligion($request, $this->user);
    }

    public function masterBus()
    {
        $buses = Bus::where('school_id', $this->user->school_id)->get();
        return view('users.master.bus.bus', compact('buses'));
    }

    public function postBus(Bus $bus, Request $request)
    {
        $userError = ['bus_no' => 'Bus No', 'bus_type' => 'Bus Type', 'bus_owned_by' => 'Bus Owned By',  'capacity' => 'Capacity', 'route' => 'Bus Route', 'city' => 'City'];
        $validator = \Validator::make($request->all(), [
            'bus_no' => 'required',
            'bus_type'=>'required',
            'bus_owned_by'=>'required',
            /*'gps_no' => 'required',*/
            'capacity'=>'required|numeric',
            'route'=>'required',
            'city'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $bus->doPostBus($request, $this->user);
    }

    public function deleteBus($id)
    {

        $check=Students::where('bus_id',$id)->where('school_id',\Auth::user()->school_id)
        ->where('session_id',$this->active_session->id)//updated 14-4-2018
        ->get();
        if(count($check)==0)
        {
            Bus::where('id', $id)->delete();
            BusStop::where('bus_id', $id)->delete();
            $driver_userid=Driver::where('bus_id',$id)->first();
            User::where('id', $driver_userid->user_id)->delete();
            Driver::where('bus_id',$id)->delete();
            $input['success'] = 'Bus is deleted Successfully';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            $input['error'] = 'Please Delete Assigned Students';
            return \Redirect::back()->withInput($input);

        }
    }

    public function editBus($id)
    {
        $bus = Bus::where('id', $id)->first();
        return view('users.master.bus.edit', compact('bus'));
    }

    public function updateBus(Bus $bus, Request $request)
    {
        $userError = ['id' => 'Bus Id', 'bus_no' => 'Bus No', 'bus_type' => 'Bus Type', 'bus_owned_by' => 'Bus Owned By',  'capacity' => 'Capacity', 'route' => 'Bus Route', 'city' => 'City'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'bus_no' => 'required',
            'bus_type'=>'required',
            'bus_owned_by'=>'required',
            /*'gps_no' => 'required',*/
            'capacity'=>'required|numeric',
            'route'=>'required',
            'city'=>'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $bus->doUpdateBus($request, $this->user);
    }

    public function masterBusStop()
    {
        $buses = Bus::where('school_id', $this->user->school_id)->get();
        $stops = BusStop::where('bus_stop.school_id', $this->user->school_id)
            ->leftJoin('bus', 'bus_stop.bus_id', '=', 'bus.id')
            ->select
            (
                'bus_stop.id',
                'bus.bus_no',
                'bus.route',
                'bus.city',
                'bus_stop.stop',
                'bus_stop.stop_index',
                'bus_stop.lattitude',
                'bus_stop.longitude',
                'bus_stop.transport_fee'
            )
            ->get();
        $busnos=array();
        foreach($stops as $stop)
        {
            $busnos[] = $stop->bus_no;
        }
        $buses = Bus::where('school_id', $this->user->school_id)->whereNotIn('bus_no', $busnos)->get();
        return view('users.master.stop.stop', compact('stops', 'buses'));
    }

    public function postBusStop(BusStop $bus, Request $request)
    {
        $userError = ['bus_id' => 'Bus Id', 'stop' => 'Stop Name', 'stop_index' => 'Stop Index', 'lattitude' => 'Lattitude', 'longitude' => 'Longitude', 'transport_fee' => 'Transport Fee'];
        $validator = \Validator::make($request->all(), [
            'bus_id' => 'required|numeric',
            'stop' => 'required',
            'stop_index' => 'required|numeric',
            'lattitude' => 'required',
            'longitude' => 'required',
            'transport_fee' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $bus->doPostBusStop($request, $this->user);
    }

    public function deleteBusStop($id)
    {
        BusStop::where('id', $id)->delete();
        $input['success'] = 'Bus Stop is deleted Successfully';
        return Redirect::back()->withInput($input);
    }

    public function editBusStop($id)
    {
        $stop = BusStop::where('id', $id)->first();
        $buses = Bus::where('school_id', $this->user->school_id)->get();
        return view('users.master.stop.edit', compact('stop', 'buses'));
    }

    public function updateBusStop(BusStop $bus, Request $request)
    {
        $userError = ['id' => 'Stop Id', 'bus_id' => 'Bus Id', 'stop' => 'Stop Name', 'stop_index' => 'Stop Index', 'lattitude' => 'Lattitude', 'longitude' => 'Longitude', 'transport_fee' => 'Transport Fee'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'bus_id' => 'required|numeric',
            'stop' => 'required',
            'stop_index' => 'required|numeric',
            'lattitude' => 'required',
            'longitude' => 'required',
            'transport_fee' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $bus->doUpdateBusStop($request, $this->user);
    }

    public function masterDriver()
    {
        $drivers = Driver::where('driver.school_id', $this->user->school_id)
            ->leftJoin('bus', 'driver.bus_id', '=', 'bus.id')
            ->leftJoin('users','driver.user_id', '=','users.id')
            ->select
            (
                'driver.id',
                'bus.bus_no',
                'bus.route',
                'bus.city',
                'driver.driver_name',
                'driver.driver_mobile',
                'driver.driver_address',
                'driver.driver_city',
                'users.username',
                'users.hint_password'

            )
            ->get();
        $busnos=array();
        foreach($drivers as $driverbus)
        {
            $busnos[] = $driverbus->bus_no;
        }
        $buses = Bus::where('school_id', $this->user->school_id)->whereNotIn('bus_no', $busnos)->get();
        return view('users.master.driver.driver', compact('buses', 'drivers'));
    }

    public function postDriver(Driver $driver, Request $request)
    {
        $userError = ['bus_id' => 'Bus Id', 'driver_name' => 'Driver Name', 'driver_mobile' => 'Driver Mobile', 'driver_address' => 'Driver Address', 'driver_city' => 'Driver City'];
        $validator = \Validator::make($request->all(), [
            'bus_id' => 'required|numeric',
            'driver_name' => 'required',
            'driver_mobile' => 'required',
            'driver_address' => 'required',
            'driver_city' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $driver->doPostDriver($request, $this->user);
    }

    public function deleteDriver($id)
    {
        $driver_userid=Driver::where('id',$id)->first();
        User::where('id', $driver_userid->user_id)->delete();

        Driver::where('id', $id)->delete();
        $input['success'] = 'Driver is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function editDriver($id)
    {
        $driver = Driver::where('id', $id)->first();
        $buses = Bus::where('school_id', $this->user->school_id)->get();
        $users=User::where('id',$driver->user_id)->first();
        return view('users.master.driver.edit', compact('driver', 'buses','users'));
    }

    public function updateDriver(Driver $driver, Request $request)
    {
        $userError = ['id' => 'Driver Id', 'bus_id' => 'Bus Id', 'driver_name' => 'Driver Name', 'driver_mobile' => 'Driver Mobile', 'driver_address' => 'Driver Address', 'driver_city' => 'Driver City'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'bus_id' => 'required|numeric',
            'driver_name' => 'required',
            'driver_mobile' => 'required',
            'driver_address' => 'required',
            'driver_city' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $driver->doUpdateDriver($request, $this->user);
    }

    public function masterHoliday()
    {
        $holidays = Holiday::where('school_id', $this->user->school_id)->get();
        return view('users.master.holiday.holiday', compact('holidays'));
    }

    public function postHoliday(Holiday $holiday, Request $request)
    {
        $userError = ['holiday' => 'Holiday', 'date' => 'Date', 'remarks' => 'Remarks'];
        $validator = \Validator::make($request->all(), [
            'holiday' => 'required',
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $holiday->doPostHoliday($request, $this->user);
    }

    public function deleteHoliday($id)
    {
        $holiday = Holiday::where('id', $id)->delete();
        $input['success'] = 'Holiday is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function editHoliday($id)
    {
        $holiday = Holiday::where('id', $id)->first();
        return view('users.master.holiday.edit', compact('holiday'));
    }

    public function updateHoliday(Holiday $holiday, Request $request)
    {
        $userError = ['id' => 'Holiday Id', 'holiday' => 'Holiday', 'date' => 'Date', 'remarks' => 'Remarks'];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'holiday' => 'required',
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $holiday->doUpdateHoliday($request, $this->user);
    }

    public function masterSalary()
    {
        $salaries = Salary::where('school_id', $this->user->school_id)->get();
        return view('users.master.salary.salary', compact('salaries'));
    }

    public function postSalary(Salary $salary)
    {
        $request = \Request::all();
        $userError = ['salary' => 'Salary Structure'];
        $validator = \Validator::make($request, ['salary' => 'required|numeric'], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $salary->doPostSalary($request, $this->user);
    }

    public function deleteSalary($id)
    {
        $holiday = Salary::where('id', $id)->delete();
        $input['success'] = 'Salary Structure is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function editSalary($id)
    {
        $salary = Salary::where('id', $id)->first();
        return view('users.master.salary.edit', compact('salary'));
    }

    public function updateSalary(Salary $salary)
    {
        $request = \Request::all();
        $userError = ['id' => 'Salary Structure Id', 'salary' => 'Salary Structure'];
        $validator = \Validator::make($request, [
            'id' => 'required|numeric',
            'salary' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $salary->doUpdateSalary($request, $this->user);
    }

    public function masterDeposit()
    {
        $classes = addClass::where('school_id', $this->user->school_id)->get();
        $amounts = Amount::where('amount.school_id', $this->user->school_id)
            ->leftJoin('class', 'amount.class_id', '=', 'class.id')
            ->select
            (
                'amount.id',
                'amount.amount',
                'class.class'
            )
            ->get();
        return view('users.master.amount.amount', compact('amounts', 'classes'));
    }

    public function postDeposit(Amount $amount)
    {
        $request = \Request::all();
        $userError = ['class' => 'Class', 'amount' => 'Amount'];
        $validator = \Validator::make($request, ['class' => 'required|numeric', 'amount' => 'required|numeric'], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $amount->doPostDeposit($request, $this->user);
    }

    public function deleteDeposit($id)
    {
        $amount = Amount::where('id', $id)->delete();
        $input['success'] = 'Class Amount is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function editDeposit($id)
    {
        $classes = addClass::where('school_id', $this->user->school_id)->get();
        $amount = Amount::where('id', $id)->first();
        return view('users.master.amount.edit', compact('amount', 'classes'));
    }

    public function updateDeposit(Amount $amount)
    {
        $request = \Request::all();
        $userError = ['id' => 'Amount Id', 'class' => 'Class Id', 'amount' => 'Amount'];
        $validator = \Validator::make($request, [
            'id' => 'required|numeric',
            'class' => 'required|numeric',
            'amount' => 'required|numeric'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $amount->doUpdateDeposit($request, $this->user);
    }

    public function masterNotification()
    {
        $notifications = NotificationType::where('school_id', $this->user->school_id)->get();
        return view('users.master.notification.notification', compact('notifications'));
    }

    public function postNotification(NotificationType $notification)
    {
        $request = \Request::all();
        $userError = ['title' => 'Title', 'description' => 'Description'];
        $validator = \Validator::make($request,
            ['title' => 'required', 'description' => 'required'],
            $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $notification->doPostNotification($request, $this->user);
    }

    public function deleteNotification($id)
    {
        $holiday = NotificationType::where('id', $id)->delete();
        $input['success'] = 'Notification is deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function editNotification($id)
    {
        $notification = NotificationType::where('id', $id)->first();
        return view('users.master.notification.edit', compact('notification'));
    }

    public function updateNotification(NotificationType $notification)
    {
        $request = \Request::all();
        $userError = ['id' => 'Notification Id', 'title' => 'Title', 'description' => 'Description'];
        $validator = \Validator::make($request, [
            'id' => 'required|numeric',
            'title' => 'required',
            'description' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $notification->doUpdateNotification($request, $this->user);
    }

    public function getMonths()
    {
        $months = \DB::table('month')->get();
        return \api(['data' => $months]);
    }

    // Export Master Methods
    public function expotSessionView()
    {
        $sessions = Session::where('active', 1)->where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.session', compact('sessions'));
    }
    public function expotSession(Session $session)
    {
        return $session->doExportMasterSession($this->user);
    }
    public function expotClassView()
    {
        $classes = addClass::where('school_id', $this->user->school_id)
         ->where('session_id',$this->active_session->id)//updated 14-4-2018 
        ->get();
        return view('users.export-manager.class', compact('classes'));
    }
    public function exportClass(addClass $class)
    {
        return $class->doExportMasterClass($this->user);
    }
    public function expotSectionView()
    {
        $allsections = Section::where('section.school_id', $this->user->school_id)
            ->where('section.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('class', 'section.class_id', '=', 'class.id')
            ->select
            (
                'section.id',
                'section.section',
                'class.id as class_id',
                'class.class',
                'section.subjects'
            )
            ->orderBy('section.id', 'ASC')
            ->get();
        foreach($allsections as $section)
        {
            $subjects = \DB::table('subject')->whereIn('id', json_decode($section->subjects))->select('subject')->get();
            $subs = [];
            foreach($subjects as $subject)
            {
                $subs[] = $subject->subject;
            }
            $mainsub = implode(", ", $subs);
            $sections[] = array(
                'id' => $section->id,
                'section' => $section->section,
                'class_id' => $section->class_id,
                'class' => $section->class,
                'subjects' => $mainsub
            );
        }
        return view('users.export-manager.section', compact('sections'));
    }
    public function exportSection(Section $section)
    {
        return $section->doExportMasterSection($this->user);
    }
    public function expotSubjectView()
    {
        $subjects = Subject::where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.subject', compact('subjects'));
    }
    public function exportSubject(Subject $subject)
    {
        return $subject->doExportMasterSubject($this->user);
    }
    public function exportExamView()
    {
        $exams = Exam::where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.exam', compact('exams'));
    }
    public function exportExamType(Exam $exam)
    {
        return $exam->doExportMasterExamType($this->user);
    }
    public function exportStaffView()
    {
        $staffs = Staff::where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.staff', compact('staffs'));
    }
    public function exportStaffType(Staff $staff)
    {
        return $staff->doExportMasterStaffType($this->user);
    }
    public function exportEventsView()
    {
        $events = Events::where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.events', compact('events'));
    }
    public function exportEvents(Events $event)
    {
        return $event->doExportMasterEvents($this->user);
    }
    public function exportCasteView()
    {
        $castes = Caste::where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.caste', compact('castes'));
    }
    public function exportCaste(Caste $caste)
    {
        return $caste->doExportMasterCaste($this->user);
    }
    public function exportReligionView()
    {
        $religions = Religion::where('school_id', $this->user->school_id)->get();
        return view('users.export-manager.religion', compact('religions'));
    }
    public function exportReligion(Religion $religion)
    {
        return $religion->doExportMasterReligion($this->user);
    }
    public function exportBusView()
    {
        $buses = Bus::where('bus.school_id', $this->user->school_id)
            ->leftJoin('bus_stop', 'bus.id', '=', 'bus_stop.bus_id')
            ->select
            (
                'bus.id',
                'bus.bus_no',
                'bus.bus_type',
                'bus.bus_owned_by',
                /*'bus.gps_no',*/
                'bus.capacity',
                'bus.route',
                'bus.city',
                'bus_stop.stop',
                'bus_stop.stop_index',
                'bus_stop.lattitude',
                'bus_stop.longitude'
            )->get();
        return view('users.export-manager.bus', compact('buses'));
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
       // $check_paidamt=\DB::table('contractor_paidamt')->where('school_id', \Auth::user()->school_id)->where('build_id', $build_id )->first();
        //dd($check_paidamt,$all_payment_id);
        //if(!empty($all_payment_id)){
            foreach ($all_payment_id as $key => $value) {
                //dd($value,$build_id);
                $all_paidamt=\DB::table('contractor_paidAmt')->where('school_id', \Auth::user()->school_id)
                ->where('build_id', $build_id )
                ->where('fee_id', $value )
                ->get();
              // $all_paidamt1[] = \DB::table('contractor_paidamt')->where('school_id', \Auth::user()->school_id)->get();
            }
            dd($all_paidamt,$all_paidamt1);
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
                    dd($allunpaid_ids);
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
                    
       // }
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
    public function exportBus(Bus $bus)
    {
        return $bus->exportMasterBus($this->user);
    }
}