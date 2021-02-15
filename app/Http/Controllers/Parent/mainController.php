<?php

namespace App\Http\Controllers\Parent;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Leave;
use App\Http\Controllers\Controller;

class mainController extends Controller
{
    public function dashboard()
    {
    	$today = Date('d-m-Y');
        $totalLeave = '';
        $todayAttenace ='';
        $todayHomework ='';
        $lastLeaveRequest = '';       
    	return view('parent.index', compact('totalLeave', 'todayAttenace','todayHomework', 'lastLeaveRequest'));
    }

    public function schoolProfile(){
        $school = \DB::table('school')->where('id', \Auth::user()->school_id)->first();
		return view('parent.resources.schoolProfile', compact('school'));    	
    }

    public function resourceEmployee(){
        $employee = \DB::table('teacher')->where('school_id', \Auth::user()->school_id)->get();
		return view('parent.resources.employee', compact('employee'));    	
    }

    public function gallery(){
        $gallery = \DB::table('gallery_img')->join('gallery', 'gallery_img.gallery_id', '=', 'gallery.id')->where('gallery.school_id', \Auth::user()->school_id)->get();
		return view('parent.gallery.index', compact('gallery'));    	
    }

    public function homeWork()
    {
    	$student_id = \Request::get('student');
    	$today = Date('d-m-Y');
    	$get = \DB::table('parent')->where('user_id', \Auth::user()->id)->first();
    	$students = \DB::table('student')->where('parent_id', $get->id)->get();
    	$homework = '';
    	if($student_id != null)
    	{
    		$stdnt = \DB::table('student')->where('id', $student_id)->first();
    		$homework = \DB::table('homework')->where('homework.school_id', \Auth::user()->school_id)->where('homework.class_id', $stdnt->class_id)->where('homework.date', $today)
    		->join('subject', 'homework.subject_id', '=', 'subject.id')->get();
    	}
    	return view('parent.homework.homework', compact('students', 'homework'));
    }

    public function leaveRequest(){
        $get = \DB::table('parent')->where('user_id', \Auth::user()->id)->first();
    	$students = \DB::table('student')->where('parent_id', $get->id)->get();
    	$leavesReq = [];
    	foreach($students as $student)
    	{
        	$leaves = \DB::table('leave_request')
        			->where('leave_request.student_id', $student->id)
        	->join('student', 'leave_request.student_id', '=', 'student.id')
        	->orderBy('leave_request.id', 'DESC')->get();
        	array_push($leavesReq, $leaves);
        }
		return view('parent.leaveapprove.index', compact('leaves', 'students','leavesReq'));
    }

    public function leavePost(Leave $post)
    {
        $input = \Request::all();
        $userError = ['from' => 'Leave From Date in dd-mm-yyyy', 'to' => 'Leave To Date in dd-mm-yyyy', 'message'=>'Message'];
        $validator = \Validator::make($input, [
                'from'=>'required|date',
                'to'=>'required|date',
                'message'=>'required',
                'student'=>'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return Redirect::back()->withErrors($validator);
        return $post->ParentLeavePost($input);
    }

}
