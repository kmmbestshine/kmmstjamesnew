<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use Event;
use App\Events\StudentCreationAlertMail;

class Students extends Model {

    use SearchableTrait;

    protected $table = 'student';
    protected $searchable = [
        'columns' => [
            'name' => 50,
            'registration_no' => 20
        ],
    ];

    public function hasManyResults() {
        return $this->hasMany('App\Result', 'student_id', 'id');
    }
    
    public function get_student_by_regNo($register_no,$school_id){
		return Students::where('school_id',$school_id)->where('registration_no',$register_no)->first();
	}

    public function getStudentClass($class_id,$section_id){
		$class = addClass::select('class')->where('id',$class_id)->first();
		$section =  Section::select('section')->where('id',$section_id)->first();
		return $class->class .'-'. $section->section;
	}
        
//    public function doPostStudent($request, $user) {
//        // Roll No Exist Return 
//        // dd(date('d-m-Y', strtotime($request['dob'])));
//        $roll_no_exist = Students::where('class_id', $request['class'])
//                ->where('section_id', $request['section'])
//                ->where('roll_no', $request['roll_no'])
//                ->where('school_id', \Auth::user()->school_id)
//                ->first();
//        if ($roll_no_exist) {
//            $input['error'] = 'Roll No Already Exists';
//            return \Redirect::back()->withInput($input);
//        }
//
//        // Registration No Exist Return
//        $reg_no_exist = Students::where('registration_no', $request['registration_no'])
//                ->where('school_id', \Auth::user()->school_id)
//                ->first();
//        if ($reg_no_exist) {
//            $input['error'] = 'Registration No Already Exists';
//            return \Redirect::back()->withInput($input);
//        }
//
//        if (isset($request['avatar'])) {
//            $image = $request['avatar'];
//            $extension = $image->getClientOriginalExtension();
//            $originalName = $image->getClientOriginalName();
//            $directory = 'student';
//            $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;
//            $image = \Image::make($image);
//            $image->resize(350, null, function ($constraint) {
//                $constraint->aspectRatio();
//            })->save($directory . '/' . $filename);
//            $avatarfile = $directory . '/' . $filename;
//        } else {
//            $avatarfile = 'student/default_avatar.png';
//        }
//
//        $parent = \DB::table('parent')->where('school_id', \Auth::user()->school_id)->where('mobile', $request['parent_contact_no'])->get();
//        // Request By Parent Id For Another Student
//        if (count($parent) > 0) {
//            foreach ($parent as $key => $value) {
//                $student = Students::where('parent_id', $value->id)
//                                ->where('school_id', \Auth::user()->school_id)->first();
//                $section_id = $student->section_id;
//                if ($section_id == $request['section']) {
//                    $input['error'] = 'parent contact number already exists in this section';
//                    return \Redirect::back()->withInput($input);
//                }
//            }
//        }
//        
//        // Make Student Unique Username By Trimming The Name
//        $length = strlen($request['name']);
//        $uname = '';
//        
//        $user_id = \DB::table('users')->insertGetId([
//            'type' => 'student',
//            'school_id' => \Auth::user()->school_id,
//            'password' => \Hash::make($request['parent_contact_no']),
//            'hint_password' => $request['parent_contact_no']
//        ]);
//
//        if ($user_id <= 100) {
//            $uname = 's00' . $user_id;
//        } else {
//            $uname = 's' . $user_id;
//        }
//
//        if ($user_id) {
//            \DB::table('users')->where('id', $user_id)->update(['username' => $uname]);
//        }
//
//        $request['contact_no'] = (isset($request['contact_no']) ? $request['contact_no'] : '');
//        $request['email'] = (isset($request['email']) ? $request['email'] : '');
//        $request['address'] = (isset($request['address']) ? $request['address'] : '');
//        $request['route'] = (isset($request['route']) ? $request['route'] : '0');
//        $request['stop'] = (isset($request['stop']) ? $request['stop'] : '0');
//        // Insert Student Info
//        $student_id = Students::insertGetId([
//                    'session_id' => $request['session_id'],
//                    'registration_no' => $request['registration_no'],
//                    'user_id' => $user_id,
//                    'section_id' => $request['section'],
//                    'class_id' => $request['class'],
//                    'school_id' => \Auth::user()->school_id,
//                    'roll_no' => $request['roll_no'],
//                    'registration_no' => $request['registration_no'],
//                    'name' => $request['name'],
//                    'dob' => date('d-m-Y', strtotime($request['dob'])),
//                    'date_of_admission' => date('d-m-Y', strtotime($request['date_of_admission'])),
//                    'date_of_joining' => date('d-m-Y', strtotime($request['date_of_joining'])),
//                    'gender' => $request['gender'],
//                    'caste_id' => $request['caste'],
//                    'blood_group' => $request['blood_group'],
//                    'religion' => $request['religion'],
//                    'bus_id' => $request['route'],
//                    'bus_stop_id' => $request['stop'],
//                    'contact_no' => $request['contact_no'],
//                    'avatar' => $avatarfile
//                       
//        ]);
//
//        if ($student_id) {
//            // Make Parent Unique Username By Trimming The father_name	
//            $length = strlen($request['father_name']);
//            $fname = '';
//            
//            $request['address'] = (isset($request['address']) ? $request['address'] : '');
//            $pId = \DB::table('parent')->insertGetId([
//                'school_id' => \Auth::user()->school_id,
//                'name' => $request['father_name'],
//                'mother' => $request['mother_name'],
//                'mobile' => $request['parent_contact_no'],
//                'address' => $request['address']
//                    
//            ]);
//
//            if ($pId) {
//                if ($pId <= 100) {
//                    $p_unq_id = 'p00' . $pId;
//                } else {
//                    $p_unq_id = 'p' . $pId;
//                }
//                // $p_unq_id = $fname.$pId."p";
//                $parent_id = \DB::table('users')->insertGetId([
//                    'type' => 'parent',
//                    'school_id' => \Auth::user()->school_id,
//                    'username' => $p_unq_id,
//                    'password' => \Hash::make($request['parent_contact_no']),
//                    'hint_password' => $request['parent_contact_no']
//                ]);
//
//                if ($parent_id) {
//                    \DB::table('parent')->where('id', $pId)->update(['user_id' => $parent_id]);
//                }
//                Students::where('id', $student_id)->update(['parent_id' => $pId]);
//            }
//        }
//        $input['success'] = 'Student Added successfully';
//        return \Redirect::back()->withInput($input);
//        /* } */
//    }
        
    public function doPostStudent($request, $user) {
        // Roll No Exist Return 
        // dd(date('d-m-Y', strtotime($request['dob'])));
        $roll_no_exist = Students::where('class_id', $request['class'])
                ->where('section_id', $request['section'])
                ->where('roll_no', $request['roll_no'])
                ->where('school_id', \Auth::user()->school_id)
                ->first();
        if ($roll_no_exist) {
            $input['error'] = 'Roll No Already Exists';
            return \Redirect::back()->withInput($input);
        }

        // Registration No Exist Return
        $reg_no_exist = Students::where('registration_no', $request['registration_no'])
                ->where('school_id', \Auth::user()->school_id)
                ->first();
        if ($reg_no_exist) {
            $input['error'] = 'Registration No Already Exists';
            return \Redirect::back()->withInput($input);
        }

        if (isset($request['avatar'])) {
            $image = $request['avatar'];
            $extension = $image->getClientOriginalExtension();
            $originalName = $image->getClientOriginalName();
            $directory = 'student';
            $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;
            $image = \Image::make($image);
            $image->resize(350, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($directory . '/' . $filename);
            $avatarfile = $directory . '/' . $filename;
        } else {
            $avatarfile = 'student/default_avatar.png';
        }

        // Parent Email Exist Return
        $parentEmail = \DB::table('parent')->where('school_id', \Auth::user()->school_id)->where('email', $request['parent_email'])->get();
        
        if (count($parentEmail) > 0) {
            foreach ($parent as $key => $value) {
                $student = Students::where('parent_id', $value->id)
                                ->where('school_id', \Auth::user()->school_id)->first();
                $section_id = $student->section_id;
                if ($section_id == $request['section']) {
                    $input['error'] = 'parent email already exists in this section';
                    return \Redirect::back()->withInput($input);
                }
            }
        }

        $parent = \DB::table('parent')->where('school_id', \Auth::user()->school_id)->where('mobile', $request['parent_contact_no'])->get();
        // Request By Parent Id For Another Student
        if (count($parent) > 0) {
            foreach ($parent as $key => $value) {
                $student = Students::where('parent_id', $value->id)
                                ->where('school_id', \Auth::user()->school_id)->first();
                $section_id = $student->section_id;
                if ($section_id == $request['section']) {
                    $input['error'] = 'parent contact number already exists in this section';
                    return \Redirect::back()->withInput($input);
                }
            }
        }
        
        // Make Student Unique Username By Trimming The Name
        $length = strlen($request['name']);
        $uname = '';
        
        $user_id = \DB::table('users')->insertGetId([
            'type' => 'student',
            'school_id' => \Auth::user()->school_id,
            'password' => \Hash::make($request['parent_contact_no']),
            'hint_password' => $request['parent_contact_no']
        ]);

        if ($user_id <= 100) {
            $uname = 's00' . $user_id;
        } else {
            $uname = 's' . $user_id;
        }

        if ($user_id) {
            \DB::table('users')->where('id', $user_id)->update(['username' => $uname]);
        }

        $request['contact_no'] = (isset($request['contact_no']) ? $request['contact_no'] : '');
        $request['email'] = (isset($request['email']) ? $request['email'] : '');
        $request['address'] = (isset($request['address']) ? $request['address'] : '');
        $request['route'] = (isset($request['route']) ? $request['route'] : '0');
        $request['stop'] = (isset($request['stop']) ? $request['stop'] : '0');
        // Insert Student Info
        $student_id = Students::insertGetId([
                    'session_id' => $request['session_id'],
                    'registration_no' => $request['registration_no'],
                    'user_id' => $user_id,
                    'section_id' => $request['section'],
                    'class_id' => $request['class'],
                    'school_id' => \Auth::user()->school_id,
                    'roll_no' => $request['roll_no'],
                    'registration_no' => $request['registration_no'],
                    'name' => $request['name'],
                    'dob' => date('d-m-Y', strtotime($request['dob'])),
                    'date_of_admission' => date('d-m-Y', strtotime($request['date_of_admission'])),
                    'date_of_joining' => date('d-m-Y', strtotime($request['date_of_joining'])),
                    'gender' => $request['gender'],
                    'caste_id' => $request['caste'],
                    'blood_group' => $request['blood_group'],
                    'religion' => $request['religion'],
                    'bus_id' => $request['route'],
                    'bus_stop_id' => $request['stop'],
                    'contact_no' => $request['contact_no'],
                    'avatar' => $avatarfile
                       
        ]);

        if ($student_id) {
            // Make Parent Unique Username By Trimming The father_name	
            $length = strlen($request['father_name']);
            $fname = '';
            
            $request['address'] = (isset($request['address']) ? $request['address'] : '');
            $pId = \DB::table('parent')->insertGetId([
                'school_id' => \Auth::user()->school_id,
                'name' => $request['father_name'],
                'mother' => $request['mother_name'],
                'mobile' => $request['parent_contact_no'],
                'address' => $request['address'],
                'email' => $request['parent_email']
            ]);

            if ($pId) {
                if ($pId <= 100) {
                    $p_unq_id = 'p00' . $pId;
                } else {
                    $p_unq_id = 'p' . $pId;
                }
                // $p_unq_id = $fname.$pId."p";
                $parent_id = \DB::table('users')->insertGetId([
                    'type' => 'parent',
                    'school_id' => \Auth::user()->school_id,
                    'username' => $p_unq_id,
                    'password' => \Hash::make($request['parent_contact_no']),
                    'hint_password' => $request['parent_contact_no']
                ]);

                if ($parent_id) {
                    \DB::table('parent')->where('id', $pId)->update(['user_id' => $parent_id]);
                }
                Students::where('id', $student_id)->update(['parent_id' => $pId]);
            }
        }

        // changes done by parthiban version3
        // send email

        $info = [];
        $info['SUN'] = $uname;
        $info['PUN'] = $p_unq_id;
        $info['PWD'] = $request['parent_contact_no'];
        $info['EMAIL'] = $request['parent_email'];

        Event::fire(new StudentCreationAlertMail($info));

        $input['success'] = 'Student Added successfully';
        return \Redirect::back()->withInput($input);
        /* } */
    }        

    public function getStudents($class, $section) {
        $students = Students::where('student.class_id', $class)
                ->where('student.section_id', $section)
                ->where('student.deleted_at', null)
                ->join('class', 'student.class_id', '=', 'class.id')
                ->join('section', 'student.section_id', '=', 'section.id')
                ->join('session', 'student.session_id', '=', 'session.id')
                ->join('parent', 'student.parent_id', '=', 'parent.id')
                ->join('caste', 'student.caste_id', '=', 'caste.id')
                ->join('blood_group', 'student.blood_group', '=', 'blood_group.id')
                ->join('religion', 'student.religion', '=', 'religion.id')
                ->select
                        (
                        'student.id', 'student.name', 'student.avatar', 'parent.name as father', 'parent.mother', 'parent.mobile', 'parent.email', 'parent.address', 'parent.city', 'student.dob', 'student.roll_no', 'student.registration_no', 'student.date_of_admission', 'student.date_of_joining', 'class.id as classId', 'class.class as classValue', 'section.id as sectionId', 'section.section as sectionValue', 'session.id as sessionId', 'session.session as sessionValue', 'caste.caste', 'blood_group.blood_group', 'religion.religion'
                )
                ->get();
        if (!$students)
            return api()->notValid(['errorMsg' => 'notFound']);
        return api(['data' => $students]);
    }

    public function doUpdateStudent($request, $user) {
        $roll_no_exist = Students::where('class_id', $request['class'])->where('section_id', $request['section'])->where('roll_no', $request['roll_no'])->where('id', '!=', $request['id'])->first();
        if ($roll_no_exist) {
            $input['error'] = 'Roll No already exists';
            return \Redirect::back()->withInput($input);
        }

        $reg_no_exist = Students::where('registration_no', $request['registration_no'])->where('id', '!=', $request['id'])->where('school_id', $user->school_id)->first();
        if ($reg_no_exist) {
            $input['error'] = 'Registration No already exists';
            return \Redirect::back()->withInput($input);
        }
        $student = Students::where('id', $request['id'])->first();
        if ($student) {
            if (isset($request['route'])) {
                $bus = $request['route'];
            } else {
                $bus = 0;
            }
            if (isset($request['stop'])) {
                $stop = $request['stop'];
            } else {
                $stop = 0;
            }
            
            if(!empty($request['parentPassword'])){
                
                $student = Students::where('id', $request['id'])->first();
                $parent = \DB::table('parent')->where('id', $student->parent_id)->first();
                $obj_user = User::find($parent->user_id);
                $obj_user->hint_password = $request['parentPassword'];
                $obj_user->password = \Hash::make($request['parentPassword']);
                $obj_user->save(); 
            }
            
            if(!empty($request['studentPassword'])){
                
                $student = Students::where('id', $request['id'])->first();
                $obj_user = User::find($student->user_id);
                $obj_user->hint_password = $request['studentPassword'];
                $obj_user->password = \Hash::make($request['studentPassword']);
                $obj_user->save(); 
            }

            if (isset($request['avatar'])) {
                $image = $request['avatar'];
                $extension = $image->getClientOriginalExtension();
                $originalName = $image->getClientOriginalName();
                $directory = 'student';
                $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;
                $image = \Image::make($image);
                $image->resize(350, null, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($directory . '/' . $filename);
                $avatarfile = $directory . '/' . $filename;
            } else {
                $avatarfile = $student->avatar;
            }

            
            $update_result = Students::where('id', $request['id'])->update([
                'session_id' => $request['session_id'],
                'registration_no' => $request['registration_no'],
                'section_id' => $request['section'],
                'class_id' => $request['class'],
                'roll_no' => $request['roll_no'],
                'registration_no' => $request['registration_no'],
                'name' => $request['name'],
                'dob' => date('d-m-Y', strtotime($request['dob'])),
                'date_of_admission' => $request['date_of_admission'],
                'date_of_joining' => $request['date_of_joining'],
                'gender' => $request['gender'],
                'caste_id' => $request['caste'],
                'blood_group' => $request['blood_group'],
                'religion' => $request['religion'],
                'bus_id' => $bus,
                'bus_stop_id' => $stop,
                'contact_no' => $request['contact_no'],
                'avatar' => $avatarfile
                   
            ]);

            $student = Students::where('id', $request['id'])->first();
            \DB::table('parent')->where('id', $student->parent_id)->update([
                'name' => $request['father_name'],
                'mother' => $request['mother_name'],
                'mobile' => $request['parent_contact_no'],
                'address' => $request['address']
                    
            ]);

            $parent = \DB::table('parent')->where('school_id', \Auth::user()->school_id)->where('mobile', $request['parent_contact_no'])->get();
            // Request By Parent Id For Another Student
            if (count($parent) > 0) {
                foreach ($parent as $key => $value) {
                    $student = Students::where('parent_id', $value->id)
                                    ->where('school_id', \Auth::user()->school_id)->first();
                    $section_id = $student->section_id;
                    if ($section_id == $request['section']) {
                        if (!$update_result) {
                            $input['error'] = 'duplicate parent contact number';
                            return \Redirect::back()->withInput($input);
                        } else {
                            $input['success'] = 'student update done ,but not accept duplicate parent contact number';
                            return \Redirect::back()->withInput($input);
                        }
                    }
                }
            }


            $input['success'] = 'Student updated successfully';
            return \Redirect::back()->withInput($input);
        } else {
            $input['error'] = 'Invalid Student';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doImportStudent($user, $request) {
        $Accounts = array();
        $objPHPExcel = \PHPExcel_IOFactory::load($request['file']);
        $obj = $objPHPExcel->getActiveSheet();

        if ($obj->getCellByColumnAndRow(0, 1)->getValue() != 'session_id' &&
                $obj->getCellByColumnAndRow(1, 1)->getValue() != 'class_id' &&
                $obj->getCellByColumnAndRow(2, 1)->getValue() != 'section_id' &&
                $obj->getCellByColumnAndRow(3, 1)->getValue() != 'religion_id' &&
                $obj->getCellByColumnAndRow(4, 1)->getValue() != 'caste_id' &&
                $obj->getCellByColumnAndRow(5, 1)->getValue() != 'registration_no' &&
                $obj->getCellByColumnAndRow(6, 1)->getValue() != 'roll_no' &&
                $obj->getCellByColumnAndRow(7, 1)->getValue() != 'name' &&
                $obj->getCellByColumnAndRow(8, 1)->getValue() != 'gender' &&
                $obj->getCellByColumnAndRow(9, 1)->getValue() != 'blood_group' &&
                $obj->getCellByColumnAndRow(10, 1)->getValue() != 'dob' &&
                $obj->getCellByColumnAndRow(11, 1)->getValue() != 'date_of_admission' &&
                $obj->getCellByColumnAndRow(12, 1)->getValue() != 'date_of_joining' &&
                $obj->getCellByColumnAndRow(13, 1)->getValue() != 'father_name' &&
                $obj->getCellByColumnAndRow(14, 1)->getValue() != 'mother_name' &&
                $obj->getCellByColumnAndRow(15, 1)->getValue() != 'parent_contact_no' &&
                $obj->getCellByColumnAndRow(16, 1)->getValue() != 'parent_email' &&
                $obj->getCellByColumnAndRow(17, 1)->getValue() != 'contact_no' &&
                $obj->getCellByColumnAndRow(18, 1)->getValue() != 'address' &&
                $obj->getCellByColumnAndRow(19, 1)->getValue() != 'bus_id') {
            $msg['error'] = 'Data is not according to format';
            return \Redirect::back()->withInput($msg);
        }

        $rows = $obj->getHighestRow();

        $row = 1;

        $Iterator = 0;

        for (((($obj->getCellByColumnAndRow(0, $row)->getValue()) == 'session_id') ? $row = 2 : $row = 1); $row <= $rows; ++$row) {

            $Accounts[$Iterator] = array(
                'session_id' => $obj->getCellByColumnAndRow(0, $row)->getValue(),
                'class_id' => $obj->getCellByColumnAndRow(1, $row)->getValue(),
                'section_id' => $obj->getCellByColumnAndRow(2, $row)->getValue(),
                'religion_id' => $obj->getCellByColumnAndRow(3, $row)->getValue(),
                'caste_id' => $obj->getCellByColumnAndRow(4, $row)->getValue(),
                'registration_no' => $obj->getCellByColumnAndRow(5, $row)->getValue(),
                'roll_no' => $obj->getCellByColumnAndRow(6, $row)->getValue(),
                'name' => $obj->getCellByColumnAndRow(7, $row)->getValue(),
                'gender' => $obj->getCellByColumnAndRow(8, $row)->getValue(),
                'blood_group' => $obj->getCellByColumnAndRow(9, $row)->getValue(),
                'dob' => $obj->getCellByColumnAndRow(10, $row)->getValue(),
                'date_of_admission' => $obj->getCellByColumnAndRow(11, $row)->getValue(),
                'date_of_joining' => $obj->getCellByColumnAndRow(12, $row)->getValue(),
                'father_name' => $obj->getCellByColumnAndRow(13, $row)->getValue(),
                'mother_name' => $obj->getCellByColumnAndRow(14, $row)->getValue(),
                'parent_contact_no' => $obj->getCellByColumnAndRow(15, $row)->getValue(),
                'parent_email' => $obj->getCellByColumnAndRow(16, $row)->getValue(),
                'contact_no' => $obj->getCellByColumnAndRow(17, $row)->getValue(),
                'address' => $obj->getCellByColumnAndRow(18, $row)->getValue(),
                'bus_id' => $obj->getCellByColumnAndRow(19, $row)->getValue()

            );


            foreach ($Accounts as $key => $value) {
                
                if ($value['name'] == '' && $value['registration_no'] == '' && $value['roll_no'] == '' && $value['parent_contact_no'] == '' && $value['class_id'] == '' && $value['section_id'] == '') {
                    unset($Accounts[$key]);
                    unset($value);
                    break;
                }
                $not_mandatary = array('contact_no','address','bus_id');
                foreach($value as $keys => $val){
                    if(!in_array($keys, $not_mandatary)){
                        if(empty($val)){
                            $msg['error'] = 'At Row : ' . $row .' '. $keys . ' required';
                            return \Redirect::back()->withInput($msg);
                        }
                    }
                }
            }

            foreach ($Accounts as $key => $val) {
                
                $parentsEmail = \DB::table('parent')->where('school_id', \Auth::user()->school_id)->where('email', $val['parent_email'])->get();

                // Request By Parent Email For Another Student
                if (count($parentsEmail) > 0) {
                    foreach ($parentsEmail as $parent_key => $parent_value) {

                        $student = Students::where('parent_id', $parent_value->id)
                                        ->where('school_id', \Auth::user()->school_id)->first();
                        $section_id = $student->section_id;
                        if ($section_id == $val['section_id']) {
                            $msg['error'] = 'At Row : ' . $row . ' parent email already exists in this section';
                            return \Redirect::back()->withInput($msg);
                        }
                    }
                }
                
                $parents = \DB::table('parent')->where('school_id', \Auth::user()->school_id)->where('mobile', $val['parent_contact_no'])->get();

                // Request By Parent Id For Another Student
                if (count($parents) > 0) {
                    foreach ($parents as $parent_key => $parent_value) {

                        $student = Students::where('parent_id', $parent_value->id)
                                        ->where('school_id', \Auth::user()->school_id)->first();
                        $section_id = $student->section_id;
                        if ($section_id == $val['section_id']) {
                            $msg['error'] = 'At Row : ' . $row . ' parent contact number already exists in this section';
                            return \Redirect::back()->withInput($msg);
                        }
                    }
                }
                // Roll no Check
                $roll_no_exist = \DB::table('student')->where('school_id', \Auth::user()->school_id)->where('roll_no', $val['roll_no'])->where('class_id', $val['class_id'])->where('section_id', $val['section_id'])->first();
                if ($roll_no_exist) {
                    $msg['error'] = 'At Row : ' . $row . ' Roll No already exists';
                    return \Redirect::back()->withInput($msg);
                }
                // Registration No Check
                $registration_exist = \DB::table('student')->where('school_id', \Auth::user()->school_id)->where('registration_no', $val['registration_no'])->first();
                if ($registration_exist) {
                    $msg['error'] = 'At Row : ' . $row . ' Registration No. already exists';
                    return \Redirect::back()->withInput($msg);
                }

                $parent_exsit = \DB::table('parent')->where('school_id', \Auth::user()->school_id)
                                ->where('mobile', $val['parent_contact_no'])->first();
                $pId = $parent_exsit->id;
                if (!$parent_exsit) {
                    $length = strlen($val['father_name']);
                    $fname = '';
                    if ($length > 5) {
                        $fname = str_slug(substr(str_replace(" ", "", $val['father_name']), 0, 5));
                    } else {
                        $fname = str_slug(str_replace(" ", "", $val['father_name']));
                    }
                    $pId = \DB::table('parent')->insertGetId([
                        'school_id' => \Auth::user()->school_id,
                        'name' => $val['father_name'],
                        'mother' => $val['mother_name'],
                        'mobile' => $val['parent_contact_no'],
                        'email' => $val['parent_email'],
                        'address' => $val['address'],
                        'avatar' => 'parent/default_avatar.png'
                        
                    ]);
                    $parent_user_id = \DB::table('users')->insertGetId([
                        'type' => 'parent',
                        'school_id' => \Auth::user()->school_id,
                        'username' => $fname . $pId . "p",
                        'password' => \Hash::make($val['parent_contact_no']),
                        'hint_password' => $val['parent_contact_no']
                    ]);
                    if ($parent_user_id) {
                        \DB::table('parent')->where('id', $pId)->update(['user_id' => $parent_user_id]);
                    }
                }
                $user_id = \DB::table('users')->insertGetId([
                    'type' => 'student',
                    'school_id' => \Auth::user()->school_id,
                    'password' => \Hash::make($val['parent_contact_no']),
                    'hint_password' => $val['parent_contact_no']
                ]);

                if ($user_id) {
                    \DB::table('users')->where('id', $user_id)->update(['username' => $uname . "" . $user_id . "s"]);
                }

                $val['contact_no'] = (isset($val['contact_no']) ? $val['contact_no'] : '');
                $val['address'] = (isset($val['address']) ? $val['address'] : '');
                $val['bus_id'] = (isset($val['bus_id']) ? $val['bus_id'] : '0');
               /*
				*Updated 22-9-2017 
				$dob = date('d-m-Y', \PHPExcel_Shared_Date::ExcelToPHP($val['dob']));
                $date_of_admission = date('d-m-Y', \PHPExcel_Shared_Date::ExcelToPHP($val['date_of_admission']));
                $date_of_joining = date('d-m-Y', \PHPExcel_Shared_Date::ExcelToPHP($val['date_of_joining']));
                */
				$dob = date('d-m-Y', strtotime(\PHPExcel_Shared_Date::ExcelToPHP($val['dob'])));
                $date_of_admission = date('d-m-Y', strtotime(\PHPExcel_Shared_Date::ExcelToPHP($val['date_of_admission'])));
                $date_of_joining = date('d-m-Y', strtotime(\PHPExcel_Shared_Date::ExcelToPHP($val['date_of_joining'])));
                /************/
                $insert_stu = [
                    'session_id' => (int) $val['session_id'],
                    'class_id' => (int) $val['class_id'],
                    'section_id' => (int) $val['section_id'],
                    'religion' => (int) $val['religion_id'],
                    'caste_id' => (int) $val['caste_id'],
                    'registration_no' => $val['registration_no'],
                    'roll_no' => (int) $val['roll_no'],
                    'name' => $val['name'],
                    'gender' => $val['gender'],
                    'blood_group' => $val['blood_group'],
                    'dob' => $dob,
                    'date_of_admission' => $date_of_admission,
                    'date_of_joining' => $date_of_joining,
                    'contact_no' => $val['contact_no'],
                    'bus_id' => (int) $val['bus_id'],
                    'user_id' => $user_id,
                    'school_id' => $user->school_id,
                    'parent_id' => $pId,
                    'avatar' => 'student/default_avatar.png'
                ];
                $student_id = Students::insertGetId($insert_stu);
                
                // send email
                if($student_id){
                    $info = [];
                    $info['SUN'] = $uname . "" . $user_id . "s";
                    $info['PUN'] = $fname . $pId . "p";
                    $info['PWD'] = $val['parent_contact_no'];
                    $info['EMAIL'] = $val['parent_email'];

                    Event::fire(new StudentCreationAlertMail($info));
                }
            }
        }

        $msg['success'] = 'Students is added successfully';
        return \Redirect::back()->withInput($msg);
    }

}
