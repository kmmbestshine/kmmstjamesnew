<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Requests;

class EmployeeAttendance extends Model
{

    /** Import Employee Attendance in Excel Sheet  */
    public function doImportEmployeeAttendance($user,$input)
    {
        $objPHPExcel = \PHPExcel_IOFactory::load($input['excel_attendance']);
        $obj = $objPHPExcel->getActiveSheet();
        if ($obj->getCellByColumnAndRow(0, 1)->getValue() == 'date' &&
            $obj->getCellByColumnAndRow(1, 1)->getValue() == 'employee_name' &&
            $obj->getCellByColumnAndRow(2, 1)->getValue() == 'employee_id' &&
            $obj->getCellByColumnAndRow(3, 1)->getValue() == 'staff_type' &&
            $obj->getCellByColumnAndRow(4, 1)->getValue() == 'time_in' &&
            $obj->getCellByColumnAndRow(5, 1)->getValue() == 'time_out' &&
            $obj->getCellByColumnAndRow(6, 1)->getValue() == 'status'
        )
        {
            //return 'success';
            $rows = $obj->getHighestRow();
            $row = 1;
            $Iterator = 0;
            for (((($obj->getCellByColumnAndRow(0, $row)->getValue()) == 'date') ? $row = 2 : $row = 1); $row <= $rows; ++$row)
            {
                //get Column Values in Excel
                $attendance_date = $obj->getCellByColumnAndRow(0, $row)->getValue();
                $employee_name = $obj->getCellByColumnAndRow(1, $row)->getValue();
                $user_name = $obj->getCellByColumnAndRow(2, $row)->getValue();
                $staff_type = $obj->getCellByColumnAndRow(3, $row)->getValue();
                $time_in = $obj->getCellByColumnAndRow(4, $row)->getValue();
                $time_out = $obj->getCellByColumnAndRow(5, $row)->getValue();
                $attendance_status = $obj->getCellByColumnAndRow(6, $row)->getValue();


                /** @ Check Empty Values in Excel Sheet @ **/
                if(empty($attendance_date))
                {
                    // return 'hii';
                    $msg['error'] = 'At Row : ' . $row . '  Attendance Date Field  required';
                    return \Redirect::back()->withInput($msg);
                }
                if(empty($employee_name))
                {
                    $msg['error'] = 'At Row : ' . $row . '  Employee Name Field  required';
                    return \Redirect::back()->withInput($msg);
                }
                if(empty($user_name))
                {
                    $msg['error'] = 'At Row : ' . $row . '  Employee Id/User Name Field  required';
                    return \Redirect::back()->withInput($msg);
                }
                if(empty($staff_type))
                {
                    $msg['error'] = 'At Row : ' . $row . '  Staff Type Field  required';
                    return \Redirect::back()->withInput($msg);
                }
                if(empty($attendance_status))
                {
                    $msg['error'] = 'At Row : ' . $row . '  Attendance Status Field  required';
                    return \Redirect::back()->withInput($msg);
                }
                if($attendance_status == 'P')
                {
                    if (empty($time_in))
                    {
                        $input['error'] = 'At Row : ' . $row . '  In Time Field Required !!!  ';
                        return \Redirect::back()->withInput($input);
                    }
                    elseif (empty($time_out))
                    {
                        $input['error'] = 'At Row : ' . $row . '  Out Time Field Required !!!  ';
                        return \Redirect::back()->withInput($input);
                    }
                }

                //Check Employee User Name
                $check_employee_user_name = \DB::table('users')->where('school_id',\Auth::user()->school_id)
                    ->where('username',$user_name)
                    ->first();
                if(!$check_employee_user_name)
                {
                    $input['error'] = 'At Row : ' . $row . '  User Name Not Exist In this School !!! ';
                    return \Redirect::back()->withInput($input);
                }
                else
                {
                    $getUserName = $check_employee_user_name->id;
                }

                //Check Staff Type
                if($staff_type == 'Teaching Staff')
                {
                    $getStaffId = \DB::table('staff')->where('school_id',\Auth::user()->school_id)
                        ->where('staff_type','=','Teaching Staff')
                        ->first();
                    $getStaff = $getStaffId->id;
                }
                else if($staff_type== 'Non Teaching Staff')
                {
                    $getStaffId = \DB::table('staff')->where('school_id',\Auth::user()->school_id)
                        ->where('staff_type','=','Non Teaching Staff')
                        ->first();
                    $getStaff = $getStaffId->id;
                }
                else
                {
                    $input['error'] = 'At Row : ' . $row . '  Staff Type Should be Teaching Staff or Non Teaching Staff Only !!! ';
                    return \Redirect::back()->withInput($input);
                }

                //Check Staff Type For Employee
                if($getUserName)
                {
                    $check_staff_type = \DB::table('teacher')
                        ->where('school_id',\Auth::user()->school_id)
                        ->where('user_id',$getUserName)
                        ->first();
                    /*if(!$check_staff_type)
                    {
                        $input['error'] = 'At Row : ' . $row . '  User Id Not Exist In this School !!! ';
                        return \Redirect::back()->withInput($input);
                    }
                    else*/
                    if($check_staff_type->type != $getStaff)
                    {
                        $input['error'] = 'At Row : ' . $row . '  Fill the Staff Type for this user name correctly !!! ';
                        return \Redirect::back()->withInput($input);
                    }
                }

                //Get Attendance Status
                if($attendance_status == 'P')
                {
                    if($time_in == '')
                    {
                        $input['error'] = 'At Row : ' . $row . '  In Time Field Required !!!  ';
                        return \Redirect::back()->withInput($input);
                    }
                    else if($time_out == '')
                    {
                        $input['error'] = 'At Row : ' . $row . '  Out Time Field Required !!!  ';
                        return \Redirect::back()->withInput($input);
                    }
                    else
                    {
                        $attendance_in = $time_in;
                        $attendance_out = $time_out;
                    }
                }
                else if($attendance_status== 'L')
                {
                    $attendance_in = "";
                    $attendance_out= "";
                }
                else if($attendance_status == 'A')
                {
                    $attendance_in = "";
                    $attendance_out= "";
                }
                else if($attendance_status == 'H')
                {
                    $attendance_in = "";
                    $attendance_out= "";
                }
                else
                {
                    $input['error'] = 'At Row : ' . $row . '  Status Field Should be P,A,L or H Only !!!  ';
                    return \Redirect::back()->withInput($input);
                }

                $Accounts[$Iterator] = array(
                    'attendance_date' => $attendance_date,
                    'employee_name' => $employee_name ,
                    'user_name' => $getUserName,
                    'staff_type' => $getStaff,
                    'time_in' => $attendance_in,
                    'time_out' => $attendance_out,
                    'attendance_status' => $attendance_status,
                );
                $Iterator ++;
            }

            foreach ($Accounts as $key => $value)
            {
                $getCurrentSession = \DB::table('session')->where('school_id',\Auth::user()->school_id)
                    ->where('active',1)->first();
                $check = \DB::table('teacher_attendance')
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('session_id',$getCurrentSession->id)
                    ->where('date',date('Y-m-d',strtotime($value['attendance_date'])))
                    ->where('staff_type',$value['staff_type'])
                    ->where('employee_id',$value['user_name'])
                    ->first();
                // dd($check);
                if (!$check)
                {
                    // return 'success';
                    \DB::table('teacher_attendance')
                        ->insert([
                            'school_id' => \Auth::user()->school_id,
                            'session_id' => $getCurrentSession->id,
                            'employee_id' => $value['user_name'],
                            'session_type' => '',
                            'attendance' => $value['attendance_status'],
                            'staff_type' => $value['staff_type'],
                            'in' => $value['time_in'],
                            'out' => $value['time_out'],
                            'date' => date('Y-m-d',strtotime($value['attendance_date']))
                        ]);
                    $input['success'] = 'Employee Attendance added Successfully !!! ';
                }
                else
                {
                    // return 'error';
                    $input['error'] = 'Employee Attendance already exists !!! ';
                }
            }
        }
        else
        {
            $input['error'] = 'Data is not according to format !!! ';
        }
        return \Redirect::back()->withInput($input);

    }

}
