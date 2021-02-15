<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusRoute extends Model
{
    protected $table = 'route';

    public function doPostBusRoute($request, $user, $platform)
    {
    	$bus = \DB::table('bus')->where('id', $request['bus_id'])->first();
    	if(!$bus)
    		return \api::notFound(['errorMsg' => 'Bus Not Exist']);
    	$stop = \DB::table('bus_stop')->where('id', $request['bus_stop_id'])->first();
    	if(!$stop)
    		return \api::notFound(['errorMsg' => 'Stop Not Exist']);
    	$route = BusRoute::where('bus_id', $request['bus_id'])
    					->where('bus_stop_id', $request['bus_stop_id'])
    					->where('city', $request['city'])
    					->where('route', $request['route'])
    					->first();
    	if($route)
    	{
    		return \api::notValid(['errorMsg' => 'Route Already Exists']);
    	}
    	else
    	{
    		BusRoute::insert([
    				'school_id' => $user->school_id,
    				'bus_id' => $request['bus_id'],
    				'bus_stop_id' => $request['bus_stop_id'],
    				'city' => $request['city'],
    				'route' => $request['route']
    		]);
    		return \api(['data' => 'Route is added successfully']);
    	}
    }

    public function doGetBusRoutes($user, $platform)
    {
    	$routes = BusRoute::where('route.school_id', $user->school_id)
    				->leftJoin('bus', 'route.bus_id', '=', 'bus.id')
    				->leftJoin('bus_stop', 'route.bus_stop_id', '=', 'bus_stop.id')
    				->select('bus.bus_no', 'bus_stop.stop', 'route.city', 'route.route')
    				->get();
    	if(count($routes)>0)
    		return \api(['data' => $routes]);
    	return \api(['data' => 'No Rows Found!!!']);
    }	

    public function doUpdateBusRoute($request, $user, $platform)
    {
    	$bus = \DB::table('bus')->where('id', $request['bus_id'])->first();
    	if(!$bus)
    		return \api::notFound(['errorMsg' => 'Bus Not Exist']);
    	$stop = \DB::table('bus_stop')->where('id', $request['bus_stop_id'])->first();
    	if(!$stop)
    		return \api::notFound(['errorMsg' => 'Stop Not Exist']);
    	$route = BusRoute::where('bus_id', $request['bus_id'])
    					->where('bus_stop_id', $request['bus_stop_id'])
    					->where('city', $request['city'])
    					->where('route', $request['route'])
    					->where('id', '!=', $request['id'])
    					->first();
    	if($route)
    	{
    		return \api::notValid(['errorMsg' => 'Route Already Exists']);
    	}
    	else
    	{
    		BusRoute::where('id', $request['id'])->update([
    				'school_id' => $user->school_id,
    				'bus_id' => $request['bus_id'],
    				'bus_stop_id' => $request['bus_stop_id'],
    				'city' => $request['city'],
    				'route' => $request['route']
    		]);
    		return \api(['data' => 'Route is updated successfully']);
    	}
    }
}