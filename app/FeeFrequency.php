<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeFrequency extends Model
{
    protected $table = 'fee_frequency';

    public function doPostFrequency($input, $user)
    {
    	$check = FeeFrequency::where('school_id', $user->school_id)->where('frequency', $input['frequency'])->first();
    	if($check)
    	{
    		$input['error'] = 'Frequency already exists';
    		return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		FeeFrequency::insert(['school_id' => $user->school_id, 'frequency' => $input['frequency']]);
    		$input['success'] = 'Frequency added successfully';
    		return \Redirect::back()->withInput($input);
    	}
    }

    public function doUpdateFrequency($input, $user)
    {
    	$check = FeeFrequency::where('school_id', $user->school_id)->where('frequency', $input['frequency'])->where('id', '!=', $input['id'])->first();
    	if($check)
    	{
    		$input['error'] = 'Frequency already exists';
    		return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		FeeFrequency::where('id', $input['id'])->update(['school_id' => $user->school_id, 'frequency' => $input['frequency']]);
    		$input['success'] = 'Frequency updated successfully';
    		return \Redirect::route('fee.frequency')->withInput($input);
    	}
    }
}