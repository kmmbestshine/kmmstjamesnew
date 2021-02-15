<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StuParent extends Model
{
    protected $table = 'parent';
    
    public function doGetStudentsByParent($user, $platform)
    {
    	$parent = \DB::table('parent')->where('user_id', $user->id)->first();
        $getStudents = Students::where('student.parent_id', $parent->id)
                    ->leftJoin('class', 'student.class_id', '=', 'class.id')
                    ->leftJoin('section', 'student.section_id', '=', 'section.id')
                    ->leftJoin('session', 'student.session_id', '=', 'session.id')
                    ->leftJoin('parent', 'student.parent_id', '=', 'parent.id')
                    ->select
                    (
                        'student.id',
                        'student.name',
                        'student.avatar',
                        'student.roll_no',
                        'parent.name as father',
                        'parent.mother',
                        'parent.mobile',
                        'parent.email',
                        'parent.address',
                        'parent.city',
                        'parent.pin_code',
                        'class.class',
                        'class.id as class_id',
                        'section.section',
                        'section.id as section_id',
                        'session.session',
                        'student.dob',
                        'student.roll_no'
                    )
                    ->get();

        foreach($getStudents as $stu)
        {
            $subjects = \DB::table('subject')->where('class_id', $stu->class_id)->where('section_id', $stu->section_id)->select('subject')->get();

            $students[] = array(
                            'id' => $stu->id,
                            'name' => $stu->name,
                            'image' => $stu->avatar, 
                            'roll_no' => $stu->roll_no, 
                            'father_name' => $stu->father,
                            'mother_name' => $stu->mother,
                            'mobile' => $stu->mobile,
                            'email' => $stu->email,
                            'address' => $stu->address,
                            'city' => $stu->city,
                            'pinCode' => $stu->pin_code,
                            'class' => $stu->class,
                            'section' => $stu->section,
                            'session' => $stu->session,
                            'subjects' => $subjects
                        );
        }
        return \api::success(['data' => $students]);
    }
}