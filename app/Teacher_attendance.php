<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teacher_attendance extends Model
{
	protected $table = 'teacher_attendance';

    public function saveAttendance($input, $user)
    {
    	foreach($input['employee_id'] as $emp)
    	{
    		$insertTo = Teacher_attendance::where('employee_id', $emp)->where('date', date('d-m-Y'))->where('school_id', $user->school_id)->update([
    				'employee_id'=>$emp,
    				'school_id'=>$user->school_id,
    				'attendance'=>$input['attendance'.$emp],
    				'date'=>date('d-m-Y'),
    				'in'=>$input['in'.$emp],
    				'out'=>$input['out'.$emp]
    			]);

    		if(!$insertTo)
    		{
				Teacher_attendance::insert([
    				'employee_id'=>$emp,
    				'school_id'=>$user->school_id,
    				'attendance'=>$input['attendance'.$emp],
    				'date'=>date('d-m-Y'),
    				'in'=>$input['in'.$emp],
    				'out'=>$input['out'.$emp]
    			]);    			
    		}
    	}
    	$msg['success'] = 'Success to Attenance Submit';
    	return \Redirect::back()->withInput($msg);
    }
}
