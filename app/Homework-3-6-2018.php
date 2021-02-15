<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homework';

    public function doPostHomework($request, $user)
    {
        $date = date('d-m-Y', strtotime($request['date']));
    	$homeworkExist = Homework::where('school_id', $user->school_id)
    				->where('class_id', $request['class'])
    				->where('section_id', $request['section'])
    				->where('subject_id', $request['subject'])
    				->where('date', $request['date'])
    				->first();
    	if($homeworkExist)
    	{
    		$request['error'] = 'Homework already exists';
    		return \Redirect::back()->withInput($request);
    	}
    	else
    	{
            $teacher = \DB::table('teacher')->where('class', $request['class'])->where('section', $request['section'])->first();

            if(isset($request['image']))
            {
                $image = $request['image'];
                $extension = $image->getClientOriginalExtension();
                $originalName= $image->getClientOriginalName();
                $directory = 'homework';
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
	        	'school_id' => $user->school_id,
	        	'class_id' => $request['class'],
	        	'section_id' => $request['section'],
	        	'subject_id' => $request['subject'],
	        	'teacher_id' => $teacher->id,
	        	'description' => $request['description'],
	        	'image'=> $imagefile,
                'pdf' => $pdffile,
	        	'date' => $date,
	        	'homework_by' => 'school',
	        ]);
	        $request['success'] = 'Homework saved successfully';
	        return \Redirect::back()->withInput($request);
    	}
    }


    public function doGetHomework($user)
    {
    	$classes = \DB::table('class')->where('school_id', $user->school_id)->get();
    	$class = \Request::get('class');
    	$section = \Request::get('section');
    	$date = date('d-m-Y', strtotime(\Request::get('date')));
        
    	/*
    	 * updated 21-10-2017 by priya
    	 * if($class && $section && $date)
        {
            $homeworks = Homework::where('homework.date', $date)
                ->leftJoin('class', 'homework.class_id', '=', 'class.id')
                ->leftJoin('section', 'homework.section_id', '=', 'section.id')
                ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
                ->select
                (
                    'homework.id',
                    'class.class',
                    'section.section',
                    'subject.subject',
                    'teacher.name',
                    'homework.description',
                    'homework.image',
                    'homework.pdf',
                    'homework.date',
                    'homework.homework_by'
                )
                ->get();
        }*/

    	if($class !='' && $section !='' && $date!='')
    	{
            //return '123';exit;
    	    if($class == 0 && $section == 0)
            {
                //return 'abc';exit;
                $homeworks = Homework::where('homework.date', $date)
                	->where('homework.school_id', $user->school_id)
                    ->leftJoin('class', 'homework.class_id', '=', 'class.id')
                    ->leftJoin('section', 'homework.section_id', '=', 'section.id')
                    ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                    ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
                    ->select
                    (
                        'homework.id',
                        'class.class',
                        'section.section',
                        'subject.subject',
                        'teacher.name',
                        'homework.description',
                        'homework.image',
                        'homework.pdf',
                        'homework.date',
                        'homework.homework_by'
                    )
                    ->get();
            }
            else
            {
                //return '123';exit;
                $homeworks = Homework::where('homework.class_id', $class)
                    ->where('homework.section_id', $section)
                    ->where('homework.date', $date)
                    ->where('homework.school_id', $user->school_id)
                    ->leftJoin('class', 'homework.class_id', '=', 'class.id')
                    ->leftJoin('section', 'homework.section_id', '=', 'section.id')
                    ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                    ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
                    ->select
                    (
                        'homework.id',
                        'class.class',
                        'section.section',
                        'subject.subject',
                        'teacher.name',
                        'homework.description',
                        'homework.image',
                        'homework.pdf',
                        'homework.date',
                        'homework.homework_by'
                    )
                    ->get();
            }

    	}

    	/******** end  ******/

    	else
    	{
    		$homeworks = [];
    	}
    	return view('users.homework.gethomework', compact('classes', 'homeworks'));
    }

    public function saveHomework($flag, $user, $request)
    {
    	$homeworkExist = Homework::where('school_id', $user->school_id)
    				->where('class_id', $request['class_id'])
    				->where('section_id', $request['section_id'])
    				->where('subject_id', $request['subject_id'])
    				->where('date', $request['date'])
    				->first();
    	if($homeworkExist)
    	{
    		return \api::notValid(['errorMsg' => 'Homework already exists', 'id' => $homeworkExist->id]);
    	}
    	else
    	{
    		define('UPLOAD_DIR', 'homework/');
	    	$img = str_replace('data:image/jpeg;base64,', '', $request['image']);
	    	$img = str_replace(' ', '+', $img);
	    	$dataImg = base64_decode($img);
	    	$file = UPLOAD_DIR . uniqid() . '.png';
	    	$success = file_put_contents($file, $dataImg);
	    	if($success)
	    	{
	    		 $teacher = \DB::table('teacher')->where('user_id', $user->id)->first();
	    		 $id = Homework::insertGetId([
	        		'school_id' => $user->school_id,
	        		'class_id'=>$request['class_id'],
	        		'section_id'=>$request['section_id'],
	        		'subject_id'=>$request['subject_id'],
	        		'teacher_id'=>$teacher->id,
	        		'description'=>$request['description'],
	        		'image'=> $file,
	        		'date' => $request['date'],
	        		'platform'=>$flag
	        		]);
	        		return api(['message'=>'Homework saved successfully', 'id' => $id]);
	    	}
	    	else
	    	{
	    		return \api::notValid(['errorMsg' => 'Error In Base 64 String']);
	    	}
    	}
    }

    public function doGetHomeworkByStudent($user, $id, $date)
    {
    	$sessionDate = $date.'-'.date('Y');
        $student = \DB::table('student')->where('user_id', $id)->first();
        if(!$student)
        	return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $homeworks = Homework::where('homework.class_id', $student->class_id)
                        ->where('homework.section_id', $student->section_id)
                        ->where('homework.date', $sessionDate)
                        ->leftJoin('subject', 'homework.subject_id', '=', 'subject.id')
                        ->leftJoin('teacher', 'homework.teacher_id', '=', 'teacher.id')
                        ->select
                        (
                            'homework.id',
                            'subject.subject',
                            'teacher.name as teacherName',
                            'homework.description',
                            'homework.image',
                            'homework.date'
                        )
                        ->orderBy('homework.id', 'DESC')
                        ->get();
                        
        $works = array('name' => $student->name, 'roll_no' => $student->roll_no, 'homework' => $homeworks);
        return \api::success(['data' => $works]);
    }
    
    public function doUpdateHomework($platform, $user, $request)
    {
    	define('UPLOAD_DIR', 'homework/');
	$img = str_replace('data:image/jpeg;base64,', '', $request['image']);
	    	$img = str_replace(' ', '+', $img);
	    	$dataImg = base64_decode($img);
	    	$file = UPLOAD_DIR . uniqid() . '.png';
	    	$success = file_put_contents($file, $dataImg);
	    	if($success)
	    	{
	    		 $teacher = \DB::table('teacher')->where('user_id', $user->id)->first();
	    		 Homework::where('id', $request['id'])->update([
	        		'school_id' => $user->school_id,
	        		'class_id'=>$request['class_id'],
	        		'section_id'=>$request['section_id'],
	        		'subject_id'=>$request['subject_id'],
	        		'teacher_id'=>$teacher->id,
	        		'description'=>$request['description'],
	        		'image'=> $file,
	        		'date' => $request['date'],
	        		'platform'=>$platform
	        		]);
	        		return api(['message'=>'Homework updated successfully']);
	    	}
	    	else
	    	{
	    		return \api::notValid(['errorMsg' => 'Error In Base 64 String']);
	    	}
    }
}
