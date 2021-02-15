<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'post';

    public function doPostPost($user, $request)
    {
    	$post = Post::where('school_id', $user->school_id)->first();
    	if($post)
    	{
    		$image = $request['image'];
			$extension = $image->getClientOriginalExtension();
		    $originalName= $image->getClientOriginalName();
		    $directory = 'post';
		    $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
		    $image= \Image::make($image);
		    $image->resize(350,null, function ($constraint)
		    {
		        $constraint->aspectRatio();
            })->save($directory. '/' . $filename);
    		Post::where('id', $post->id)->update(['school_id' => $user->school_id, 'image' => $directory. '/' . $filename]);
    		$request['success'] = 'Post is updated successfully';
    		return \Redirect::back()->withInput($request);
    	}
    	else
    	{
    		$image = $request['image'];
			$extension = $image->getClientOriginalExtension();
		    $originalName= $image->getClientOriginalName();
		    $directory = 'post';
		    $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
		    $image= \Image::make($image);
		    $image->resize(350,null, function ($constraint)
		    {
		        $constraint->aspectRatio();
            })->save($directory. '/' . $filename);
    		Post::insert(['school_id' => $user->school_id, 'image' => $directory. '/' . $filename]);
    		$request['success'] = 'Post is added successfully';
    		return \Redirect::back()->withInput($request);
    	}
    }
}