<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    public function saveFeedback($request, $user)
    {
    	$teacher = \DB::table('teacher')->where('user_id', $user->id)->first();
    	if(!$teacher) 
    		return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    	Feedback::insert([
 				'student_id'=>$request['student_id'],
 				'feedback'=>$request['feedback'],
 				'date' => $request['date'],
 				'teacher_id'=>$teacher->id,
 				'school_id'=>$user->school_id   		
    		]);
    	return api(['data'=>'Feedback Submitted Successfully']);
    }

    public function doPostFeedbackByTeacher($request, $user, $teacher)
    {
        Feedback::insert([
                'student_id' => $request['student_id'],
                'feedback' => $request['feedback'],
                'date' => $request['date'],
                'teacher_id' => $teacher->id,
                'school_id' => $user->school_id,
                'feedback_by' => 'teacher'          
        ]);
        return api(['data'=>'Feedback Submitted Successfully']);
    }

    public function doGetFeedbackByStudent($user)
    {
    	$student = \DB::table('student')->where('user_id', $user->id)->first();
    	$feedbacks = Feedback::where('student_id', $student->id)->leftJoin('teacher', 'feedback.teacher_id', '=', 'teacher.id')->orderBy('feedback.id', 'DESC')
    			->select('feedback.id', 'teacher.name as teacherName', 'feedback.feedback', 'feedback.date')
    			->get();
    	return \api::success(['data' => $feedbacks]);
    }
}
