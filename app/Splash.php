<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Splash extends Model
{
    protected $table = 'splash';
    
    public function doPostSplash($request, $user)
    {
    	define('UPLOAD_DIR', 'splash/');
	$img = str_replace('data:image/jpeg;base64,', '', $request['splash']);
	$img = str_replace(' ', '+', $img);
	$dataImg = base64_decode($img);
	$file = UPLOAD_DIR . uniqid() . '.png';
    	$success = file_put_contents($file, $dataImg);
    	
    	if($success)
    	{
    		$splash = Splash::where('school_id', $user->school_id)->update(['image' => $file]);
	    	if(!$splash)
	    	{
	    		Splash::insert(['school_id' => $user->school_id, 'image' => $file]);
	    		return \api::success(['data' => 'Splash added successfully']);
	    	}
	    	else
	    	{
	    		return \api::success(['data' => 'Splash updated successfully']);
	    	}
    	}
    	else
    	{
    		return api::notValid(['errorMsg' => 'There is some error in string']);
    	}

    }
    
    public function doGetSplash($user)
    {
    	return \api::success(['data' => Splash::where('school_id', $user->school_id)->first()]);
    }
}
