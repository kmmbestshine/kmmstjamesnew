<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Event;
use App\Events\EmployeeCreationAlertMail;

class Employee extends Model {

    protected $table = 'teacher';

    public function doGetEmployee($user) {
        $employees = Employee::where('teacher.school_id', $user->school_id)
                ->leftJoin('staff', 'teacher.type', '=', 'staff.id')
                ->leftJoin('class', 'teacher.class', '=', 'class.id')
                ->leftJoin('section', 'teacher.section', '=', 'section.id')
                ->leftJoin('users', 'teacher.user_id', '=', 'users.id')
                ->select
                        (
                        'teacher.id', 'staff.staff_type', 'class.class', 'section.section', 'teacher.name', 'users.username', 'users.hint_password', 'teacher.mobile', 'teacher.email', 'teacher.avatar'
                )
                ->get();
        return view('users.employee.list', compact('employees'));
    }

    public function doPostEmployee($request, $user) {
        $type = \DB::table('staff')->where('id', $request['type'])->first();
        //check if teaching staff
        if ($type->staff_type == 'Teaching Staff') {
            if (empty($request['class']) || empty($request['section'])) {
                $request['error'] = 'select class and section';
                return \Redirect::back()->withInput($request);
            }
        } else {
            $request['class'] = '';
            $request['section'] = '';
        }
        $check = Employee::where('mobile', $request['mobile'])->first();
        if (!$check) {
            $password = substr(md5(microtime()), rand(0, 9), 6);
            $length = strlen($request['name']);
            $uname = '';
            // if($length>5)
            // {   
            //     $uname = str_slug(substr(str_replace(" ", "", $request['name']), 0, 5));
            // }
            // else
            // {
            //     $uname = str_slug(str_replace(" ", "", $request['name']));
            // }
            $uid = User::insertGetId([
                        'type' => 'teacher',
                        'school_id' => $user->school_id,
                        'password' => \Hash::make($request['mobile']),
                        'hint_password' => $request['mobile']
            ]);

            if ($uid) {
                if ($uid <= 100) {
                    $uname = 'T00' . $uid;
                } else {
                    $uname = 'T' . $uid;
                }
                if (isset($request['image'])) {
                    $image = $request['image'];
                    $extension = $image->getClientOriginalExtension();
                    $originalName = $image->getClientOriginalName();
                    $directory = 'employee';
                    $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;
                    $image = \Image::make($image);
                    $image->resize(350, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($directory . '/' . $filename);
                    $file = $directory . '/' . $filename;
                } else {
                    $file = 'employee/default_avatar.png';
                }
                User::where('id', $uid)->update(['username' => $uname . $uid . "t"]);
                $request['section'] = (isset($request['section']) ? $request['section'] : "0");
                $request['class'] = (isset($request['class']) ? $request['class'] : "0");
                Employee::insert([
                    'user_id' => $uid,
                    'type' => $request['type'],
                    'class' => $request['class'],
                    'name' => $request['name'],
                    'mobile' => $request['mobile'],
                    'email' => $request['email'],
                    'section' => $request['section'],
                    'avatar' => $file,
                    'school_id' => $user->school_id,
                    'salary' => $request['salary']
                ]);
            }
            
            // send email to employee
            $info = [];
            $info['UN'] = $uname . $uid . "t";
            $info['PWD'] = $request['mobile'];
            $info['EMAIL'] = $request['email'];
       
            Event::fire(new EmployeeCreationAlertMail($info));
            
            $request['success'] = 'Employee added succesfully';
            return \Redirect::back()->withInput($request);
        } else {
            $request['error'] = 'Employee already exisis';
            return \Redirect::back()->withInput($request);
        }
    }

    public function imageUpload($request, $user) {
        $image = $request['image'];
        try {

            //get extension of file
            $extension = $image->getClientOriginalExtension();

            $originalName = $image->getClientOriginalName();

            //define directory to store images
            $directory = 'images/employee';

            //change filename to a random sha1 plus current time
            $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;


            $image = \Image::make($image);
            $image->resize(350, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($directory . '/' . $filename);



            return api(['data' => $filename]);


            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function doUpdateEmployee($request, $user) {
        $type = \DB::table('staff')->where('id', $request['type'])->first();
        //check if teaching staff
        if ($type->staff_type == 'Teaching Staff') {
            if (empty($request['class']) || empty($request['section'])) {
                $request['error'] = 'select class and section';
                return \Redirect::back()->withInput($request);
            }
        } else {
            $request['class'] = '';
            $request['section'] = '';
        }

        $check = Employee::where('mobile', $request['mobile'])->where('id', '!=', $request['id'])->first();
        if (!$check) {
            $employee = Employee::where('id', $request['id'])->first();
            if ($employee) {
                if (isset($request['image'])) {
                    $image = $request['image'];
                    $extension = $image->getClientOriginalExtension();
                    $originalName = $image->getClientOriginalName();
                    $directory = 'employee';
                    $filename = substr(str_shuffle(sha1(rand(3, 300) . time())), 0, 10) . "." . $extension;
                    $image = \Image::make($image);
                    $image->resize(350, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })->save($directory . '/' . $filename);
                    $file = $directory . '/' . $filename;
                } else {
                    $file = $employee->avatar;
                }
                if(!empty($request['employeePassword'])){
                
                    
                    $obj_user = User::find($employee->user_id);
                    $obj_user->hint_password = $request['employeePassword'];
                    $obj_user->password = \Hash::make($request['employeePassword']);
                    $obj_user->save(); 
                }
                Employee::where('id', $request['id'])->update([
                    'type' => $request['type'],
                    'class' => $request['class'],
                    'name' => $request['name'],
                    'mobile' => $request['mobile'],
                    'email' => $request['email'],
                    'section' => $request['section'],
                    'avatar' => $file,
                    'salary' => $request['salary']
                ]);
                $input['success'] = 'Employee updated succesfully';
                return \Redirect::back()->withInput($input);
            } else {
                $input['error'] = 'Employee not exist';
                return \Redirect::back()->withInput($input);
            }
        } else {
            $input['error'] = 'Employee already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doDeleteEmployee($platform, $id) {
        Employee::where('id', $id)->delete();
        return api(['data' => 'Employee deleted succesfully']);
    }

    public function doImportEmployee($user, $request) {
        $Accounts = array();
        $objPHPExcel = \PHPExcel_IOFactory::load($request['file']);
        $obj = $objPHPExcel->getActiveSheet();
        if ($obj->getCellByColumnAndRow(0, 1)->getValue() != 'staff_type_id' &&
                $obj->getCellByColumnAndRow(1, 1)->getValue() != 'name' &&
                $obj->getCellByColumnAndRow(2, 1)->getValue() != 'contact_no' &&
                $obj->getCellByColumnAndRow(4, 1)->getValue() != 'class_id' &&
                $obj->getCellByColumnAndRow(5, 1)->getValue() != 'section_id') {
            $input['error'] = 'Data is not according to format';
            return \Redirect::back()->withInput($input);
        }
        $rows = $obj->getHighestRow();

        $row = 1;

        $Iterator = 0;
        for (((($obj->getCellByColumnAndRow(0, $row)->getValue()) == 'staff_type_id') ? $row = 2 : $row = 1); $row <= $rows; ++$row) {
            $staff_type_id = $obj->getCellByColumnAndRow(0, $row)->getValue();
            $class_id = $obj->getCellByColumnAndRow(4, $row)->getValue();
            $section_id = $obj->getCellByColumnAndRow(5, $row)->getValue();
            $teaching_id = \DB::table('staff')->where('id', $staff_type_id)->first();
            if ($teaching_id->staff_type == 'Teaching Staff') {
                if (empty($class_id) || empty($section_id)) {
                    $input['error'] = 'At Row : ' . $row . ' add class and section';
                    return \Redirect::back()->withInput($input);
                }
            } else {
                $class_id = '';
                $section_id = '';
            }
            $Accounts[$Iterator] = array(
                'staff_type_id' => $staff_type_id,
                'name' => $obj->getCellByColumnAndRow(1, $row)->getValue(),
                'contact_no' => $obj->getCellByColumnAndRow(2, $row)->getValue(),
                'email' => $obj->getCellByColumnAndRow(3, $row)->getValue(),
                'class_id' => $class_id,
                'section_id' => $section_id,
            );

            /*
              foreach ($obj->getDrawingCollection() as $drawing) {
              if ($drawing instanceof \PHPExcel_Worksheet_Drawing)
              {
              $cellID = $drawing->getCoordinates();
              if($cellID == \PHPExcel_Cell::stringFromColumnIndex(5).$row)
              {
              $filename = $drawing->getPath();
              $ex = $drawing->getExtension();
              $drawing->getDescription();
              $new_file = substr(str_shuffle(sha1(rand(3,300).time())), 0, 10) . "." . $ex;

              copy($filename, 'employee/'.$new_file);


              $Accounts[$Iterator]['image'] = 'employee/'.$new_file;
              }
              }
              }
              phase 2 by siva
             */
            $Iterator++;
            foreach ($Accounts as $key => $value) {
                if ($value['name'] == '' && $value['contact_no'] == '' && $value['email'] == '' && $value['class_id'] == '' && $value['section_id'] == '') {
                    unset($Accounts[$key]);
                    unset($value);
                    break;
                }
                $not_mandatary = array('class_id', 'section_id');
                foreach ($value as $keys => $val) {
                    if (!in_array($keys, $not_mandatary)) {
                        if (empty($val)) {
                            $msg['error'] = 'At Row : ' . $row . ' ' . $keys . ' required';
                            return \Redirect::back()->withInput($msg);
                        }
                    }
                }
            }
        }

        foreach ($Accounts as $key => $value) {
            //$value['image'] = (isset($value['image']) ? $value['image'] : 'employee/default_avatar.png');
            $check = Employee::where('email', $value['email'])->where('mobile', $value['contact_no'])->first();
            if (!$check) {
                $password = substr(md5(microtime()), rand(0, 9), 6);
                $length = strlen($value['name']);
                $uname = '';
                if ($length > 5) {
                    $uname = str_slug(substr(str_replace(" ", "", $value['name']), 0, 5));
                } else {
                    $uname = str_slug(str_replace(" ", "", $value['name']));
                }

                $uid = User::insertGetId([
                            'type' => 'teacher',
                            'school_id' => $user->school_id,
                            'password' => \Hash::make($value['contact_no']),
                            'hint_password' => $value['contact_no']
                ]);

                if ($uid) {

                    User::where('id', $uid)->update(['username' => $uname . $uid . "t"]);
                    Employee::insert([
                        'user_id' => $uid,
                        'type' => $value['staff_type_id'],
                        'class' => $value['class_id'],
                        'section' => $value['section_id'],
                        'name' => $value['name'],
                        'mobile' => $value['contact_no'],
                        'email' => $value['email'],
                        'avatar' => 'employee/default_avatar.png',
                        'school_id' => $user->school_id,
                        'platform' => 'website'
                    ]);
                }
            } else {
                $input['error'] = 'Employee already exists';
                return \Redirect::back()->withInput($input);
            }
        }
        $input['success'] = 'Employee added successfully';
        return \Redirect::back()->withInput($input);
    }

}
