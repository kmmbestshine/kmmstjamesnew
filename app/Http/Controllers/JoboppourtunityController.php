<?php

//namespace App\Http\Controllers;

//use Illuminate\Http\Request;

//use App\Http\Requests;
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

class JoboppourtunityController extends Controller
{
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
            view()->share(compact('classes', 'employees','male_students','tobeupdategender','female_students', 'students', 'school_image', 'birthdays', 'examtypes', 'busCount', 'abses', 'roler','userplans'));
        }
    }
    public function teachersRecruitment() {
        $bio_answernew=DB::table('biodata')->get();
        $bio_answer=DB::table('bio_results')->get();
        foreach ($bio_answer as $key => $value) {
            $bio_answerID[]=$value->teacher_id;
        }
        $collection = collect($bio_answerID);
            $unique_staff_id = $collection->unique()->values()->all();
        foreach ($unique_staff_id as $key => $id ) {
            $biodata[]=DB::table('biodata')->where('id','=',$id)->get();
        }
        //dd($biodata);
        foreach ($biodata as $data1) {
            foreach ($data1 as $data ) {
        $bio_answer=DB::table('bio_results')
        ->where('teacher_id','=',$data->id)
        ->first();
        $data->type=$bio_answer->type;
            }
        }
    // not taken exam staff
        $biodata_all=DB::table('biodata')->get();
        foreach ($biodata_all as $key => $value) {
            $all_dataID[]=$value->id;
        }
         $allnot_Atend_ids= array_diff($all_dataID, $unique_staff_id);
         if(!empty($allnot_Atend_ids))
                    {
                        $all_not_taken_exam=array();
                    foreach ($allnot_Atend_ids as $key => $value) {
                            $all_not_taken_exam[] = DB::table('biodata')->where('id',$value)->get();
                        }
                        }
        
        return view('users.teachers_test.list', compact('biodata','all_not_taken_exam','bio_answernew'));
    }
    public function staffRecruitmentdelete($id) {
        \DB::table('biodata')->where('id', $id)->delete();
        $msg['success'] = 'Success to delete Staff List';
        return \Redirect::back()->withInput($msg);
    }
    public function democlass_checklist($id) {
        $check_list=["Indroduction","Motivation","Communicative English","Subject Knowledge","Board Work","Body Language","Class Room Management","Confidence Level","Audibility Of Teaching"];
        $check_list1=["Spelling Mistakes","Pronunciation Mistakes"];
        $biodata = DB::table('biodata')->where('id', $id)->first();
        return view('users.teachers_test.democlass_checklist',compact('check_list','check_list1','biodata'));
        
    }
    public function viewquestion() {
       // dd('hhhhhhhhh');
        $questions = \DB::table('bio_qutions')
        ->where('school_id', $this->user->school_id)
        ->where('session_id', $this->active_session->id)->get();
        return view('users.teachers_test.viewQuestion', compact('questions'));
    }
    public function createSchool() {
       return view('users.teachers_test.school.school-input');
    }
    public static function postSchool()
    {
        $input = \Request::all();
            $file = $input['school_image'];
            $extension = $file->getClientOriginalExtension();
            $originalName= $file->getClientOriginalName();
            $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
            $file = \Image::make($file);
            $success = $file->resize(350,null, function ($constraint)
            {
                $constraint->aspectRatio();

            })->save('school/' . $filename);

            if($success)
            {

                $result=DB::table('bio_schools')->insert(
            array(
            
                    'school_name' => $input['school_name'],
                    'email' => $input['school_email'],
                    'mobile' => $input['school_mobile'],
                    'address' => $input['school_address'],
                    'city' => $input['school_city'],
                    'image' => 'school/'.$filename
            
            
                    ));
        }
         $msg['success'] = 'Schools inserted successfully';
        return \Redirect::back()->withInput($msg);
        
    }
     public function viewquestionlist() {
        $input = \Request::all();
       
       $question = \DB::table('bio_qutions')
                        ->where('type',$input['type'])->get();

        $type=$input['type'];
                $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
            $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
             return view('users.teachers_test.viewQuestion', compact('question','school','session','type'));  

    }
    public function deleteQuestions($id,$type) {
        //dd($id,$type);
        try {
            // delete question and answers
            DB::table('bio_qutions')->delete($id);
            DB::table('bio_options')->where('question_id', $id)->delete();

            $success = true;
            $message = "Successfully Deleted";

        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }
        $teaching_type=$type;
        //dd($id,$teaching_type,$teaching_type);
        // return response
        if ($success)
            return redirect()->route('questions', $teaching_type)->with('success', $message);
        else
            return redirect()->route('questions', $teaching_type)->with('error', $message);
    }

    public function democlass_checklistview($id) {
        
        $biodata = DB::table('biodata')->where('id', $id)->first();
         $democlassview = DB::table('bio_democlass_chklst')->where('staff_id', $id)->get();
         foreach ($democlassview as $key => $value) {
            
            $demo_chklst=json_decode($value->demo_chklst);
            $marks=json_decode($value->chklst_marks);
            $checklist2=json_decode($value->chklst);
            $chklst_val=json_decode($value->chklst_val);
            $total_marks=$value->total_marks;
            $remarks=$value->remarks;
            }
             
        return view('users.teachers_test.democlass_checklistview',compact('remarks','checklist2','chklst_val','check_list','check_list1','biodata','democlassview','demo_chklst','marks','total_marks'));
        
    }
    
    public function viewstaff_profile($id) {
        //dd('hhhhhhhh');
        
        $biodata = DB::table('biodata')->where('school_id', Auth::user()->school_id)
                ->where('session_id', $this->active_session->id)
                ->where('id', $id)
                ->first();
        $bioqualify = DB::table('bio_qualification')->where('school_id', Auth::user()->school_id)
                ->where('session_id', $this->active_session->id)
                ->where('staff_id', $id)
                ->get();
        $qualify = DB::table('bio_qualification')->where('school_id', Auth::user()->school_id)
                ->where('session_id', $this->active_session->id)
                ->where('staff_id', $id)
                ->first();
        $bioexp = DB::table('bio_exp')->where('school_id', Auth::user()->school_id)
                ->where('session_id', $this->active_session->id)
                ->where('staff_id', $id)
                ->get();
        $bioexps = DB::table('bio_exp')->where('school_id', Auth::user()->school_id)
                ->where('session_id', $this->active_session->id)
                ->where('staff_id', $id)
                ->first();
                //dd($biodata,$bioqualify,$bioexp);
                
        return view('users.teachers_test.viewstaffProfile',compact('biodata','bioqualify','bioexp','qualify','bioexps'));
        
    }
    public function postDemoclasschecklist($id) {
        $input = \Request::all();
        //dd($input);
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
        foreach ($input['fooby'] as $key => $value1) {
            foreach ($value1 as $key => $value) {
               $chk_marks[]=$value;
            }
           
        }
        $check_list=["Indroduction","Motivation","Communicative English","Subject Knowledge","Board Work","Body Language","Class Room Management","Confidence Level","Audibility Of Teaching"];
        $check_list1=["Spelling Mistakes","Pronunciation Mistakes"];
        
        foreach ($input['list2'] as $key => $value1) {
            foreach ($value1 as $key => $value) {
               $list2[]=$value;
            }
           
        }
        $total_marks=array_sum($chk_marks);
        //dd($input,'hhhhhhhhhh',$chk_marks,json_encode($chk_marks),$total_marks,$check_list2[0],$checklist2,json_encode($check_list),json_encode($check_list1),json_encode($list2));

            //dd($input,$foopy,$input['check_list'],$list2,$input['check_list2']);
            
            $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
         
        $result=DB::table('bio_democlass_chklst')->insert(
            array(
            
            'school_id' =>$school->id,
            'session_id' => $session->id,
            'staff_id' =>$id,
            'demo_chklst' => json_encode($check_list),
            'chklst_marks' =>json_encode($chk_marks),
            'total_marks' =>$total_marks,
            'chklst' => json_encode($check_list1),
             'chklst_val' => json_encode($list2),
            'remarks' =>$input['remarks']
            
                    ));
       
        $msg['success'] = 'Success to Submit Demo Class Check List';
       // dd('inserted');
       return \Redirect::back()->withInput($msg);
        
    }
    public function personal_interview($id) {
        //dd('hhhhhhhh');
        
        $check_list=["Attitude","Flexibility","Faithfullness","Dedication","Readness"];
        $check_list1=["Money Minded / Greedy","Over Confidence","Decision Making"];
        $biodata = DB::table('biodata')->where('id', $id)->first();
        return view('users.teachers_test.personal_interview',compact('check_list','check_list1','biodata'));
        
    }
    public function postpersonalinterview($id) {
        $input = \Request::all();
        
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
            
            $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
        foreach ($input['fooby'] as $key => $value1) {
            foreach ($value1 as $key => $value) {
               $chk_marks[]=$value;
            }
           
        }

        foreach ($input['list2'] as $key => $value1) {
            foreach ($value1 as $key => $value) {
               $list2[]=$value;
            }
           
        }
        $total_marks=array_sum($chk_marks);
        // dd($input,'hhhhhhhhhh',json_encode($input['check_list2']),json_encode($input['check_list']),json_encode($chk_marks),json_encode($list2));
         //foreach ($input['check_list'] as $key => $value)  {
        $result=DB::table('bio_personal_interview')->insert(
            array(
            
            'school_id' =>$school->id ,
            'session_id' => $session->id,
            'staff_id' =>$id,
            'pers_chklst' => json_encode($input['check_list']),
            'per_chklst_marks' =>json_encode($chk_marks),
            'chklst' => json_encode($input['check_list2']),
             'chklst_valu' => json_encode($list2),
             'total_marks' => $total_marks
            
            
                    ));
       // }
        $msg['success'] = 'Success to Submit Personal Interviw Check List';
        return \Redirect::back()->withInput($msg);
    }
    public function personal_interviewview($id) {
        $input = \Request::all();
        
        $biodata = DB::table('biodata')->where('id', $id)->first();
         $personalview = DB::table('bio_personal_interview')->where('staff_id', $id)->get();
         $check_list=["Attitude","Flexibility","Faithfullness","Dedication","Readness"];
        $check_list1=["Money Minded or Greedy","Over Confidence","Decision Making"];
         foreach ($personalview as $key => $value) {
            $per_chklst_marks=json_decode($value->per_chklst_marks);
            
            $chklst_valu=json_decode($value->chklst_valu);
            $total_marks=$value->total_marks;
            
            }
            
        return view('users.teachers_test.personal_interviewview',compact('total_marks','check_list','per_chklst_marks','biodata','check_list1','chklst_valu'));
        
    }
    public function getselectedstaff($id) {
       $biodataupdate = DB::table('biodata')->where('id', $id);
        $biodata_status = $biodataupdate->update([
                                    'staff_status' => 'Selected'
                                ]);
        
        $biodata = DB::table('biodata')->where('school_id', Auth::user()->school_id)
                ->where('session_id', $this->active_session->id)
                ->where('id', $id)->first();
         $bioschool= DB::table('bio_schools')->get();
         
        return view('users.teachers_test.selected_staff',compact('biodata','bioschool'));
        
    }
    public function postSelectedStaff() {
        $input = \Request::all();
        //dd($input,'hhhhhhhhhh');
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
        $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
        $result=DB::table('bio_selected')->insert(
            array(
            
            'school_id' =>$school->id ,
            'session_id' => $session->id,
            'staff_id' =>$input['staff_id'],
            'school_name' => $input['school'],
            'doj' =>$input['date'],
            'grade' =>$input['grade'],
            'condition' =>$input['add_condition'],
            'designation' => $input['designation'],
             'period' => $input['period'],
             'salary' => $input['salary'],
            
            
                    ));
       
        $msg['success'] = 'Selected Staff Details inserted successfully';
        return \Redirect::back()->withInput($msg);
        
    }
    public function getapprovedlist() {
        $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->select('biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                //->where('staff_id', $id)
                ->get();
        return view('users.teachers_test.approvedlist', compact('bio_selected'));
        
    }
    public function staffappointmentissuelist($id) {

        //dd($id);
        $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->select('biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                ->where('bio_selected.id', $id)
                ->first();
            //dd($bio_selected->name);
        return view('users.teachers_test.addstaffappointment', compact('bio_selected'));
        
    }

    public function acknowledgementDownload($id) {

        $bio_selected1 = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->join('bio_docu_upload', 'bio_selected.staff_id', '=', 'bio_docu_upload.staff_id')
                ->select('bio_docu_upload.*','biodata.address','biodata.pin_code','bio_selected.created_at','biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                ->where('bio_selected.id', $id)
                ->get();
         $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->join('bio_docu_upload', 'bio_selected.staff_id', '=', 'bio_docu_upload.staff_id')
                ->select('bio_docu_upload.*','biodata.address','biodata.pin_code','bio_selected.created_at','biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                ->where('bio_selected.id', $id)
                ->first();
       // dd($bio_selected);
        $school=\DB::table('bio_schools')->where('school_name', $bio_selected->school_name)->first();
       return view('users.teachers_test.pdf.acknowledgement', compact('bio_selected','school','bio_selected1'));
    }
    public function appointmentDownload($id) {

        $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->join('bio_docu_upload', 'bio_selected.staff_id', '=', 'bio_docu_upload.staff_id')
                ->select('bio_docu_upload.*','biodata.address','biodata.pin_code','bio_selected.grade','bio_selected.condition','bio_selected.created_at','biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                ->where('bio_selected.id', $id)
                ->first();
         $school=\DB::table('bio_schools')->where('school_name', $bio_selected->school_name)->first();
               // dd($bio_selected,$school);
            if($bio_selected->designation =='Principal'){
                return view('users.teachers_test.pdf.Principalappointment', compact('bio_selected','school'));
            }elseif($bio_selected->designation =='XSEED Teacher'){
               return view('users.teachers_test.pdf.xseedappointment', compact('bio_selected','school'));
            }elseif($bio_selected->designation =='Mother Teacher'){
                return view('users.teachers_test.pdf.motherappointment', compact('bio_selected','school'));
            }elseif($bio_selected->designation =='Office Staff'){
                return view('users.teachers_test.pdf.officeappointment', compact('bio_selected','school'));
            }elseif($bio_selected->designation =='PG Language Staff'){
                return view('users.teachers_test.pdf.languageappointment', compact('bio_selected','school'));
            }else{
                return view('users.teachers_test.pdf.neetappointment', compact('bio_selected','school'));
            }
    }

    public function agreementDownload($id) {

        $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->join('bio_docu_upload', 'bio_selected.staff_id', '=', 'bio_docu_upload.staff_id')
                ->select('bio_docu_upload.*','biodata.address','biodata.pin_code','bio_selected.created_at','biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                ->where('bio_selected.id', $id)
                ->first();

       // $school=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
        $school=\DB::table('bio_schools')->where('school_name', $bio_selected->school_name)->first();
            
       return view('users.teachers_test.pdf.agreement', compact('bio_selected','school'));
    }
    public function staffdocumentUpload($id) {
        $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->join('biodata', 'bio_selected.staff_id', '=', 'biodata.id')
                ->select('biodata.name','bio_selected.id','bio_selected.school_name','bio_selected.doj','bio_selected.designation','bio_selected.period','bio_selected.salary')
                ->where('bio_selected.id', $id)
                ->first();
        return view('users.teachers_test.addstaffdocu', compact('bio_selected'));
        
    }
    public function postdocumentUpload(Request $request, $id) {
        
    $input = \Request::all();
    //dd($input);
    $degree=$input['degree'];
    $title=$input['title'];
    $certNo=$input['certNo'];
    $serNo=$input['serNo'];
    $issuedt=$input['issuedt'];
       if($input)
        {
           

             // Upload the multible images
        $images=array();
        if ($files=$request->file('images')) {
            foreach ($files as $file) {
                $name=$file->getClientOriginalName();
                $file->move('multipleuploads',$name);
                $images[]=$name;
            }
            
        }

            
            $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
            $session = Session::where('school_id', Auth::user()->school_id)->where('active', 1)
            ->first();
            $bio_selected = DB::table('bio_selected')->where('bio_selected.school_id', Auth::user()->school_id)
                ->where('bio_selected.session_id', $this->active_session->id)
                ->where('id', $id)
                ->first();
           // dd($input,$images,$bio_selected);
            foreach($input['degree'] as $key =>$value)
            {
            \DB::table('bio_docu_upload')->insert([
                    'school_id' => $school->id,
                    'session_id' => $session->id,
                    'staff_id' => $bio_selected->staff_id,
                    'school_name' => $bio_selected->school_name,
                    'degree' => $value,
                    'title'=> $title[$key],
                    'certNo' => $certNo[$key],
                    'serNo' => $serNo[$key],
                    'issuedt' => $issuedt[$key],
                    'multiple_img' => implode("|",$images),
                   // 'aadharimage' => $aadharimagefile,
                   // 'aadharpdf' => $aadharpdffile,
                   // 'panimage' => $panimagefile,
                   // 'panpdf' => $panpdffile,
                   // 'bankimage'=> $bankimagefile,
                   // 'bankpdf' => $bankpdffile,
                   // 'expimage' => $expimagefile,
                   // 'exppdf' => $exppdffile,
                ]);
        }
            $input['success'] = 'Staff Document saved successfully';
            return \Redirect::back()->withInput($input);
        }
    }

    public function getsugession($id) {
        $biodata = DB::table('biodata')->where('id', $id)->first();
        // to get total test marks
        $usr_answer = DB::table('bio_results')->where('teacher_id', $id)->first();
       
        $ct_Ans_Marks=$usr_answer->total_marks;
        //dd($ct_Ans_Marks,$exam_total_marks);
    //to get total demo class marks

        $demo = DB::table('bio_democlass_chklst')->where('staff_id', $id)->first();
            $toal_demoMarks=$demo->total_marks;
    //to get total personal int marks

        $personal = DB::table('bio_personal_interview')->where('staff_id', $id)->first();
            //$toal_personalMarks=0;
       // foreach ($personal as $key => $va) {
          // $toal_personalMarks+=$va->per_chklst_marks;
        //}
        $toal_personalMarks=$personal->total_marks;

        $total_Score=$toal_personalMarks + $toal_demoMarks + $ct_Ans_Marks;
       // dd($ct_Ans_Marks,$toal_demoMarks,$toal_personalMarks,$demo);
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
        
        //dd($userAns,$bioAns,$correctAns,$wrongAns,$ct_Ans_Marks,$demo,$toal_demoMarks,$toal_personalMarks,$total_Score);
        return view('users.teachers_test.analyst_checklist',compact('exam_total_marks','school','biodata','ct_Ans_Marks','toal_demoMarks','toal_personalMarks','total_Score'));
    }
    public function waitingJob($id) {
        //dd("jjjjj",$id);
        $biodataupdate = DB::table('biodata')->where('id', $id);
        $biodata_status = $biodataupdate->update([
                                    'staff_status' => 'Waiting List'
                                ]);
        //dd("updated");
        $biodata = DB::table('biodata')->where('id', $id)->first();
        // to get total test marks
        $usr_answer = DB::table('bio_results')->where('teacher_id', $id)->first();
       
        $ct_Ans_Marks=$usr_answer->total_marks;
        //dd($ct_Ans_Marks,$exam_total_marks);
    //to get total demo class marks

        $demo = DB::table('bio_democlass_chklst')->where('staff_id', $id)->get();
            $toal_demoMarks=0;
        foreach ($demo as $key => $val) {
           $toal_demoMarks+=$val->chklst_marks;
        }
    //to get total personal int marks

        $personal = DB::table('bio_personal_interview')->where('staff_id', $id)->get();
            $toal_personalMarks=0;
        foreach ($personal as $key => $va) {
           $toal_personalMarks+=$va->per_chklst_marks;
        }

        $total_Score=$toal_personalMarks + $toal_demoMarks + $ct_Ans_Marks;
       // dd($ct_Ans_Marks,$toal_demoMarks,$toal_personalMarks,$demo);
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
        $rejectedmsg= " Staff Status Updated Successfully";
        //dd($userAns,$bioAns,$correctAns,$wrongAns,$ct_Ans_Marks,$demo,$toal_demoMarks,$toal_personalMarks,$total_Score);
        return view('users.teachers_test.analyst_checklist',compact('rejectedmsg','exam_total_marks','school','biodata','ct_Ans_Marks','toal_demoMarks','toal_personalMarks','total_Score'));
    }

    public function rejectedJob($id) {
        //dd("jjjjj",$id);
        $biodataupdate = DB::table('biodata')->where('id', $id);
        $biodata_status = $biodataupdate->update([
                                    'staff_status' => 'Rejected'
                                ]);
        //dd("updated");
        $biodata = DB::table('biodata')->where('id', $id)->first();
        // to get total test marks
        $usr_answer = DB::table('bio_results')->where('teacher_id', $id)->first();
       
        $ct_Ans_Marks=$usr_answer->total_marks;
        //dd($ct_Ans_Marks,$exam_total_marks);
    //to get total demo class marks

        $demo = DB::table('bio_democlass_chklst')->where('staff_id', $id)->get();
            $toal_demoMarks=0;
        foreach ($demo as $key => $val) {
           $toal_demoMarks+=$val->chklst_marks;
        }
    //to get total personal int marks

        $personal = DB::table('bio_personal_interview')->where('staff_id', $id)->get();
            $toal_personalMarks=0;
        foreach ($personal as $key => $va) {
           $toal_personalMarks+=$va->per_chklst_marks;
        }

        $total_Score=$toal_personalMarks + $toal_demoMarks + $ct_Ans_Marks;
       // dd($ct_Ans_Marks,$toal_demoMarks,$toal_personalMarks,$demo);
        $school = \DB::table('school')
            ->where('id', \Auth::user()->school_id)
            ->first();
        $rejectedmsg= "Staff Status Updated Successfully";
        //dd($userAns,$bioAns,$correctAns,$wrongAns,$ct_Ans_Marks,$demo,$toal_demoMarks,$toal_personalMarks,$total_Score);
        return view('users.teachers_test.analyst_checklist',compact('rejectedmsg','exam_total_marks','school','biodata','ct_Ans_Marks','toal_demoMarks','toal_personalMarks','total_Score'));
    }
    public function joboppournityschool() {
        return view('corres.biologin');
    }

    public function joboppournity() {
        $input = \Request::all();
        $username=$input['username'];
        $password=$input['password'];
        //dd($username,$password);
        return view('corres.stepone.createBiodata', compact('username', 'password'));
    }
    public function postBiodata() {
        $input = \Request::all();
        //dd($input);
        $name=$input['name'];
        $contact_no=$input['contact_no'];
        $gender=$input['gender'];
        $email=$input['email'];
        $whatsapp_no=$input['whatsapp_no'];
        $facebook_id=$input['facebook_id'];
        $instagram=$input['instagram'];
        $religion=$input['religion'];
        $caste=$input['caste'];
        $blood_group=$input['blood_group'];
        $father_name=$input['father_name'];
        $father_contact_no=$input['father_contact_no'];
        $mother_name=$input['mother_name'];
        $mother_contact_no=$input['mother_contact_no'];
        $address=$input['address'];
        $pin_no=$input['pin_no'];
        $dob=$input['dob'];
        $age=$input['age'];
        $maried_status=$input['maried_status'];
        $spouse_name=$input['spouse_name'];
        $spouse_contact_no=$input['spouse_contact_no'];
        $lang_known=$input['lang_known'];

        $qualification=$input['qualification'];
        $course_name  =$input['course_name'];
        $institute_name=$input['institute_name'];
        $passed_yr=$input['passed_yr'];
        $univ_board=$input['univ_board'];
        $scored=$input['scored'];

        $company_name=$input['company_name'];
        $from=$input['from'];
        $to=$input['to'];
        $tenure=$input['tenure'];
        $salary=$input['salary'];
        $exp_type=$input['yesno'];
        $company_contact=$input['contact'];

        $username=$input['username'];
        $password=$input['password'];

        
        $school=DB::table('school')
        //->leftJoin('users', 'school.user_id', '=', 'users.id')
        ->where('email',$username)
        ->where('mobile',$password)
        ->first();
        $check_Contact=DB::table('biodata')->where('school_id', $school->id)
                ->where('session_id', $this->active_session->id)
                ->where('contact_no',$contact_no)
                ->first();
        //dd($input,$school,$username,$password);
        $session = DB::table('session')->where('school_id', $school->id)
                ->where('active',"1")
                ->first();
       // dd($check_Contact);
        if(empty($check_Contact)){

        DB::table('biodata')->insert(
                array(
                'school_id' =>$school->id ,
                'session_id' => $session->id,
                'name' => $name,
                'contact_no' =>$contact_no,
                'gender'=>$gender,
                'email' =>$email,
                'whatsapp_no' =>$whatsapp_no,
                'facebook_id' =>$facebook_id,
                'instagram'=>$instagram,
                'religion'=>$religion,
                'caste_id'=>$caste,
                'blood_group' => $blood_group,
                'f_name' => $father_name,
                'f_contact_no'=>$father_contact_no,
                'm_name' =>$mother_name,
                'm_contact_no' =>$mother_contact_no,
                'address' =>$address,
                'pin_code'=>$pin_no,
                'dob'=>$dob,
                'age'=>$age,
                'maried_sta' => $maried_status,
                'spouse_name' => $spouse_name,
                'spouse_contact_no'=>$spouse_contact_no,
                'language_known' =>$lang_known
                
                ));

$staff_id = DB::table('biodata')->where('school_id', $school->id)
                ->where('session_id', $session->id)->max('id');
                //->first();
               // dd($biodataid);

        foreach($course_name as $key =>$value)
            {
        $result=DB::table('bio_qualification')->insert(
            array(
            
            'school_id' =>$school->id ,
            'session_id' => $session->id,
            'staff_id' =>$staff_id,
            'course_name' => $course_name[$key],
            'institute_name' =>$institute_name[$key],
            'year_passed' => $passed_yr[$key],
            'univer_board' => $univ_board[$key],
            'marks_percent' => $scored[$key],
            'qualify' => $qualification,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
                    ));
        }


        foreach($company_name as $key =>$val)
            {
        $result=DB::table('bio_exp')->insert(
            array(
            
            'school_id' =>$school->id ,
            'session_id' => $session->id,
            'staff_id' =>$staff_id,
            'institute_name' => $company_name[$key],
            'from_dt' =>$from[$key],
            'to_dt' => $to[$key],
            'tenure' => $tenure[$key],
            'salary' => $salary[$key],
            'type' => $exp_type,
            'comp_contact_no' => $company_contact[$key],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
                    ));
        }

        //dd('job',$input);
        $msg = 'Biodata is added successfully';
    }else{
        $msg1 = 'Already Exist Contact No';
    }
        //return \Redirect::back()->withInput($msg);
        return view('corres.stepone.createBiodata', compact('msg1','msg','username','password'));
    }
    
     public function teachersQuestion() {
         $type = \Request::get('type');
        return view('users.teachers_test.input', compact('type'));
    }
     public function postTeachersQuestion() {
        $input = \Request::all();
        $question_name =$input['question_name'];
        $teaching_type=$input['type'];
        $option_1 =$input['option_1'];
        $option_2 =$input['option_2'];
        $option_3 =$input['option_3'];
        $option_4 =$input['option_4'];
        $correct_option =$input['correct_option'];
        try {
            // add question
            $question_id = DB::table('bio_qutions')->insertGetId([
                'type' => $teaching_type,
                'question' => $question_name,
            ]);

            // add options
            DB::table('bio_options')->insert([
                'question_id' => $question_id,
                'option_1' => $option_1,
                'option_2' => $option_2,
                'option_3' => $option_3,
                'option_4' => $option_4,
                'correct_option' => $correct_option,
            ]);

            $success = true;
            $message = "Successfully Inserted";

        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $message = $ex->getMessage();
        }
//dd($message);
        // return response
        if ($success)
            return redirect()->route('questions', $teaching_type)->with('success', $message);
        else
            return redirect()->route('questions', $teaching_type)->with('error', $message);
        
    }
    public function index($teaching_type)
    {
        $pageTitle = \DB::table('bio_qutions')
        ->where('type', $teaching_type)->get();
        return view('users.teachers_test.index', compact('pageTitle', 'teaching_type'));
    }
    public function teachersOnlinetest() {
        
        $questions = \DB::table('bio_qutions')
        ->where('school_id', $this->user->school_id)
        ->where('session_id', $this->active_session->id)->get();
        return view('users.teachers_test.select_teachers_test', compact('questions'));
    }
    public function teachersQViewOnlinetest($type)
    {
        $pageTitle = 'Do Quiz';
        //dd($pageTitle);
        return view('users.teachers_test.questions', compact('pageTitle', 'type'));
    }
    public function viewResult()
    {
        $pageTitle = 'View Result';
        return view('users.teachers_test.view-result', compact('pageTitle'));
    }
    public function interview_test_result($id)
    {
        $input = \Request::all();
        $staff_id = \DB::table('biodata')
            ->where('id','=',$id)
            ->first();

        $teacher_id = $staff_id->id;
        //duplicate quiz restrict
        $check_quiz = DB::table('bio_results')->where('teacher_id', $teacher_id)->first();
       $teacher_type = $check_quiz->teacher_type;
       //dd($teacher_type);
        if($check_quiz!=null){
            $total_marks=$check_quiz->total_marks;
            $user_selected_answers=json_decode($check_quiz->answers);
        //dd($user_selected_answers,$total_marks,$teacher_type);
            return Redirect::route('view-result')->with(['teacher_type' => $teacher_type, 'total_marks' => $total_marks, 'user_selected_answers' => $user_selected_answers]);

        }
    }
    public function postteachersOnlinetest(Request $request) {
        $input = \Request::all();
        $staff_id = \DB::table('biodata')
            ->where('contact_no','=',$input['mobile_no'])
            ->first();

        $teacher_id = $staff_id->id;
        $teacher_type = $input['type'];
        $per_right_answer_marks = 2;
        $total_marks = 0;

        //duplicate quiz restrict
        $check_quiz = DB::table('bio_results')->where('teacher_id', $teacher_id)->where('teacher_type', $teacher_type)
            ->first();
        //dd($check_quiz);
        if($check_quiz!=null){
            return redirect()->route('home')->with('error', 'Already Done quiz');
        }else{
            $user_selected_answers = array();
            $questions = DB::table('bio_qutions')->where('type', $teacher_type)->get();
            foreach ($questions as $question) {
                $user_selected_answer = $request->input('question_' . $question->id);
                array_push($user_selected_answers, $user_selected_answer);
            }

            $counter = 0;
            foreach ($questions as $question) {
                $options = DB::table('bio_options')->where('question_id', $question->id)->get();
                foreach ($options as $option) {

                    if ($user_selected_answers[$counter] == $option->correct_option) {
                        $total_marks += $per_right_answer_marks;
                    }
                }
                echo "<br>";

                $counter++;
            }

            // store to db
            DB::table('bio_results')->insert([
                'teacher_id' => $teacher_id,
                'teacher_type' => $teacher_type,
                'total_marks' => $total_marks,
                'answers' => json_encode($user_selected_answers),
                //'submission_time' => $total_marks,
            ]);
            return Redirect::route('view-result')->with(['teacher_type' => $teacher_type, 'total_marks' => $total_marks, 'user_selected_answers' => $user_selected_answers]);
        }
}
}
