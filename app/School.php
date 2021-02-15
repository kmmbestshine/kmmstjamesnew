<?php

namespace App;
use Redirect, api, DB, Validator;
use Illuminate\Database\Eloquent\Model;

use Nicolaslopezj\Searchable\SearchableTrait;

class School extends Model
{
    use SearchableTrait; 

    protected $table = 'school';

    protected $searchable = [
        'columns' => [
            'school_name' => 50,
            'address' => 20,
            'city' => 10
        ],
    ];

    public static function postSchool($request)
    {
    	$check = School::where('email', $request['school_email'])->where('mobile', $request['school_mobile'])->first();
        if(!$check)
        {
            $file = $request['school_image'];
            $extension = $file->getClientOriginalExtension();
            $originalName= $file->getClientOriginalName();
            $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
            $file = \Image::make($file);
            $success = $file->resize(350,null, function ($constraint)
            {
                $constraint->aspectRatio();

            })->save('school/' . $filename);

            if($success)
            {
                $id = School::insertGetId([
                    'school_name' => $request['school_name'],
                    'email' => $request['school_email'],
                    'mobile' => $request['school_mobile'],
                    'address' => $request['school_address'],
                    'city' => $request['school_city'],
                    'userplan' => $request['userplan'],
                    'userplanAdded'=>$request['usermanualplan'],
                    'schoolcategory' => $request['Categorys'],
                    'schoolstatus' => $request['schoolstatus'],
                    'image' => 'school/'.$filename
                ]);
                $uname = '';
                $digit = mt_rand(10, 99);
                $length = strlen($id);
                if($length>1)
                {
                    $uname = $digit.$id.'Sch';
                }
                else
                {
                    $uname = $digit.'0'.$id.'Sch';
                }

                $user_id = \DB::table('users')->insertGetId([
                    'type' => 'school',
                    'school_id' => $id,
                    'username' => $uname,
                    'password' => \Hash::make($request['school_mobile']),
                    'hint_password' => $request['school_mobile'],
                    'status' =>$request['schoolstatus']
                ]);

                if($user_id){
                    School::where('id', $id)->update(['user_id' => $user_id]);

                }

                $message['success'] = 'School added Successfully';
                return \Redirect::route('viewSchool')->withInput($message);
            }
            else
            {
                $message['error'] = 'Image Upload Failed';
                return Redirect::back()->withInput($message);
            }
        }
        else
        {
            $message['exist'] = 'School Already Exists';
            return Redirect::back()->withInput($message);
        }
    }

    public function doUpdateSchool($request)
    {
        $check = School::where('email', $request['school_email'])->where('mobile', $request['school_mobile'])->where('id', '!=', $request['id'])->first();
        if($check)
        {
            $message['exist'] = 'School Already Exists';
            return Redirect::back()->withInput($message);
        }
        else
        {
            $school_check = School::where('id', $request['id'])->first();
            if($request['school_image'])
            {
                $file = $request['school_image'];
                $extension = $file->getClientOriginalExtension();
                $originalName= $file->getClientOriginalName();
                $filename = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $extension;
                $file = \Image::make($file);
                $success = $file->resize(350,null, function ($constraint)
                {
                    $constraint->aspectRatio();

                })->save('school/' . $filename);
                $filenm = 'school/'. $filename;
            }
            else
            {
                $filenm = $school_check->image;
            }

            $id =School::where('id', $request['id'])->update([
                    'school_name' => $request['school_name'],
                    'email' => $request['school_email'],
                    'mobile' => $request['school_mobile'],
                    'address' => $request['school_address'],
                    'city' => $request['school_city'],
                     'userplan' => $request['userplan'],
                    'userplanAdded'=>$request['usermanualplan'],
                    'schoolcategory' => $request['Categorys'],
                    'schoolstatus' => $request['schoolstatus'],
                    'image' => $filenm
            ]);
             if($id){
                     \DB::table('users')->where('school_id', $request['id'])->
                     update(['status' =>$request['schoolstatus']]);

                }
            $message['success'] = 'School is updated Successfully';
            return \Redirect::route('viewSchool')->withInput($message);
        }
    }

    public function doGetAllDetails($user)
    {
        $principal = \DB::table('principal')->where('school_id', $user->school_id)->first();
        $teachers = \DB::table('teacher')->where('teacher.school_id', $user->school_id)
                ->leftJoin('staff', 'teacher.type', '=', 'staff.id')
                ->leftJoin('class', 'teacher.class', '=', 'class.id')
                ->leftJoin('section', 'teacher.section', '=', 'section.id')
                ->select
                (
                    'teacher.id',
                    'teacher.name',
                    'teacher.email',
                    'teacher.mobile',
                    'class.class',
                    'section.section',
                    'staff.staff_type',
                    'teacher.avatar',
                    'teacher.created_at'
                )
                ->get();
        return \api::success(['principal' => $principal, 'teachers' => $teachers]);
    }

    public function doGetSchoolProfile($user)
    {
        $school = School::where('id', $user->school_id)->first();
        return \api::success(['data' => $school]);
    }

    public function doGetSchool($platform, $id)
    {
        $school = School::where('id', $id)->select('id', 'school_name', 'email', 'mobile', 'address', 'city', 'image')->first();
        return \api::success(['data' => $school]);
    }
}
