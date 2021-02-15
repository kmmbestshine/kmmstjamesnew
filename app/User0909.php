<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function loginCheck($request)
    {
        if (\Auth::attempt(['username' => $request['username'], 'password' => $request['password']]))
        {
            if(\Auth::user()->type == 'admin')
            {
                return \Redirect::route('admin.dashboard');    
            }
            if(\Auth::user()->type == 'school')
            {
                return \Redirect::route('user.dashboard');
            }
            if(\Auth::user()->type == 'teacher')
            {
                \Auth::logout();
                return 'you are not allowed in web view';
            }
            if(\Auth::user()->type == 'student')
            {
                \Auth::logout();
                return 'you are not allowed in web view';
            }
            if(\Auth::user()->type == 'parent')
            {
                \Auth::logout();
                return 'you are not allowed in web view';
            }
            if(\Auth::user()->type == 'user_role')
            {
                return \Redirect::route('user.dashboard');
            }      
        }
        else
        {
            $errorlogin['message'] = "Username/Password Invalid";
            return \Redirect::back()->withInput($errorlogin);
        }

    }


    public static function input($request)
    {
        $password = substr(md5(microtime()),rand(0,9),6);
        $length = strlen($request['name']);
        $uname = '';    
        if($length>5)
        {   
            $uname = str_slug(substr(str_replace(" ", "", $request['name']), 0, 5));

        }
        else
        {
            $uname = str_slug(str_replace(" ", "", $request['name']));
        }
        
        $id = \DB::table('principal')->insertGetId([
            'name'=>$request['name'],
            'email'=>$request['email'],
            'mobile'=>$request['mobile'],
            'school_id'=>$request['school']
            
        ]);
        
        if($id)
        {
	       	$uid = User::insertGetId([
        		'type'=>'principal',
                'school_id'=>$request['school'],
        		'username' => $uname,
			    'password'=>\Hash::make($password),
		        'hint_password'=>$password,
        	]);
        	
        	if($uid)
        	{
        		\DB::table('principal')->where('id', $id)->update(['user_id' => $uid]);
        		User::where('id', $uid)->update(['username' => $uname.$uid."p"]);
        	}
        }

        return \Redirect::route('createAccount');
    }

    public function doChangePassPost($request, $user)
    {
        $users = User::where('id', $user->id)->first();
            if($users->hint_password == $request['old_pass'])
            {
                User::where('id', $users->id)->update([
                    'password'=>\Hash::make($request['new_pass']),
                    'hint_password'=>$request['new_pass'] 
                ]);
                return \api::success(['data' => 'Password Changed Successfully']);
            }
            else
            {
                return \api::notValid(['errorMsg' => 'Old Password is wrong']);
            }
    }

    public function doGetUserProfile($user)
    {
        $gallery = Gallery::with('hasManyImages')->where('school_id', $user->school_id)->orderBy('id', 'DESC')->first();

        if(\Auth::user()->type == 'teacher')
        {
            $profile = \DB::table('teacher')->where('teacher.user_id', $user->id)
                    ->leftJoin('class', 'teacher.class', '=', 'class.id')
                ->leftJoin('section', 'teacher.section', '=', 'section.id')
                ->leftJoin('staff', 'teacher.type', '=', 'staff.id')
                ->leftJoin('post', 'teacher.school_id', '=', 'post.school_id')
                ->select
                (
                    'teacher.id', 
                    'teacher.name', 
                    'teacher.mobile', 
                    'teacher.email', 
                    'teacher.avatar', 
                    'class.class', 
                    'section.section', 
                    'staff.staff_type', 
                    'post.image as school_avatar',
                    \DB::raw("(select username from users where id=teacher.user_id) as username"),
                    \DB::RAW("(select title from push_notification where role='teacher' and role_id=teacher.id ORDER BY push_notification.id DESC LIMIT 1) as notification_title"),
                    \DB::RAW("(select description from push_notification where role='teacher' and role_id=teacher.id ORDER BY push_notification.id DESC LIMIT 1) as notification_description"),
                    \DB::RAW("(select image from push_notification where role='teacher' and role_id=teacher.id ORDER BY push_notification.id DESC LIMIT 1) as notification_image")
                )
                ->first();
        }
        if(\Auth::user()->type == 'student')
        {
            $profile = \DB::table('student')->where('student.user_id', $user->id)
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')
                    ->leftJoin('session', 'student.session_id', '=', 'session.id')
                    ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                    ->leftJoin('attendance', 'student.id', '=', 'attendance.student_id')
                    ->leftJoin('post', 'student.school_id', '=', 'post.school_id')
                    ->select
                    (
                        'student.id', 
                        'student.name', 
                        'student.dob', 
                        'student.roll_no',
                        'student.registration_no',
                        'student.avatar', 
                        'class.class', 
                        'section.section', 
                        'session.session', 
                        'parent.name as father_name', 
                        'parent.mother as mother_name', 
                        'parent.mobile as parent_contact_no', 
                        'parent.email as parent_email', 
                        'parent.address', 
                        'parent.city', 
                        'post.image as school_avatar',
                        \DB::raw("(select username from users where id=student.user_id) as username"),
                        \DB::raw("(select attendance from attendance where date='".date('d-m-Y')."' and student_id=student.id) as attendance"),
                        \DB::RAW("(select title from push_notification where role='student' and role_id=student.id ORDER BY id DESC LIMIT 1) as notification_title"),
                        \DB::RAW("(select description from push_notification where role='student' and role_id=student.id ORDER BY id DESC LIMIT 1) as notification_description"),
                        \DB::RAW("(select image from push_notification where role='student' and role_id=student.id ORDER BY id DESC LIMIT 1) as notification_image")
                    )
                    ->first();
        }
        if(\Auth::user()->type == 'parent')
        {
            $profile = \DB::table('parent')->where('parent.user_id', $user->id)
                        ->leftJoin('post', 'parent.school_id', '=', 'post.school_id')
                        ->select
                        (
                            'parent.id', 
                            'parent.name', 
                            'parent.mother', 
                            'parent.email', 
                            'parent.mobile', 
                            'parent.address', 
                            'parent.city', 
                            'parent.avatar', 
                            'parent.pin_code', 
                            'post.image as school_avatar',
                            \DB::raw("(select username from users where id=parent.user_id) as username"),
                            \DB::RAW("(select title from push_notification where role='parent' and role_id=parent.id ORDER BY id DESC LIMIT 1) as notification_title"),
                            \DB::RAW("(select description from push_notification where role='parent' and role_id=parent.id ORDER BY id DESC LIMIT 1) as notification_description"),
                            \DB::RAW("(select image from push_notification where role='parent' and role_id=parent.id ORDER BY id DESC LIMIT 1) as notification_image")
                        )
                        ->first();
        }   
        return \api::success(['data' => $profile, 'gallery' => $gallery]);
    }

    public function createAuth($request)
    {

        if (\Auth::attempt(['username' => $request['username'], 'password' => $request['password']]))
        {
            dd($request->all());
            return \Redirect::route('users.dashboard');    
        }
        else
        {
            $errorlogin['error']="Username/Password Invalid";
            return \Redirect::route('login')->withInput($errorlogin);
        }
    }
}
