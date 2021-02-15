<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $table = 'fee';

    public function hasManyStructure()
    {
        return $this->hasMany('App\FeeSummary', 'fee_id', 'id')
                    ->leftJoin('fee_structure', 'fee_summary.fee_structure_id', '=', 'fee_structure.id')
                    ->select('fee_structure.id as structure_id', 'fee_structure.structure', 'fee_summary.id as summary_id', 'fee_summary.fee_id', 'fee_summary.value');
    }

    public function feeGet($class, $user)
    {
    	$fees = Fee::where('fee.class_id', $class)->where('fee.school_id', $user->school_id)->join('class', 'fee.class_id', '=', 'class.id')->join('feetype', 'fee.type', '=', 'feetype.id')->select('fee.id', 'class.class','feetype.type', 'fee.amount')->get();
    	if(!$fees)
    		return api()->notValid(['errorMsg'=>'notFound']);
    	return api(['data'=>$fees]);
    }

    public function doPostFee($input, $user)
    {
    	$student = \DB::table('student')->where('registration_no', $input['registration'])->first();
    	if(!$student)
    	{
    		$input['error'] = 'Registration No is invalid';
    		return \Redirect::back()->withInput($input);
    	}
    	if(isset($input['month']))
    	{
    		$months = json_encode($input['month']);
    	}
    	else
    	{
    		$months = '';
    	}
    	$id = Fee::insertGetId([
    		'school_id' => $user->school_id,
    		'frequency_id' => $input['frequn'], 
    		'registration_no' => $input['registration'],
    		'months' => $months
    	]);
        if($id)
        {
            foreach($input['struc'] as $key => $struc)
            {
                \DB::table('fee_summary')->insert(['fee_id' => $id, 'fee_structure_id' => $key, 'value' => $struc]);
            }
        }
    	$input['success'] = 'Fee is added successfully';
    	return \Redirect::back()->withInput($input);
    }
}
