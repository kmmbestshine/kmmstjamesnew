<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $table = 'result';

    public function doPostResult($request, $user)
    {
        // dd($request);
        $teacher = \DB::table('teacher')->where('class', $request['class'])->where('section', $request['section'])->first();
        //dd($request['student_marks']);
        foreach($request['student_marks'] as $student_id => $marks)
        {
         die(var_dump($marks));   //dd($student_id, $marks);
            foreach($marks as $subject_id => $mark)
            {
                //dd($student_id, $subject_id,  $mark, $request['result'][$student_id], $request['remarks'][$student_id]);
                
                $grades = \DB::table('grade')->where('school_id', $user->school_id)->get();
                $markGrade = '';
                foreach($grades as $grade)
                {
                    if(($grade->min <= $mark) && ($mark <= $grade->max)){$markGrade = $grade->grade;}
                }

                $check = Result::where('class_id', $request['class'])
                        ->where('section_id', $request['section'])
                        ->where('exam_type_id', $request['exam_type_id'])
                        ->where('subject_id', $subject_id)
                        ->where('month', $request['month'])
                        ->where('student_id', $student_id)
                        ->first();
                if($check)
                {
                    // Result::where('id', $check->id)->update([
                    //     'date' => $request['date'],
                    //     'max_marks' => $request['max_marks'],
                    //     'pass_marks' => $request['pass_marks'],
                    //     'obtained_marks' => $mark,
                    //     'grade' => $markGrade
                    // ]);
                }
                else
                {   
                    Result::insert([
                        'class_id' => $request['class'],
                        'section_id' => $request['section'],
                        'exam_type_id' => $request['exam_type_id'],
                        'month' => $request['month'],
                        'subject_id' => $subject_id,
                        'student_id' => $student_id,
                        'teacher_id' => $teacher->id,
                        'date' => date('d-m-Y', strtotime($request['date'])),
                        'max_marks' => $request['max_marks'],
                        'pass_marks' => $request['pass_marks'],
                        'obtained_marks' => $mark,
                        'grade' => $markGrade
                    ]);
                }
            }

            \DB::table('result_mod')->insert([
                'student_id' => $student_id,
                'exam_type_id' => $request['exam_type_id'],
                'month' => $request['month'],
                'teacher_id' => $teacher->id,
                'date' => date('d-m-Y', strtotime($request['date'])),
                'result' => $request['result'][$student_id],
                'remarks' => $request['remarks'][$student_id]
            ]);
        }
        $input['success'] = 'Result is added successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doPostResultByTeacher($request, $user, $teacher)
    {
        foreach($request['student_marks'] as $sub_key => $marks)
        {
            foreach($marks as $key => $mark)
            {
                $check = Result::where('class_id', $teacher->class)
                        ->where('section_id', $teacher->section)
                        ->where('exam_type_id', $request['exam_type_id'])
                        ->where('subject_id', $sub_key)
                        ->where('month', $request['month'])
                        ->where('student_id', $request['student_id'][$key])
                        ->first();
                if($check)
                {
                    Result::where('id', $check->id)->update([
                        'date' => $request['date'],
                        'max_marks' => $request['max_marks'],
                        'pass_marks' => $request['pass_marks'],
                        'obtained_marks' => $mark
                    ]);
                }
                else
                {
                    Result::insert([
                        'class_id' => $teacher->class,
                        'section_id' => $teacher->section,
                        'exam_type_id' => $request['exam_type_id'],
                        'month' => $request['month'],
                        'subject_id' => $sub_key,
                        'student_id' => $request['student_id'][$key],
                        'teacher_id' => $teacher->id,
                        'date' => $request['date'],
                        'max_marks' => $request['max_marks'],
                        'pass_marks' => $request['pass_marks'],
                        'obtained_marks' => $mark,
                        'result_by' => 'teacher'
                    ]);
                }
            }
        }
        $input['success'] = 'Result is added successfully';
        return \Redirect::back()->withInput($input);
    }   

    public function doGetResults($platform, $class, $section)
    {
    	$results = Result::where('result.class_id', $class)->where('result.section_id', $section)
    				->get();
    	return \api(['data' => $results]);
    }

    public function doGetResultsByTeacherAPI($user, $teacher, $examid)
    {
        $results = Result::where('result.class_id', $teacher->class)
                        ->where('result.section_id', $teacher->section)
                        ->where('result.exam_type_id', $examid)
                    ->leftJoin('exam', 'result.exam_type_id', '=', 'exam.id')
                    ->leftJoin('subject', 'result.subject_id', '=', 'subject.id')
                    ->leftJoin('student', 'result.student_id', '=', 'student.id')
                    ->select
                    (
                        'result.id',
                        'result.month',
                        'student.name',
                        'student.roll_no',
                        'subject.subject',
                        'result.date',
                        'result.max_marks',
                        'result.pass_marks',
                        'result.obtained_marks',
                        'exam.exam_type',
                        'result.result',
                        'result.grade'
                    )
                    ->get();

        return \api(['data' => $results]);
    }

    public function doDeleteResult($platform, $id)
    {
    	$result = Result::where('id'. $id)->first();
    	if(!$result)
    		return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    	Result::where('id'. $id)->delete();
    	return \api(['data' => 'Result is deleted successfully']);
    }

    public function doEditResult($platform, $id)
    {
    	$result = Result::where('result.id', $id)
    				->leftJoin('class', 'result.class_id', '=', 'class.id')
    				->leftJoin('section', 'result.section_id', '=', 'section.id')
    				->leftJoin('subject', 'result.subject_id', '=', 'subject.id')
    				->leftJoin('student', 'result.student_id', '=', 'student.id')
    				->leftJoin('exam', 'result.exam_type_id', '=', 'exam.id')
    				->select
    				(
    					'result.id',
    					'result.max_marks',
    					'result.pass_marks',
    					'result.obtained_marks',
    					'student.id as student_id',
    					'student.name',
    					'student.roll_no',
    					'class.class',
    					'class.id as class_id',
    					'section.section',
    					'section.id as section_id',
    					'subject.subject',
    					'subject.id as subject_id',
    					'exam.exam_type',
    					'exam.id as exam_type_id'
    				)
    				->first();
    	if(!$result)
    		return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    	return \api(['data' => $result]);
    }

    public function doUpdateResult($request, $user, $platform)
    {
    	$check = Result::where('class_id', $request['class_id'])
    					->where('section_id', $request['section_id'])
    					->where('exam_type_id', $request['exam_type_id'])
    					->where('subject_id', $request['subject_id'])
    					->where('student_id', $request['student_id'])
    					->where('id', '!=', $request['id'])
    					->first();
    	if($check)
    	{
    		return \api::notValid(['errorMsg' => 'Result Already Exists']);
    	}
    	else
    	{
    		$teacher = \DB::table('teacher')->where('user_id', $user->id)->first();
    		Result::where('id', $request['id'])->update([
    			'class_id' => $request['class_id'],
    			'section_id' => $request['section_id'],
    			'exam_type_id' => $request['exam_type_id'],
    			'subject_id' => $request['subject_id'],
    			'student_id' => $request['student_id'],
    			'teacher_id' => $teacher->id,
    			'date' => $request['date'],
    			'max_marks' => $request['max_marks'],
    			'pass_marks' => $request['pass_marks'],
    			'obtained_marks' => $request['obtained_marks']
    		]);
    		return \api(['data' => 'Result is updated successfully']);
    	}
    }
}