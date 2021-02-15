<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    protected $table = 'notification_type';

    public function doPostNotification($request, $user)
    {
    	$check = NotificationType::where('school_id', $user->school_id)->where('title', $request['title'])	->where('description', $request['description'])->first();
    	if($check)
    	{
    		$input['error'] = 'Notification already exists';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		NotificationType::insert([
    			'school_id' => $user->school_id,
    			'title' => $request['title'],
    			'description' => $request['description']
    		]);
    		$input['success'] = 'Notification is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doUpdateNotification($request, $user)
    {
    	$check = NotificationType::where('school_id', $user->school_id)->where('title', $request['title'])	->where('description', $request['description'])->where('id', '!=', $request['id'])->first();
    	if($check)
    	{
    		$input['error'] = 'Notification already exists';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		NotificationType::where('id', $request['id'])->update([
    			'title' => $request['title'],
    			'description' => $request['description']
    		]);
    		$input['success'] = 'Notification is updated successfully';
            return \Redirect::route('master.notification')->withInput($input);
    	}
    }
}