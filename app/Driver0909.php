<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'driver';

    public function doPostDriver($request, $user)
    {
    	$check = Driver::where('driver_mobile', $request['driver_mobile'])->where('school_id', $user->school_id)->first();
    	if(!$check)
    	{
    		Driver::insert([
    			'school_id' => $user->school_id,
    			'bus_id' => $request['bus_id'],
    			'driver_name' => $request['driver_name'],
    			'driver_mobile' => $request['driver_mobile'],
    			'driver_address' => $request['driver_address'],
    			'driver_city' => $request['driver_city']
    			]);
    		$input['success'] = 'Driver is added Successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		$input['success'] = 'Driver already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doUpdateDriver($request, $user)
    {
    	$check = Driver::where('driver_mobile', $request['driver_mobile'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
    	if(!$check)
    	{
    		Driver::where('id', $request['id'])->update([
    			'bus_id' => $request['bus_id'],
    			'driver_name' => $request['driver_name'],
    			'driver_mobile' => $request['driver_mobile'],
    			'driver_address' => $request['driver_address'],
    			'driver_city' => $request['driver_city']
    			]);
    		$input['success'] = 'Driver is updated Successfully';
            return \Redirect::route('master.driver')->withInput($input);
    	}
    	else
    	{
    		$input['success'] = 'Driver already exists';
            return \Redirect::back()->withInput($input);
    	}
    }
}