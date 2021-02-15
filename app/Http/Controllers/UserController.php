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
use App\GalleryImages;
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
use App\UserNotification;

class UserController extends Controller
{
    protected $user;
    
    function __construct(){
        try{
            $this->user= JWTAuth::parseToken()->authenticate();
        }
        catch(\Exception$e){}
    }

    public function authenticate(Request $request)
    {
    	    // grab credentials from the request
        $credentials = $request->only('username', 'password');
        $validator = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails())
            return api()->notValid(['message'=>$validator->errors()->first()]);   
        try 
        {
            if (! $token = JWTAuth::attempt($credentials))
                return api()->notValid(['message'=>'Invalid credentials']);
        } 
        catch (JWTException $e) 
        {
            return api()->notValid(['message'=>'Something went wrong']);
        }
       
        if(\Auth::user()->type == 'school')
        {
            $school = \DB::table('school')->where('user_id', \Auth::user()->id)->first();
            return api()->success(['token'=> $token, 'id'=> \Auth::user()->id, 'name' => $school->school_name, 'primary_id' => $school->id, 'role' => \Auth::user()->type]);
        }

        if(\Auth::user()->type == 'student')
        {
        	return api()->success(['token'=> $token, 'id'=> \Auth::user()->id, 'name' => $data->name, 'primary_id' => $data->id, 'role' => \Auth::user()->type]);
        }
        if(\Auth::user()->type == 'teacher')
        {
        	$teacher = \DB::table('teacher')->where('user_id', \Auth::user()->id)->first();

            \Auth::user()->class_id = $teacher->class;
            \Auth::user()->section_id = $teacher->section;
        	return api()->success(['token'=> $token, 'id'=> \Auth::user()->id, 'teacher_orig_id' => $teacher->id, 'name' => $teacher->name, 'email' => $teacher->email, 'mobile' => $teacher->mobile, 'class_id' => $teacher->class,
        	'section_id' => $teacher->section, 'role' => \Auth::user()->type]);
        }
        return api()->success(['token'=> $token, 'id'=> \Auth::user()->id, 'name' => $data->name, 'primary_id' => $data->id, 'email' => $data->email, 'mobile' => $data->mobile, 'role' => \Auth::user()->type]);
    }

    public function logout(){
        \JWTAuth::parseToken()->invalidate();
       return api()->success(['data' => 'You have logged out successfully']);
    }

    public function changePassPost(Request $request, User $user)
    {
        $userError = ['old_pass' => 'Old Password', 'new_pass' => 'New Password'];
        $validator = \Validator::make($request->all(),[
                'old_pass'=>'required',
                'new_pass'=>'required|min:6'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return \api::notValid(['errorMsg' => $validator->errors()->first()]);
        return $user->doChangePassPost($request, $this->user);
    }

    public function getAllDetails(School $school)
    {
    	return $school->doGetAllDetails($this->user);
    }
    
    public function getSchoolProfile(School $school)
    {
    	return $school->doGetSchoolProfile($this->user);
    }
    
    public function getUserProfile(User $user)
    {
        return $user->doGetUserProfile($this->user);
    }

    public function freeSearch(Request $request, $platform)
    {
        $search = School::search($request['search'])->get();
        return \api::success(['data' => $search]);
    }

    public function getSchool(School $school, $platform, $id)
    {
        return $school->doGetSchool($platform, $id);
    }
    
    public function getSchoolsList()
    {
    	$schools = School::all();
    	return \api(['data' => $schools]);
    }
     public function getVersonUpdate(){
        $version=\DB::table('version')->where('id','!=','')->select('version')->first();

        return \api::success(['data' =>$version]);
    }

    public function shareApp()
    {
        return \Redirect::away("https://play.google.com/store/apps/details?id=com.shine.school");
    }

    public function postDevice()
    {
        $request = \Request::all();
        $userError = ['device_id' => 'Device Id', 'role_id' => 'Role Id', 'role' => 'Role'];
        $validator = \Validator::make($request, [
                'device_id'=>'required',
                'role_id'=>'required|numeric',
                'role'=>'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }
        else
        {
            $check = \DB::table('push_notification')->where('role_id', $request['role_id'])
                    ->where('role', $request['role'])
                    ->first();
            if($check)
            {
                $device_id = \DB::table('push_notification')->where('role_id', $request['role_id'])
                            ->where('role', $request['role'])
                            ->where('device_id', $request['device_id'])
                            ->first();
                if($device_id)
                {

                }
                else
                {
                    \DB::table('push_notification')->where('id', $check->id)->update(['is_live' => 1]);
                    
                    \DB::table('push_notification')->insert(['device_id' => $request['device_id'], 'role_id' => $request['role_id'], 'role' => $request['role']]);
                }
                return api(['data' => 'Device Id is updated successfully']);
            }
            else
            {
                \DB::table('push_notification')->insert(['device_id' => $request['device_id'], 'role_id' => $request['role_id'], 'role' => $request['role']]);
                return api(['data' => 'Device Id is added successfully']);
            }
        }
    }

    public function getGallery(Gallery $gallery, $platform)
    {
        $galleries = Gallery::with('hasManyImages')->where('gallery.school_id', $this->user->school_id)->get();
        return api(['data' => $galleries]);
    }

    public function getNotification()
    {
        if($this->user->type == 'student')
        {
            $man = Students::where('user_id', $this->user->id)->first();
        }
        if($this->user->type == 'parent')
        {
            $man = StuParent::where('user_id', $this->user->id)->first();
        }
        if($this->user->type == 'teacher')
        {
            $man = Employee::where('user_id', $this->user->id)->first();
        }
        // dd($man->id, $this->user->type);
        // $notifications = \DB::table('push_notification')->where('role_id', $man->id)->where('role', $this->user->type)->select('title', 'description', 'image')->get();
        // return api(['data' => $notifications]);


        // if($this->user->type == 'student')
        // {
        //     $man = Students::where('user_id', $this->user->id)->first();
        //     $type = 'student';
        // }
        // if($this->user->type == 'parent')
        // {
        //     $man = StuParent::where('user_id', $this->user->id)->first();
        //     $type = 'parent';
        // }
        // if($this->user->type == 'teacher')
        // {
        //     $man = Employee::where('user_id', $this->user->id)->first();
        //     $type = 'teacher';
        // }
        // dd($man->id, $this->user->type);
        $notifications = \DB::table('notification_history')
                            ->leftJoin('notification_type', 'notification_history.notification_type_id', '=', 'notification_type.id')
                            ->select
                            (
                                'notification_type.title',
                                'notification_type.description',
                                'notification_history.role'
                            )
                            ->where('notification_history.role_id', $man->id)
                            ->where('notification_history.role', $this->user->type)
                            ->where('notification_history.message_type', 'push_msg')
                            ->get();
        return api(['data' => $notifications]);

    }
    
    public function getClass()
    {
    	$classes = addClass::where('school_id', $this->user->school_id)->get();
    	return api(['data' => $classes]);
    }

     public function getClasstid($flag,$id){//by mari 28.09.2017
        
        
        $class= TimeTable::where('teacher_id', $id)
                ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                ->join('class', 'time-table.class_id', '=', 'class.id')
                ->join('section', 'time-table.section_id', '=', 'section.id')
                ->select('class.id','class.class')
                ->groupBy('class.id')
                ->get();

       
        if(empty($class)){
            $class= "TimeTablenot assinged";
        }
        return api(['data' =>$class]);
    }
    public function getSectionmark($flag, $teacher_id, $class_id)
    {
        $section= TimeTable::where('teacher_id', $teacher_id)
                ->where('time-table.class_id', $class_id)
                ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                ->join('class', 'time-table.class_id', '=', 'class.id')
                ->join('section', 'time-table.section_id', '=', 'section.id')
                ->select('section.id','section.section')
                ->groupBy('section.id')
                ->get();
                 if(empty($section)){
            $section= "TimeTablenot assinged";
        }
        return api(['data' =>$section]);


    }
    
    public function getSection($flag, $id)
    {
        $section = \DB::table('section')->where('school_id', $this->user->school_id)->where('class_id', $id)->get();
        if(!$section)
            return api()->notFound(['errorMsg'=>'not enough section']);
        return api(['data'=> $section]);
    }
    
    public function getFeeStructure()
    {
    	$frequencies = \DB::table('fee_frequency')->where('school_id', $this->user->school_id)->get();
    	$structures = \DB::table('fee_structure')->where('school_id', $this->user->school_id)->get();
        $amounts = \DB::table('amount')->where('amount.school_id', $this->user->school_id)
                                ->leftJoin('class', 'amount.class_id', '=', 'class.id')
                                ->select('class.class', 'amount.amount')->get();
    	return api(['frequencies' => $frequencies, 'structures' => $structures, 'amounts' => $amounts]);
    }
    
    public function getLatestEvent()
    {
    	$gallery = Gallery::where('school_id', $this->user->school_id)->orderBy('id', 'DESC')->first();
    	$images = GalleryImages::where('gallery_id', $gallery->id)->take(5)->get();
    	return \api(['data' => $images]);
    }

    public function editProfile()
    {
        $request = \Request::all();
        $userError = ['type' => 'Type', 'id' => 'Id', 'image' => 'Image'];
        $validator = \Validator::make($request, [
                'type' => 'required',
                'id' => 'required|numeric',
                'image' => 'image',
                'email' => 'email'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \api::notValid(['errorMsg'=>$validator->errors()->first()]);
        }

        if($request['type'] == '')
        {
            return api::notValid(['errorMsg' => 'User Type is required']);
        }
        if($request['type'] == 'teacher')
        {
            $teacher = \DB::table('teacher')->where('id', $request['id'])->first();
            $request['name'] = (isset($request['name']) ? $request['name'] : $teacher->name);
            $request['mobile'] = (isset($request['mobile']) ? $request['mobile'] : $teacher->mobile);
            $request['email'] = (isset($request['email']) ? $request['email'] : $teacher->email);
            // dd($request);
            if(isset($request['image']))
            {
                $directory = 'employee';
                $image = $request['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName= $image->getClientOriginalName();
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
                $imagefile = $teacher->avatar;
            }

            \DB::table('teacher')->where('id', $request['id'])->update([
                    'name' => $request['name'],
                    'mobile' => $request['mobile'],
                    'email' => $request['email'],
                    'avatar' => $imagefile
            ]);

            return api(['data' => 'Teacher Profile is updated successfully']);
        }

        if($request['type'] == 'parent')
        {
            $parent = \DB::table('parent')->where('id', $request['id'])->first();
            $request['name'] = (isset($request['name']) ? $request['name'] : $parent->name);
            $request['mobile'] = (isset($request['mobile']) ? $request['mobile'] : $parent->mobile);
            $request['email'] = (isset($request['email']) ? $request['email'] : $parent->email);
            if(isset($request['image']))
            {
                $directory = 'parent';
                $image = $request['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName= $image->getClientOriginalName();
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
                $imagefile = $parent->avatar;
            }

            \DB::table('parent')->where('id', $request['id'])->update([
                    'name' => $request['name'],
                    'mobile' => $request['mobile'],
                    'email' => $request['email'],
                    'avatar' => $imagefile
            ]);

            return api(['data' => 'Parent Profile is updated successfully']);
        }
    }

    public function appHelp()
    {
        $help = \DB::table('help')->first();
        return \api(['data' => $help]);
    }

    public function getFeeStructureStudent($flag, $id)
    {
        /*$get = \DB::table('student')->where('student.id', $id)->where('student.school_id', $this->user->school_id)
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('amount', 'class.id', '=', 'amount.class_id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')
                ->select('student.name', 'student.roll_no', 'class.class', 'section.section', 'amount.amount as total_amount', 'student.registration_no')
                ->first();
        $get->total_amount = empty($get->total_amount)?0:$get->total_amount;
        $reg_no = $get->registration_no;
        $transport_fee = \DB::table('bus_stop')->where('school_id', $this->user->school_id)->where('id', $get->bus_stop_id)->first();
    
    $security = \DB::table('security_fee')
                ->where('school_id', $this->user->school_id)
                ->where('class_id', $get->class_id)
                ->first();
   $fee = \DB::table('fee_head')
    ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', \Auth::user()->school_id)->get();

    $monthfee = \DB::table('fee_head')
    ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', \Auth::user()->school_id)->where('fee_head.fee_head_type', 'month')->get();

    $annual_fee = \DB::table('fee_head')
    ->join('fee_head_amount', 'fee_head.id', '=', 'fee_head_amount.fee_head_id')->where('fee_head_amount.class_id', $get->class_id)->where('fee_head.school_id', \Auth::user()->school_id)->where('fee_head.fee_head_type', 'annual')->get();

    $month_total = 0;
    foreach($monthfee as $fe)
    {
            $month_total = $month_total+$fe->amount;
            $monthannual = $month_total*12;
    }
    $annualfee = 0;
    foreach($annual_fee as $fe)
    {
            $annualfee = $annualfee+$fe->amount;
    }
    
    $total_fee = $monthannual + $transport_fee->transport_fee + $annualfee;
    $totalPay = \DB::table('payment')->where('student_id', $get->id)->get();

    $countPay = 0;
    $total_discount = 0;
    foreach($totalPay as $pay)
    {
        $total_discount = $total_discount+(int)$pay->discount;
        $countPay = $countPay+(int)$pay->pay_amount;

    }
    $get->total_fee = empty($total_fee)?0:$total_fee;
    $get->pay = $countPay;
    $get->discount = $input['discount'];
    $get->total_discount =$total_discount;
    $get->annualfee = $annualfee;
    $get->month_total = $month_total;
    $get->transport_fee = $transport_fee;
    $get->security_fee = $security_fee;
        foreach($fee as $fe)
        {
            if($fe->fee_head_type == 'annual')
            {
                $fe->month_fee =$fe->amount;
            }
            else
            {
                $fe->month_fee = $fe->amount*12;                
            }
        }


        if(!$get)
        {
            return api()->notFound(['errorMsg'=>'Not enough Fee :(']);
        }
        else
        {
            return api(['data'=>$get]);
        }*/
        $result = array();
        $stu_info = \DB::table('student')->where('student.id', $id)->where('student.school_id', $this->user->school_id)
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')->first();
        $payied_info = \DB::table('payment')->where('student_id',$id)->get();

        $total_amt = 0;
        $tot_paid = 0;
        $balance_amt = 0;

        $fees = \DB::table('fee_structure')->where('school_id', $this->user->school_id)->where('class_id', $stu_info->class_id)->get(); 
        foreach ($fees as $key => $value) {
            $total_amt += $value->amount;
        }               
        foreach($payied_info as $key => $value){
            $tot_paid+=$value->amount;
        }
        $balance_amt = $total_amt - $tot_paid;
        $result['student']=$stu_info;
        $result['total_amt']=$total_amt;
        $result['tot_paid']=$tot_paid;
        $result['balance_amt']=$balance_amt;
        return api(['data'=>$result]);
    }
     public function getLeaveRequestsParentcount()
    {
        $requests = \DB::table('leave_request')->where('user_id', $this->user->id)->where('school_id', $this->user->school_id)->where('view_status','=','0')->where('status','!=','process')->
        orderBy('id', 'DESC')->get();
        if(!$requests)
            return api()->notFound(['errorMsg'=>'Not enough requests']);
        return api(['data'=>$requests]);
    }


    // public function getLeaveRequestsParent()
    // {
    //     $requests = \DB::table('leave_request')->where('user_id', $this->user->id)->where('school_id', $this->user->school_id)->orderBy('id', 'DESC')->get();
    //     if(!$requests)
    //         return api()->notFound(['errorMsg'=>'Not enough requests']);
    //     return api(['data'=>$requests]);
    // }
     public function getLeaveRequestsParent()
    {
        $requests = \DB::table('leave_request')->where('user_id', $this->user->id)->where('school_id', $this->user->school_id)->orderBy('id', 'DESC')->get();
        $update_leave_status=\DB::table('leave_request')->where('user_id','=', $this->user->id)->where('school_id','=',$this->user->school_id)->update(['view_status'=>1]);
        if(!$requests)
            return api()->notFound(['errorMsg'=>'Not enough requests']);
        return api(['data'=>$requests]);
    }
}