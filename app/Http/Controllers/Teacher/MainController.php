<?php

namespace App\Http\Controllers\Teacher;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator, Redirect, Auth, api;

use App\addClass;
use App\Attendance;
use App\BloodGroup;
use App\Bus;
use App\BusRoute;
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
use App\Salary;
use App\School;
use App\Section;
use App\Session;
use App\Splash;
use App\Staff;
use App\State;
use App\Students;
use App\Subject; 
use App\TimeTable;
use App\User;

class MainController extends Controller
{
	protected $teacher;

    protected $user;

    public function __construct(){
    
        if(Auth::check())
        {
            $class = \DB::table('class')->where('school_id', Auth::user()->school_id)->get();
            $this->teacher = Employee::where('user_id', Auth::user()->id)->first();
            $this->user = \Auth::user();
            view()->share(compact('class'));
        }
    }

    public function dashBoard()
    {
        return view('teacher.index');
    }

    public function getAttendance()
    {
        $students = \DB::table('student')->where('class_id', $this->teacher->class)
                            ->where('section_id', $this->teacher->section)
                            //->orderBy('roll_no', 'ASC')
                            ->orderBy('name', 'ASC')
                            ->get();
        return view('teacher.attendance.index', compact('students'));
    }

    public function postAttendance()
    {
        $input = \Request::all();
        $userError = ['attendance' => 'Attendance', 'date' => 'Date in dd-mm-yyyy'];
        $validator = \Validator::make($input, [
            'date' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \Redirect::back()->withInput($input)->withErrors($validator);
        }
        else
        {
            foreach($input['attendance'] as $key => $atten)
            {           
                $input['remarks'][$key] = (isset($input['remarks'][$key]) ? $input['remarks'][$key] : '');   
                $atten_exist = \DB::table('attendance')->where('class_id', $this->teacher->class)
                                ->where('section_id', $this->teacher->section)
                                ->where('date', date('d-m-Y', strtotime($input['date'])))
                                ->where('student_id', $key)
                                ->first();
                    
                if($atten_exist)
                {
                    Attendance::where('id', $atten_exist->id)->update([
                        'attendance' => $atten,
                        'remarks' => $input['remarks'][$key],
                        'teacher_id' => $this->teacher->id,
                        'attendance_by' => 'teacher'
                    ]);
                }
                else
                {
                    Attendance::insert([
                        'school_id' => $this->user->school_id,
                        'teacher_id' => $this->teacher->id,
                        'class_id' => $this->teacher->class,
                        'section_id' => $this->teacher->section,
                        'student_id' => $key,
                        'attendance' => $atten,
                        'remarks' => $input['remarks'][$key],
                        'date' => date('d-m-Y', strtotime($input['date'])),
                        'attendance_by' => 'teacher'
                    ]);
                }
            }
            $input['success'] = 'Attendance is added successfully';
            return Redirect::back()->withInput($input);
        }
    }

    public function viewAttendance()
    {
        $attendances = \DB::table('attendance')->where('attendance.class_id', $this->teacher->class)
                        ->where('attendance.section_id', $this->teacher->section)
                        ->where('attendance.date', date('d-m-Y'))
                        ->leftJoin('student', 'attendance.student_id', '=', 'student.id')
                        ->select
                        (
                            'attendance.attendance',
                            'attendance.date',
                            'attendance.remarks',
                            'student.name',
                            'student.roll_no'
                        )
                        ->get();
        return view('teacher.attendance.view', compact('attendances'));
    }

    public function homework()
    {
        $section = Section::where('id', $this->teacher->section)->first();
        $subjects = Subject::whereIn('id', json_decode($section->subjects))->get();
    	return view('teacher.homework.homework', compact('subjects'));
    }

    public function postHomework()
    {
        $request = \Request::all();
        $userError = ['subject' => 'Subject Id', 'description' => 'Description', 'image' => 'Image', 'date' => 'Date'];
        $validator = \Validator::make($request, [
            'subject' => 'required|numeric',
            'description' => 'required',
            'image' => 'image',
            'pdf' => 'mimes:pdf',
            'date' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \Redirect::back()->withErrors($validator)->withInput($request);
        }
        else
        {  
            $homeworkExist = Homework::where('school_id', $this->user->school_id)
                    ->where('class_id', $this->teacher->class)
                    ->where('section_id', $this->teacher->section)
                    ->where('subject_id', $request['subject'])
                    ->where('date', date('d-m-Y', strtotime($request['date'])))
                    ->first();
            if($homeworkExist)
            {
                $request['error'] = 'Homework already exists';
                return \Redirect::back()->withInput($request);
            }
            else
            {
                if(isset($request['image']))
                {
                    $image = $request['image'];
                    $extension = $image->getClientOriginalExtension();
                    $originalName= $image->getClientOriginalName();
                    $directory = 'homework';
                    $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
                    $image= \Image::make($image);
                    $image->resize(700,null, function ($constraint)
                    {
                        $constraint->aspectRatio();
                    })->save($directory. '/' . $filename);
                    $imagefile = $directory.'/'.$filename;
                }
                else
                {
                    $imagefile = '';
                }

                if(isset($request['pdf']))
                {
                    $pdf = $request['pdf'];
                    $ex = $pdf->getClientOriginalExtension();
                    $name = $pdf->getClientOriginalName();
                    $destinationPath = 'homework';
                    $pdfname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                    $upload_pdf = $pdf->move($destinationPath, $pdfname);
                    $pdffile = $destinationPath.'/'.$pdfname;
                }
                else
                {
                    $pdffile = '';
                }
                
                Homework::insert([
                    'school_id' => $this->user->school_id,
                    'class_id' => $this->teacher->class,
                    'section_id' => $this->teacher->section,
                    'subject_id' => $request['subject'],
                    'teacher_id' => $this->teacher->id,
                    'description' => $request['description'],
                    'image'=> $imagefile,
                    'pdf' => $pdffile,
                    'date' => date('d-m-Y', strtotime($request['date'])),
                    'homework_by' => 'teacher',
                ]);
                $request['success'] = 'Homework saved successfully';
                return \Redirect::back()->withInput($request);
            }
        }
    }

    public function viewHomework()
    {
        $homeworks = Homework::where('homework.class_id', $this->teacher->class)
                            ->where('homework.section_id', $this->teacher->section)
                            ->where('homework.date', date('d-m-Y'))
                            ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                            ->leftJoin('class', 'homework.class_id', '=', 'class.id')
                            ->leftJoin('section', 'homework.section_id', '=', 'section.id')
                            ->select
                            (
                                'class.class',
                                'section.section',
                                'subject.subject',
                                'homework.description',
                                'homework.image',
                                'homework.pdf',
                                'homework.date'
                            )
                            ->get();
        return view('teacher.homework.view', compact('homeworks'));
    }

    public function leaveApproval()
    {
    	$students = Students::where('class_id', $this->teacher->class)->where('section_id', $this->teacher->section)->orderBy('roll_no', 'ASC')->get();
        $leaves = Leave::where('leave_request.school_id', $this->user->school_id)
                        ->leftJoin('student', 'leave_request.student_id', '=', 'student.id')
                        ->leftJoin('class', 'student.class_id', '=', 'class.id')
                        ->leftJoin('section', 'student.section_id', '=', 'section.id')
                        ->select
                        (
                            'leave_request.id',
                            'student.name',
                            'student.roll_no',
                            'class.class',
                            'section.section',
                            'leave_request.from_leave',
                            'leave_request.to_leave',
                            'leave_request.status',
                            'leave_request.by_request',
                            'leave_request.remarks',
                            'leave_request.teacher_remarks'
                        )
                        ->orderBy('leave_request.id', 'DESC')->get();
        return view('teacher.leaveapprove.index', compact('students', 'leaves'));
    }

    public function postLeave()
    {
        $request = \Request::all();
        $userError = ['student_id' => 'Student Id', 'leave_from' => 'Leave From Date', 'leave_to' => 'Leave To Date', 'request_by' => 'Request By', 'status' => 'Status'];
        $validator = \Validator::make($request, [
                'student_id'=>'required',
                'leave_from'=>'required',
                'leave_to'=>'required',
                'request_by'=>'required',
                'status' => 'required'
            ], $userError);
        $validator->setAttributeNames($userError);
        if($validator->fails())
        {
            return \Redirect::back()->withInput($request)->withErrors($validator);
        }
        else
        {
            $leave = Leave::where('student_id', $request['student_id'])
                        ->where('to_leave', date('d-m-Y', strtotime($request['leave_to'])))
                        ->where('from_leave', date('d-m-Y', strtotime($request['leave_from'])))
                        ->first();
            if($leave)
            {
                $request['error'] = 'Leave Request is already submitted';
                return Redirect::back()->withInput($request);
            }
            else
            {
                Leave::insert([
                    'student_id' => $request['student_id'],
                    'from_leave' => date('d-m-Y', strtotime($request['leave_from'])),
                    'to_leave' => date('d-m-Y', strtotime($request['leave_to'])),
                    'status' => $request['status'],
                    'by_request' => $request['request_by'],
                    'remarks' => $request['remarks'],
                    'school_id' => $this->user->school_id,
                ]);
                $request['success'] = 'Leave Request is Submitted Successfully';
                return Redirect::back()->withInput($request); 
            }
        }
    }

    public function editLeave($id)
    {
        $leave = Leave::where('leave_request.id', $id)
                    ->leftJoin('student', 'leave_request.student_id', '=', 'student.id')
                    ->select
                    (
                        'leave_request.id',
                        'leave_request.from_leave',
                        'leave_request.to_leave',
                        'leave_request.status',
                        'leave_request.by_request',
                        'leave_request.remarks',
                        'student.name',
                        'student.id as student_id'
                    )
                    ->first();
        return view('teacher.leaveapprove.edit', compact('leave'));
    }

    public function updateLeave()
    {
        $request = \Request::all();
        $leave = Leave::where('student_id', $request['student_id'])
                        ->where('to_leave', date('d-m-Y', strtotime($request['leave_to'])))
                        ->where('from_leave', date('d-m-Y', strtotime($request['leave_from'])))
                        ->first();
        if($leave)
        {
            $request['error'] = 'Leave Request is already submitted';
            return Redirect::back()->withInput($request);
        }
        else
        {
            if($request['leave_from'] != '')
            {
                $leave_from = date('d-m-Y', strtotime($request['leave_from']));
            }
            else
            {
                $leave_from = date('d-m-Y', strtotime($request['leave_from_old']));
            }
            if($request['leave_from'] != '')
            {
                $leave_to = date('d-m-Y', strtotime($request['leave_to']));
            }
            else
            {
                $leave_to = date('d-m-Y', strtotime($request['leave_to_old']));
            }
            Leave::where('id', $request['id'])->update([
                'student_id' => $request['student_id'],
                'from_leave' => $leave_from,
                'to_leave' => $leave_to,
                'status' => $request['status'],
                'by_request' => $request['request_by'],
                'remarks' => $request['remarks']
            ]);
            $request['success'] = 'Leave Request is Updated Successfully';
            return Redirect::route('teach.leaveApproval')->withInput($request); 
        }
    }

    public function schoolProfile()
    {
        $school = School::where('id', $this->user->school_id)->first();
        return view('teacher.resources.schoolProfile', compact('school'));
    }

    public function resourceEmployee()
    {
        return view('teacher.resources.employee');
    }

    public function timeTable()
    {
        return view('teacher.resources.timetable');
    }

    public function feeStructure()
    {
        return view('teacher.resources.feeStructure');
    }

    public function gallery()
    {
        $galleries = Gallery::where('school_id', $this->user->school_id)->get();
        return view('teacher.gallery.index', compact('galleries'));
    }
}
