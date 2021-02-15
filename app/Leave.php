<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $table = 'leave_request';

    public function postLeave($user, $request)
    {
    	$leave = Leave::where('student_id', $request['student_id'])->where('to_leave', $request['leave_to'])->where('from_leave', $request['leave_from'])->first();
    	if($leave)
    	{
    		return api()->notValid(['errorMsg'=>'Leave Request is already submitted']);
    	}
    	else
    	{
    		Leave::insert([
    			'student_id'=>$request['student_id'],
    			'from_leave'=>$request['leave_from'],
    			'to_leave'=>$request['leave_to'],
    			'status'=>$request['status'],
    			'by_request'=>$request['request_by'],
    			'remarks'=>$request['remarks'],
    			'school_id'=>$user->school_id,
    			'user_id'=>$user->id
    		]);
    		return api(['data'=>'Leave Request is Submitted Successfully']);
    	}
    }

    public function doPostLeaveRequestByTeacher($user, $request, $teacher)
    {
        $leave = Leave::where('student_id', $request['student_id'])->where('to_leave', $request['leave_to'])->where('from_leave', $request['leave_from'])->first();
        if($leave)
        {
            return api()->notValid(['errorMsg'=>'Leave Request is already submitted']);
        }
        else
        {
            $request['remarks'] = (isset($request['remarks']) ? $request['remarks'] : "");
            Leave::insert([
                'student_id' => $request['student_id'],
                'from_leave' => $request['leave_from'],
                'to_leave' => $request['leave_to'],
                'status' => $request['status'],
                'by_request' => $request['request_by'],
                'remarks' => $request['remarks'],
                'school_id' => $user->school_id,
                'user_id' => $user->id
            ]);
            return api(['data'=>'Leave Request is Submitted Successfully']);
        }
    }

    public function doPostLeaveByParent($user, $request)
    {
        $leave = Leave::where('student_id', $request['student_id'])->where('to_leave', $request['leave_to'])->where('from_leave', $request['leave_from'])->first();

        if($leave)
        {
            return api()->notValid(['errorMsg'=>'Leave Request is already submitted']);
        }
        else
        {
            Leave::insert([
                'student_id'=>$request['student_id'],
                'from_leave'=>$request['leave_from'],
                'to_leave'=>$request['leave_to'],
                'status'=>$request['status'],
                'by_request'=>$request['request_by'],
                'remarks'=>$request['remarks'],
                'school_id'=>$user->school_id,
                'user_id'=>$user->id
            ]);
            return api(['data'=>'Leave Request is Submitted Successfully']);
        }
    }
    
    public function getLeaveRequestByParam($class, $section, $month)
    {
    	$students = \DB::table('student')
    			->where('student.class_id', $class)
    			->where('student.section_id', $section)
    			->join('class', 'student.class_id', '=', 'class.id')
    			->join('section', 'student.section_id', '=', 'section.id')
    			->select('student.id', 'student.name', 'student.roll_no', 'student.avatar', 'class.class', 'section.section')
    			->get();
    	foreach($students as $student)
    	{
    		$leave = \DB::table('leave_request')->where('student_id', $student->id)->where('from_leave', 'LIKE', '%'.$month.'%')
    				->select('id', 'from_leave', 'to_leave', 'status', 'remarks', 'teacher_remarks')->get();
    		if($leave)
    		{
    			$leaves[] = array(
    					'student_leave_info' => $leave,
    					'total_request' => count($leave),
    					'student_id' => $student->id,
    					'name' => $student->name,
    					'image' => $student->avatar,
    					'class' => $student->class,
    					'section' => $student->section,
    					'roll_no' => $student->roll_no
    					);
    		}
    	}
    	return \api::success(['data' => $leaves]);
    }
    
    public function doUpdateLeaveRequest($request, $user, $platform)
    {
    	$request['teacher_remarks'] = (isset($request['teacher_remarks']) ? $request['teacher_remarks'] : '');
    	Leave::where('id', $request['id'])->update(['status' => $request['status'], 'teacher_remarks' => $request['teacher_remarks']]);
    	return \api(['data' => 'Leave is updated successfully']);
    }
}
