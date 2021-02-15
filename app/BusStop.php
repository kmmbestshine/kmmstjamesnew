<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Bus;

class BusStop extends Model
{
    protected $table = 'bus_stop';
    
    public function doPostBusStop($request, $user)
    {
    	$check = BusStop::where('school_id', $user->school_id)->where('bus_id', $request['bus_id'])->where('stop', $request['stop'])->first();
    	if($check)
    	{
    		return \api::notValid(['errorMsg' => 'Stop Already Exists']);
    	}
    	else
    	{
    		BusStop::insert([
                'school_id' => $user->school_id, 
                'bus_id' => $request['bus_id'],
                'stop' => $request['stop'],
                'stop_index' => $request['stop_index'],
                'lattitude' => $request['lattitude'],
                'longitude' => $request['longitude'],
                'transport_fee' => $request['transport_fee']
            ]);
    		$input['success'] = 'Bus Stop is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    }
    
    public function doUpdateBusStop($request, $user)
    {
    	$check = BusStop::where('school_id', $user->school_id)->where('stop', $request['stop'])->where('id', '!=', $request['id'])->where('bus_id', $request['bus_id'])->first();
    	if($check)
        {
    		$input['error'] = 'Bus already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            BusStop::where('id', $request['id'])->update([
                'bus_id' => $request['bus_id'],
                'stop' => $request['stop'],
                'stop_index' => $request['stop_index'],
                'lattitude' => $request['lattitude'],
                'longitude' => $request['longitude'],
                'transport_fee' => $request['transport_fee']
            ]);
            $input['success'] = 'Bus Stop is updated successfully';
            return \Redirect::route('master.stop')->withInput($input);
        }
    }
}