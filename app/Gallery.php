<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $table = 'gallery';

    public function hasManyImages()
    {
        return $this->hasMany('App\GalleryImages', 'gallery_id', 'id');
    }

    public function doFieldsGallery($user, $request)
    {
        $id = Gallery::insertGetId([
                    'school_id' => $user->school_id,
                    'event' => $request['header'],
                    'date' => date('d-m-Y', strtotime($request['date']))
            ]);
        $input['id'] = $id;
        $input['fieldsSuccess'] = true;
        return \Redirect::back()->withInput($input);
    }

    public static function doPostGallery($user, $request, $id)
    {
    	$filename = '';
        $image = \Request::file('file');
        $extension = $image->getClientOriginalExtension();
        $originalName= $image->getClientOriginalName();
        $directory = 'gallery';
        $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
        $image= \Image::make($image);
        $image->resize(870,870, function ($constraint)
        {
            $constraint->aspectRatio();
        })->save($directory. '/' . $filename);
        \DB::table('gallery_img')->insert(['gallery_id' => $id, 'image' => $directory. '/' . $filename]); 
    }
    
    public static function doGetGallery($user)
    {
    	return \api::success(['data' => Gallery::where('school_id', $user->school_id)->get()]);
    }

    public function doUpdateGallery($user, $request)
    {
        $gallery = Gallery::where('school_id', $user->school_id)->where('event', $request['header'])->where('id', '!=', $request['id'])->first();
        if($gallery)
        {
            $input['error'] = 'Gallery is already exists';
            return \Redirect::back()->withInput($input);
        }
        if($request['date'])
        {
            $date = $request['date'];
        }
        else
        {
            $date = $request['old_date'];
        }
        $gallery_img = array();
        if(isset($request['files']))
        {
            foreach($request['files'] as $file)
            {
                if($file==null)
                {
                }
                else
                {
                    $imagerule = array('files' => 'required|image');
                    $image_Friendly = array('files' => 'Gallery Images');
                    $imagevalidator = \Validator::make(array('files'=> $file), $imagerule, $image_Friendly);
                    $imagevalidator->setAttributeNames($image_Friendly);
                    if($imagevalidator->fails())
                    {
                        return \Redirect::back()->withInput($input)->withErrors($imagevalidator);
                    }
                    else
                    {
                        $ex = $file->getClientOriginalExtension();
                        $orgName= $file->getClientOriginalName();
                        $destinationPath = 'gallery';
                        $docname = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;
                        $file= \Image::make($file);
                        $file->resize(700,null, function ($constraint)
                        {
                            $constraint->aspectRatio();
                        })->save($destinationPath. '/' . $docname);
                        array_push($gallery_img, $destinationPath. '/' . $docname);
                    }
                }
            }
        }

        Gallery::where('id', $request['id'])->update(['event' => $request['header'], 'date' => $date]);
        if($gallery_img)
        {
            foreach($gallery_img as $img)
            {
                \DB::table('gallery_img')->insert(['gallery_id' => $request['id'], 'image' => $img]); 
            }
        }

        $input['success'] = 'Gallery is updated successfully';
        return \Redirect::route('get.gallery')->withInput($input);
    }
}
