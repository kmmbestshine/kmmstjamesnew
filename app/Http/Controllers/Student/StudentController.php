<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Leave;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class StudentController extends Controller
{
    public function dashBorad()
    {
        $today = Date('d-m-Y');
        $totalLeave = \DB::table('leave_request')->where('student_id', \Auth::user()->id)->count();
        $todayAttenace = \DB::table('attendance')->where('student_id', \Auth::user()->id)->where('date', $today)->first();
        $class = \DB::table('student')->where('user_id', \Auth::user()->id)->first();
        $todayHomework = \DB::table('homework')
            ->where('homework.class_id', $class->class_id)
            ->where('homework.section_id', $class->section_id)->
            where('homework.date', $today)
            ->join('subject', 'homework.subject_id', '=', 'subject.id')->get();
        $lastLeaveRequest = \DB::table('leave_request')->take('5')->orderBy('id', 'DESC')->get();
        // dd($todayHomework);
    	return view('student.index', compact('totalLeave', 'todayAttenace','todayHomework', 'lastLeaveRequest'));
    }

    public function attendance(){
		return view('student.attendance.index');    	
    }

    public function homeWork(){
        $today = Date('d-m-Y');
        $student = \DB::table('student')->where('user_id', \Auth::user()->id)->first();
        $subject = \DB::table('subject')->where('class_id', $student->class_id)->where('section_id', $student->section_id)->get();
        $subject_id = \Request::get('subject');
        $homework = '';
        if($subject_id != null)
        {
            $homework = \DB::table('homework')->where('school_id', \Auth::user()->school_id)->where('class_id', $student->class_id)->where('subject_id', $subject_id)->where('date', $today)->first();
        }
		return view('student.homework.homework', compact('subject', 'homework','subject_id'));    	
    }

    public function leaveRequest(){
        $leaves = \DB::table('leave_request')->where('student_id', \Auth::user()->id)->orderBy('id', 'DESC')->get();
		return view('student.leaveapprove.index', compact('leaves'));    	
    }

    public function leavePost(Leave $post)
    {
        $input = \Request::all();
        $userError = ['from' => 'Leave From Date in dd-mm-yyyy', 'to' => 'Leave To Date in dd-mm-yyyy', 'message'=>'Message'];
        $validator = \Validator::make($input, [
                'from'=>'required|date',
                'to'=>'required|date',
                'message'=>'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
            return Redirect::back()->withErrors($validator);
        return $post->studentLeavePost($input);
    }

    public function schoolProfile(){
        $school = \DB::table('school')->where('id', \Auth::user()->school_id)->first();
		return view('student.resources.schoolProfile', compact('school'));    	
    }

    public function marks(){
        $student = \DB::table('student')->where('user_id', \Auth::user()->id)
            ->where('school_id', \Auth::user()->school_id)->first();
        $marks = \DB::table('result')->where('result.class_id', $student->class_id)
                ->where('result.section_id', $student->section_id)
                ->join('exam', 'result.exam_type_id', '=', 'exam.id')
                ->join('subject', 'result.subject_id', '=', 'subject.id')
                ->select('result.id', 'result.month', 'result.date', 'result.max_marks', 'pass_marks', 'obtained_marks', 'exam.exam_type', 'subject.subject')->get();
        // dd($marks);
    	return view('student.marks.index', compact('marks'));
    }

    public function resourceEmployee(){
        $employee = \DB::table('teacher')->where('school_id', \Auth::user()->school_id)->get();
		return view('student.resources.employee', compact('employee'));    	
    }

    public function timeTable(){
        $student = \DB::table('student')->where('user_id', \Auth::user()->id)->first();
        $time = \DB::table('subject')->where('subject.class_id', $student->class_id)->where('subject.section_id', $student->section_id)
        ->join('time-table', 'subject.id', '=', 'time-table.subject_id')
        ->join('teacher', 'time-table.teacher_id', '=', 'teacher.id')
        ->select('subject.id', 'subject.subject', 'time-table.period', 'time-table.start_time', 'time-table.end_time','time-table.day', 'teacher.name')->get();
		return view('student.resources.timetable', compact('time'));    	
    }

    public function feeStructure(){
		return view('student.resources.feestructure');    	
    }

    public function gallery(){
        $gallery = \DB::table('gallery_img')->join('gallery', 'gallery_img.gallery_id', '=', 'gallery.id')->where('gallery.school_id', \Auth::user()->school_id)->get();
		return view('student.gallery.index', compact('gallery'));    	
    }
}
