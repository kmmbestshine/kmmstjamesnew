<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class Exam extends Model
{
    protected $table='exam';

    // public function doPostExamType($request, $user)
    // {
    // 	$check = Exam::where('exam_type', $request['exam_type'])->where('school_id', $user->school_id)->first();
    // 	if(!$check)
    // 	{
	   //  	Exam::insert([
	   //  		'school_id' => $user->school_id,
    //             'exam_type' => $request['exam_type']
	   //  		]);
	   //  	$input['success'] = 'Exam Type is added successfully';
    //         return \Redirect::back()->withInput($input);
    // 	}
    // 	else{
    // 		$input['error'] = 'Session already exists';
    //         return \Redirect::back()->withInput($input);
    // 	}
    // }
    public function doPostExamType($request, $user)
    {//change by mari
        $check = Exam::where('exam_type', $request['exam_type'])->where('school_id', $user->school_id)->first();
        if(!$check)
        {
            Exam::insert([
                'school_id' => $user->school_id,
                'exam_type' => $request['exam_type'],
                'pass_marks'=> $request['pass_marks'],
                'from'=> $request['from'],
                'to'=> $request['to'],
                'max_marks'=> $request['max_marks']
                ]);
            $input['success'] = 'Exam Type is added successfully';
            return \Redirect::back()->withInput($input);
        }
        else{
            $input['error'] = 'Exam Type already exists';
            return \Redirect::back()->withInput($input);
        }
    }
    
    public function ManyResults()
    {
    	$user = JWTAuth::parseToken()->authenticate();
    	$student = \DB::table('student')->where('user_id', $user->id)->first();
    	return $this->hasMany('\App\Result', 'exam_type_id', 'id')->where('result.student_id', $student->id)
    			->leftJoin('subject', 'result.subject_id', '=', 'subject.id')
    			->select('result.id', 'result.exam_type_id', 'result.month', 'result.date', 'result.max_marks', 'result.pass_marks', 'subject.subject', 'result.obtained_marks');
    }
    
    public function ManyResultsParent()
    {
    	return $this->hasMany('\App\Result', 'exam_type_id', 'id')->leftJoin('subject', 'result.subject_id', '=', 'subject.id')
    			->select('result.id', 'result.exam_type_id', 'result.month', 'result.date', 'result.max_marks', 'result.pass_marks', 'subject.subject', 'result.obtained_marks');
    			
    }

    public function doDeleteExamType($id)
    {
        $exam = Exam::where('id', $id)->delete();
        $input['success'] = 'Exam Type is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditExamType($id)
    {
        $exam = Exam::where('id', $id)->first();
        return view('users.master.exam.edit', compact('exam'));
    }

    // public function doUpdateExamType($request, $user)
    // {
    //     $check = Exam::where('exam_type', $request['exam_type'])
    //             ->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
    //     if(!$check)
    //     {
    //         Exam::where('id', $request['id'])->update([
    //             'exam_type' => $request['exam_type']
    //             ]);
    //         $input['success'] = 'Exam Type is updated successfully';
    //         return \Redirect::route('master.exam')->withInput($input);
    //     }
    //     else{
    //         $input['error'] = 'Exam Type already exists';
    //         return \Redirect::back()->withInput($input);
    //     }
    // }
    public function doUpdateExamType($request, $user)
    {//change by mari
        $check = Exam::where('exam_type', $request['exam_type'])
                ->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if(!$check)
        {
            Exam::where('id', $request['id'])->update([
                'exam_type' => $request['exam_type'],
                'pass_marks'=>$request['pass_marks'],
                'max_marks'=>$request['max_marks'],
                'from'=>$request['from'],
                'to'=>$request['to']

                ]);
            $input['success'] = 'Exam Type is updated successfully';
            return \Redirect::route('master.exam')->withInput($input);
        }
        else{
            $input['error'] = 'Exam Type already exists';
            return \Redirect::back()->withInput($input);
        }
    }


    public function doExportMasterExamType($user)
    {
        $exams = Exam::where('school_id', $user->school_id)
                    ->select('id', 'exam_type')->get()->toArray();
        
        \Excel::create('exams', function($excel) use ($exams) {
            $excel->sheet('exams', function($sheet) use ($exams)
            {           
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );

                $sheet->getDefaultStyle()->applyFromArray($style);
                $sheet->setFontSize(12);
                $sheet->setAllBorders('thin');

                $sheet->row(1, array(
                    'Exam Id', 'Exam'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($exams);
            });
        })->download('xls');
    }
}
