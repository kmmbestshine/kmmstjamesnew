<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'salary';

    public function doPostSalary($request, $user)
    {
    	$check =  Salary::where('school_id', $user->school_id)->where('value', $request['salary'])->first();
    	if($check)
    	{
    		$request['error'] = 'Salary Structure is already exists';
    		return \Redirect::back()->withInput($request);
    	}
    	else
    	{
    		Salary::insert(['school_id' => $user->school_id, 'value' => $request['salary']]);
    		$request['success'] = 'Salary Structure is added successfully';
    		return \Redirect::back()->withInput($request);
    	}
    }

    public function doUpdateSalary($request, $user)
    {
    	$check =  Salary::where('school_id', $user->school_id)->where('value', $request['salary'])->where('id', '!=', $request['id'])->first();
    	if($check)
    	{
    		$request['error'] = 'Salary Structure is already exists';
    		return \Redirect::back()->withInput($request);
    	}
    	else
    	{
    		Salary::where('id', $request['id'])->update(['value' => $request['salary']]);
    		$request['success'] = 'Salary Structure is updated successfully';
    		return \Redirect::route('master.salary')->withInput($request);
    	}
    }
}