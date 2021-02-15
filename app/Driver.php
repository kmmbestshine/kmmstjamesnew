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
            $schoolname=\DB::table('school')->where('id', $user->school_id)->select('school_name')->first();
            $driver_school_name=str_replace(" ","",$schoolname->school_name);
            $schoolname=substr($driver_school_name, 0, 3);
    
            $check_max_driver = Driver::whereNotNull('driver_user_name')->where('school_id', $user->school_id)->orderBy('driver_user_name', 'desc')->first();
            if($check_max_driver)
            {
                $replacedata=$schoolname.'D'.$user->school_id;
                $driverid=str_replace($replacedata,'',$check_max_driver['driver_user_name'])+1;
               
                $driverlen=4-strlen($driverid);
                //echo $driverlen;
                $finalid='';
                if($driverlen != 0){
                    for($i=0;$i<$driverlen;$i++)
                    {
                        if($i==0)
                        {
                             $finalid='0'.$driverid;   
                        }else
                        {
                            $finalid='0'.$finalid;
                        }
                    }
                }else{
                    $finalid=$driverid;
                }
                $request['driver_user_name']=$schoolname.'D'.\Auth::user()->school_id.$finalid;
               
            }
            else
            {
                $request['driver_user_name']=$schoolname.'D'.\Auth::user()->school_id.'0001';
            }
            \DB::table('users')->where('username', '=', $request['driver_user_name'])->where('school_id', '=', $user->school_id)->delete();
            $user_id = \DB::table('users')->insertGetId([
            'type' => 'drivers',
            'school_id' => \Auth::user()->school_id,
            'username'=>$request['driver_user_name'],
            'password' => \Hash::make($request['driver_mobile']),
            'hint_password' => $request['driver_mobile']
        ]);
            if($user_id){
    		Driver::insert([
    			'school_id' => $user->school_id,
    			'bus_id' => $request['bus_id'],
    			'driver_name' => $request['driver_name'],
    			'driver_mobile' => $request['driver_mobile'],
    			'driver_address' => $request['driver_address'],
    			'driver_city' => $request['driver_city'],                         
                        'driver_user_name' => $request['driver_user_name'],
                    'user_id'=>$user_id
    			]);
                
    		$input['success'] = 'Driver is added Successfully';
            return \Redirect::back()->withInput($input);
            }
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

                User::where('id',$request['user_id'])->update([
                    'password' => \Hash::make($request['driver_password']),
                    'hint_password' => $request['driver_password']
                ]);
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