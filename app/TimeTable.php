<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class TimeTable extends Model
{
    protected $table = 'time-table';

    /** @ Updated 14-4-2018 by priya @ **/
    private $active_session;
    function __construct()
    {
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
    }
    /**** end ****/

    // updated 9-11-2017 by priya
    /** @  Add Time Table From Excel Sheet  @  **/
    /*public function doImportTimeTable($user,$input)
    {
        //return $rows;
        $objPHPExcel = \PHPExcel_IOFactory::load($input['excel_timetable']);
        $obj = $objPHPExcel->getActiveSheet();
        if ($obj->getCellByColumnAndRow(0, 1)->getValue() == 'session_id' &&
            $obj->getCellByColumnAndRow(1, 1)->getValue() == 'class_id' &&
            $obj->getCellByColumnAndRow(2, 1)->getValue() == 'section_id' &&
            $obj->getCellByColumnAndRow(3, 1)->getValue() == 'subject_id' &&
            $obj->getCellByColumnAndRow(4, 1)->getValue() == 'employee_id' &&
            $obj->getCellByColumnAndRow(5, 1)->getValue() == 'period' &&
            $obj->getCellByColumnAndRow(6, 1)->getValue() == 'start_time' &&
            $obj->getCellByColumnAndRow(7, 1)->getValue() == 'end_time' &&
            $obj->getCellByColumnAndRow(8, 1)->getValue() == 'day'
        )
        {
            //return 'success';
            $rows = $obj->getHighestRow();

            $row = 1;

            $Iterator = 0;
            for (((($obj->getCellByColumnAndRow(0, $row)->getValue()) == 'session_id') ? $row = 2 : $row = 1); $row <= $rows; ++$row)
            {
                $session_id = $obj->getCellByColumnAndRow(0, $row)->getValue();
                $class_id = $obj->getCellByColumnAndRow(1, $row)->getValue();
                $section_id = $obj->getCellByColumnAndRow(2, $row)->getValue();
                $subject_id = $obj->getCellByColumnAndRow(3, $row)->getValue();
                $employee_id  = $obj->getCellByColumnAndRow(4, $row)->getValue();
                $period = $obj->getCellByColumnAndRow(5, $row)->getValue();
                $start_time = $obj->getCellByColumnAndRow(6, $row)->getValue();
                $end_time = $obj->getCellByColumnAndRow(7, $row)->getValue();
                $day = $obj->getCellByColumnAndRow(8, $row)->getValue();


                if ($class_id == '' || $section_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill class and section Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($session_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Session Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($subject_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Subject Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($employee_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Employee Field ';
                    return \Redirect::back()->withInput($input);
                }
                else if($period =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Period Number';
                    return \Redirect::back()->withInput($input);
                }
                else if($day =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Day Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($start_time =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Start Time Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($end_time =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill End Time Field';
                    return \Redirect::back()->withInput($input);
                }
                else
                {
                    //Session Id Check
                    $session_id_exist =\DB::table('session') ->where('school_id',\Auth::user()->school_id)
                        ->where('active',1)->first();
                    if(!$session_id_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Session id is not active in this School...';
                        return \Redirect::back()->withInput($msg);
                    }

                    // Class id Check
                    $class_id_exist = \DB::table('class')->where('school_id', \Auth::user()->school_id)->where('id', $class_id)->first();
                    if (!$class_id_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Class id is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }

                    // Section id Check
                    $section_id_exist = \DB::table('section')->where('school_id', \Auth::user()->school_id)->where('id', $section_id)->where('class_id', $class_id)->first();
                    $section_exist = \DB::table('section')->where('school_id', \Auth::user()->school_id)->where('id', $section_id)->first();
                    if (!$section_exist)// Section id Check In school
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Section id is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }
                    else if(!$section_id_exist)// Section id Check In Class
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Section id is not exist in this Class...';
                        return \Redirect::back()->withInput($msg);
                    }

                    //Subject Id check
                    $subject_id_exist = \DB::table('subject')->where('school_id', \Auth::user()->school_id)->where('id', $subject_id)->first();
                    if (!$subject_id_exist)//to check subject id
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Subject id is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }

                    //to Check Employee Id
                    $check_employee_id_exist = \DB::table('users')->where('school_id',\Auth::user()->school_id)
                        ->where('username',$employee_id)->first();
                    if(!$check_employee_id_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This User Name is not exist in this Class And Section !!!';
                        return \Redirect::back()->withInput($msg);
                    }

                    //to check period exist
                    $check_period_exist = \DB::table('time-table')->where('school_id',\Auth::user()->school_id)
                        ->where('class_id',$class_id)
                        ->where('section_id',$section_id)
                        // ->where('subject_id',$subject_id)
                        ->where('day',$day)
                        ->where('period',$period)
                        ->first();
                    if($check_period_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . '  Period is Already Exist for '.ucwords($day).' !!!';
                        return \Redirect::back()->withInput($msg);
                    }

                    

                    $Accounts[$Iterator] = array(
                        'period' => $period,
                        'start_time' => $start_time ,
                        'end_time' => $end_time,
                        'day' => $day,
                        'session_id' => $session_id,
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'subject_id' => $subject_id,
                        'employee_id' => $employee_id,
                    );

                    $Iterator++;
                    foreach ($Accounts as $key => $value)
                    {
                        $not_mandatary = array('employee_id','subject_id','session_id','day','end_time','start_time','period','class_id', 'section_id');
                        foreach ($value as $keys => $val)
                        {
                            if (!in_array($keys, $not_mandatary))
                            {
                                if (empty($val))
                                {
                                    $msg['error'] = 'At Row : ' . $row . ' ' . $keys . ' required';
                                    return \Redirect::back()->withInput($msg);
                                }
                            }
                        }
                    }
                }
            }

            foreach ($Accounts as $key => $value)
            {
                $getEmployeeId = \DB::table('users')->where('users.school_id',\Auth::user()->school_id)
                    ->where('users.username',$value['employee_id'])
                    ->leftJoin('teacher','teacher.user_id','=','users.id')
                    ->select('users.*','teacher.id as employee_id')
                    ->first();
                $check = TimeTable::where('class_id', $value['class_id'])
                    ->where('section_id', $value['section_id'])
                    //->where('subject_id', $value['subject_id'])
                    //->where('teacher_id', $getEmployeeId->employee_id)
                    ->where('day', $value['day'])
                    ->where('period', $value['period'])
                    ->where('school_id',\Auth::user()->school_id)
                    ->first();
                if (!$check)
                {

                    // start time validation
                    if (strpos($value['start_time'], ':') !== false) {
                        $explodeStart = explode(":",$value['start_time']);
                        $pointS = substr($explodeStart[1], 0, 2);
                        $start_time = $explodeStart[0].":".$pointS;                        
                    }elseif (strpos($value['start_time'], '.') !== false) {
                        $explodeStart = explode(".",$value['start_time']);
                        $pointS = substr($explodeStart[1], 0, 2);
                        $start_time = $explodeStart[0].".".$pointS; 
                    }elseif (strpos($value['start_time'], '-') !== false) {
                        $explodeStart = explode("-",$value['start_time']);
                        $pointS = substr($explodeStart[1], 0, 2);
                        $start_time = $explodeStart[0]."-".$pointS; 
                    }else{
                        $msg['error'] = 'Time format is invalid';
                        return \Redirect::back()->withInput($msg);
                    }   

                    if (strpos($value['end_time'], ':') !== false) {
                        $explodeEnd = explode(":",$value['end_time']);
                        $pointE = substr($explodeEnd[1], 0, 2);
                        $end_time = $explodeEnd[0].":".$pointE;                        
                    }elseif (strpos($value['end_time'], '.') !== false) {
                        $explodeEnd = explode(".",$value['end_time']);
                        $pointE = substr($explodeEnd[1], 0, 2);
                        $end_time = $explodeEnd[0].".".$pointE; 
                    }elseif (strpos($value['end_time'], '-') !== false) {
                        $explodeEnd = explode("-",$value['end_time']);
                        $pointE = substr($explodeEnd[1], 0, 2);
                        $end_time = $explodeEnd[0]."-".$pointE; 
                    }else{
                        $msg['error'] = 'Time format is invalid';
                        return \Redirect::back()->withInput($msg);
                    } 

                    TimeTable::insert([
                        'school_id' => \Auth::user()->school_id,
                        'class_id' => $value['class_id'],
                        'section_id' => $value['section_id'],
                        'subject_id' => $value['subject_id'],
                        'teacher_id' => $getEmployeeId->employee_id,
                        'period' => $value['period'],
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'day' => $value['day']
                    ]);
                    $input['success'] = 'TimeTable added Successfully !!! ';
                }
                else
                {
                    $input['error'] = 'TimeTable already exists !!! ';
                }
            }
        }
        else
        {
            $input['error'] = 'Data is not according to format !!! ';
        }
        return \Redirect::back()->withInput($input);
    }*/


     public function doImportTimeTable($user,$input)
    {
        //return $rows;
        $objPHPExcel = \PHPExcel_IOFactory::load($input['excel_timetable']);
        $obj = $objPHPExcel->getActiveSheet();
        if ($obj->getCellByColumnAndRow(0, 1)->getValue() == 'session_id' &&
            $obj->getCellByColumnAndRow(1, 1)->getValue() == 'class_id' &&
            $obj->getCellByColumnAndRow(2, 1)->getValue() == 'section_id' &&
            $obj->getCellByColumnAndRow(3, 1)->getValue() == 'subject_name' &&
            $obj->getCellByColumnAndRow(4, 1)->getValue() == 'subject_id' &&
            $obj->getCellByColumnAndRow(5, 1)->getValue() == 'employee_id' &&
            $obj->getCellByColumnAndRow(6, 1)->getValue() == 'employee_name' &&
            $obj->getCellByColumnAndRow(7, 1)->getValue() == 'period_no' &&
            $obj->getCellByColumnAndRow(8, 1)->getValue() == 'start_time' &&
            $obj->getCellByColumnAndRow(9, 1)->getValue() == 'end_time' &&
            $obj->getCellByColumnAndRow(10, 1)->getValue() == 'day'
        )
        {
            //return 'success';
            $rows = $obj->getHighestRow();

            $row = 1;

            $Iterator = 0;
            for (((($obj->getCellByColumnAndRow(0, $row)->getValue()) == 'session_id') ? $row = 2 : $row = 1); $row <= $rows; ++$row)
            {
                $session_id = $obj->getCellByColumnAndRow(0, $row)->getValue();
                $class_id = $obj->getCellByColumnAndRow(1, $row)->getValue();
                $section_id = $obj->getCellByColumnAndRow(2, $row)->getValue();
                $subject_name = $obj->getCellByColumnAndRow(3, $row)->getValue();
                $subject_id = $obj->getCellByColumnAndRow(4, $row)->getValue();
                $employee_id  = $obj->getCellByColumnAndRow(5, $row)->getValue();
                $employee_name = $obj->getCellByColumnAndRow(6, $row)->getValue();
                $period = $obj->getCellByColumnAndRow(7, $row)->getValue();
                $start_time = $obj->getCellByColumnAndRow(8, $row)->getValue();
                $end_time = $obj->getCellByColumnAndRow(9, $row)->getValue();
                $day = $obj->getCellByColumnAndRow(10, $row)->getValue();

                //dd($period);

                if($session_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Session Field';
                    return \Redirect::back()->withInput($input);
                }
                elseif ($class_id == '' || $section_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill class and section Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($subject_name =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Subject Name Time Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($subject_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Subject Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($employee_id == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Employee Id Field ';
                    return \Redirect::back()->withInput($input);
                }
                else if($employee_name == '')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Employee Name Field ';
                    return \Redirect::back()->withInput($input);
                }
                else if($period =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Period Number';
                    return \Redirect::back()->withInput($input);
                }
                else if($day =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Day Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($start_time =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill Start Time Field';
                    return \Redirect::back()->withInput($input);
                }
                else if($end_time =='')
                {
                    $input['error'] = 'At Row : ' . $row . ' Fill End Time Field';
                    return \Redirect::back()->withInput($input);
                }
                else
                {
                    //validate period Between 1 to 10
                    if($period > 10)
                    {
                        $msg['error'] = 'At Row : ' . $row . '  The Period must be between 1 and 10 !!!';
                        return \Redirect::back()->withInput($msg);
                    }

                    //validate - day should be full name
                    $day_array = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
                    if (!in_array($day, $day_array))
                    {
                        $msg['error'] = 'At Row : ' . $row . '  Fill the Day with Full Name or type Correctly  !!!';
                        return \Redirect::back()->withInput($msg);
                    }


                    //Validate Time Field
                    if($start_time)//check start Time
                    {
                        if (!preg_match('/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/',$start_time))
                        {
                            $msg['error'] = 'At Row : ' . $row . '   Invalid Time format for Start Time Field - format must be using colon like H:i  !!!';
                            return \Redirect::back()->withInput($msg);
                        }
                    }
                    if($end_time)//check end Time
                    {
                        if (!preg_match('/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/', $end_time))
                        {
                            $msg['error'] = 'At Row : ' . $row . '   Invalid Time format for End Time Field - format must be using colon like H:i  !!!';
                            return \Redirect::back()->withInput($msg);
                        }
                    }

                    /*$start = new DateTime::createFromFormat('H:i', $start_time);
                    $end = new DateTime::createFromFormat('H:i', $end_time);
                    if ($start > $end)
                    {
                        echo "true";
                    }
                    else
                    {
                        echo "false";
                    }*/

                    /*if($end_time < $start_time)//check start Time is lesser than end Time
                    {
                        $msg['error'] = 'At Row : ' . $row . '   End Time field should be Greater than Start Time Field  !!!';
                        return \Redirect::back()->withInput($msg);
                    }*/

                    /* if($start_time)
                    {
                        // echo preg_match('/^((0[1-9]|1[0-2]):[0-5][0-9]) ([AaPp][Mm])$/','01:00 am');
                        /*if(!preg_match('/^((0[1-9]|1[0-2]):[0-5][0-9]) ([AaPp][Mm])$/',$start_time))
                        {
                        /*trigger_error(
                                'Invalid Time format - format must be using semicolon instead of underscores, dots and/or dashes.',
                                E_USER_DEPRECATED
                            );
                            $msg['error'] = 'At Row : ' . $row . '  Time In field should be in time format  !!!';
                            return \Redirect::back()->withInput($msg);
                        }

                        // if (!preg_match('/^([0-9]{2})\:([0-9]{2})$/', $start_time)) {
                        // $exp = ^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$;
                        // $timeFormat = /^([0-9]{2})\:([0-9]{2})$/;
                        $rules =preg_match( '/^(([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?)$^/');
                    }
                    */

                    //Session Id Check
                    $session_id_exist =\DB::table('session') ->where('school_id',\Auth::user()->school_id)
                        ->where('active',1)->first();
                    if(!$session_id_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Session id is not active in this School...';
                        return \Redirect::back()->withInput($msg);
                    }

                    // Class id Check
                    $class_id_exist = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018    
                    ->where('id', $class_id)->first();
                    if (!$class_id_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Class id is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }

                    // Section id Check
                    $section_id_exist = \DB::table('section')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018    
                    ->where('id', $section_id)->where('class_id', $class_id)->first();
                    $section_exist = \DB::table('section')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('id', $section_id)->first();
                    if (!$section_exist)// Section id Check In school
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Section id is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }
                    else if(!$section_id_exist)// Section id Check In Class
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Section id is not exist in this Class...';
                        return \Redirect::back()->withInput($msg);
                    }

                    //Subject Name Check
                    $subject_name_exist = \DB::table('subject')->where('school_id', \Auth::user()->school_id)
                        //->where('id', $subject_id)
                        ->where('subject', $subject_name)
                        ->first();
                    if (!$subject_name_exist)//Check Subject name in school
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Subject Name is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }
                    //Subject Id check
                    $subject_id_exist = \DB::table('subject')->where('school_id', \Auth::user()->school_id)->where('id', $subject_id)->first();
                    if (!$subject_id_exist)//to check subject id
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Subject id is not exist in this School...';
                        return \Redirect::back()->withInput($msg);
                    }

                    //check subject name is correct for this subject ID
                    $check_subject_name_exist = \DB::table('subject')->where('school_id', \Auth::user()->school_id)
                        ->where('id', $subject_id)
                        ->where('subject', $subject_name)
                        ->first();

                    if(!$check_subject_name_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' Check the Subject Name for this Subject Id !!! ';
                        return \Redirect::back()->withInput($msg);
                    }
                    /* else
                    {
                      //check subject name Exist in this class & section
                      $subjects = \DB::table('subject')->whereIn('id', json_decode($section_id_exist->subjects))
                        ->select('subject.id','subject.subject')
                        ->get();

                      //  if (!in_array($subject_id, $subjects->id))

                       foreach($subjects as $sub)
                        {
                            if (!in_array($subject_id,$sub->id))
                            {
                                return 'fg';
                            }
                            else
                            {
                                return 'fhj';
                            }*/
                            //Check subject Id Exist In this class & section
                            /*if($subject_id != $sub->id)
                            {

                                $msg['error'] = 'At Row : ' . $row . ' Check the Subject Id for this Class & Section !!! ';
                                return \Redirect::back()->withInput($msg);
                            }
                            //Check subject Name Exist In this class & section
                            elseif($subject_name != $sub->subject)
                            {
                                $msg['error'] = 'At Row : ' . $row . ' Check the Subject Name for this Class & Section !!! ';
                                return \Redirect::back()->withInput($msg);
                            }*/
                        /*}
                    }*/

                    //to Check Employee Id
                    $check_employee_id_exist = \DB::table('users')->where('school_id',\Auth::user()->school_id)
                        ->where('username',$employee_id)->first();
                    if(!$check_employee_id_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This User Name is not exist in this School !!!';
                        return \Redirect::back()->withInput($msg);
                    }

                    //Check Employee Name In School
                    $check_employee_name_exist = \DB::table('teacher')
                        ->where('session_id',$this->active_session->id)//updated 10-5-2018
                        ->where('school_id',\Auth::user()->school_id)
                        ->where('name',$employee_name)->first();
                    if(!$check_employee_name_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' This Employee Name  not exist in this School !!!';
                        return \Redirect::back()->withInput($msg);
                    }

                    //check Employee Name for this user name
                    $check_employee_name =\DB::table('teacher')->where('school_id',\Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)//updated 10-5-2018
                        ->where('user_id',$check_employee_id_exist->id)
                        ->where('name',$employee_name)
                        ->first();
                    if(!$check_employee_name)
                    {
                        $msg['error'] = 'At Row : ' . $row . ' Check Employee Name for this User Name !!!';
                        return \Redirect::back()->withInput($msg);
                    }

                    //to check period exist
                    $check_period_exist = \DB::table('time-table')->where('school_id',\Auth::user()->school_id)
                        ->where('session_id',$this->active_session->id)//updated 14-4-2018
                        ->where('class_id',$class_id)
                        ->where('section_id',$section_id)
                        // ->where('subject_id',$subject_id)
                        ->where('day',$day)
                        ->where('period',$period)
                        ->first();
                    if($check_period_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . '  Period is Already Exist for '.ucwords($day).' !!!';
                        return \Redirect::back()->withInput($msg);
                    }




                    //to check Period exist in Day
                    /*$check_day_exist = \DB::table('time-table')->where('school_id',\Auth::user()->school_id)
                        ->where('class_id',$class_id)
                        ->where('section_id',$section_id)
                        ->where('subject_id',$subject_id)
                        ->where('day',$day)
                        ->first();
                    if($check_day_exist)
                    {
                        $msg['error'] = 'At Row : ' . $row . '  Period is Already Exist for '.ucwords($day).' !!!';
                        return \Redirect::back()->withInput($msg);
                    }*/

                    /*$subject_exist =\DB::table('section')->where('school_id',\Auth::user()->school_id)
                         ->where('class_id',$class_id)
                         ->where('section_id',$section_exist)
                         ->get();
                     foreach($subject_exist as $subject_name)
                     {
                         foreach($subject_name['subjects'] as $keys => $singleSubject)
                         {
                             if($singleSubject[$keys] != $subject_id)
                             {
                                 $msg['error'] = 'At Row : ' . $row . ' This Subject id is not exist in this Class And Section !!!';
                                 return \Redirect::back()->withInput($msg);
                             }
                         }
                     }*/

                    $Accounts[$Iterator] = array(
                        'period' => $period,
                        'start_time' => $start_time ,
                        'end_time' => $end_time,
                        'day' => $day,
                        'session_id' => $session_id,
                        'class_id' => $class_id,
                        'section_id' => $section_id,
                        'subject_id' => $subject_id,
                        'employee_id' => $employee_id,
                    );

                    $Iterator++;
                    foreach ($Accounts as $key => $value)
                    {
                        $not_mandatary = array('employee_id','subject_id','session_id','day','end_time','start_time','period','class_id', 'section_id');
                        foreach ($value as $keys => $val)
                        {
                            if (!in_array($keys, $not_mandatary))
                            {
                                if (empty($val))
                                {
                                    $msg['error'] = 'At Row : ' . $row . ' ' . $keys . ' required';
                                    return \Redirect::back()->withInput($msg);
                                }
                            }
                        }
                    }
                }
            }

            foreach ($Accounts as $key => $value)
            {
                $getEmployeeId = \DB::table('users')->where('users.school_id',\Auth::user()->school_id)
                    ->where('users.username',$value['employee_id'])
                    ->leftJoin('teacher','teacher.user_id','=','users.id')
                    ->select('users.*','teacher.id as employee_id')
                    ->first();
               //check Time table exist
                $timeTableExists = TimeTable::where('school_id', $user->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('class_id', $value['class_id'])
                    ->where('section_id', $value['section_id'])
                    ->where('subject_id', $value['subject_id'])
                    ->where('day', $value['day'])
                    ->where('period', $value['period'])
                    ->where('start_time', $value['start_time'])
                    ->first();

                //check period & day exist
                $check = TimeTable::where('class_id', $value['class_id'])
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('section_id', $value['section_id'])
                    ->where('day', $value['day'])
                    ->where('period', $value['period'])
                    ->where('school_id',\Auth::user()->school_id)
                    ->first();

                //check this Employee in same period & day exist
                $checkEmployee=TimeTable::where('school_id',$user->school_id)
                    ->where('session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('day',$value['day'])
                    ->where('period',$value['period'])
                    ->where('teacher_id',$getEmployeeId->employee_id)
                    ->first();
                if($timeTableExists)
                {
                    $input['error'] = 'TimeTable already exists !!! ';
                }
                elseif($check)
                {
                    $input['error'] = 'TimeTable already exists for this period & day !!! ';
                }
                elseif($checkEmployee)
                {
                    $input['error'] = 'Time table Already Exist for this Employee in same period !!! ';
                }
                else
                {
                    TimeTable::insert([
                        'school_id' => \Auth::user()->school_id,
                        'class_id' => $value['class_id'],
                        'section_id' => $value['section_id'],
                        'subject_id' => $value['subject_id'],
                        'teacher_id' => $getEmployeeId->employee_id,
                        'period' => $value['period'],
                        'start_time' => $value['start_time'],
                        'end_time' => $value['end_time'],
                        'day' => $value['day'],
                        'session_id' => $this->active_session->id //updated 14-4-2018
                    ]);
                    $input['success'] = 'TimeTable added Successfully !!! ';
                }
            }
        }
        else
        {
            $input['error'] = 'Data is not according to format !!! ';
        }
        return \Redirect::back()->withInput($input);
    }

    /**  end **/


    /*
     * updated 14-4-2018
     * public function doGetTimeTable($user)
    {
        $timetables = TimeTable::where('time-table.school_id', $user->school_id)
            ->where('time-table.session_id',$this->active_session->id)//updated 14-4-2018
            ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
            ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
            ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
            ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
            ->select
            (
                'time-table.id',
                'class.class',
                'section.section',
                'subject.subject',
                'teacher.name',
                'time-table.period',
                'time-table.start_time',
                'time-table.end_time',
                'time-table.day'
            )
            ->get();
        return view('users.time_table.time_table', compact('timetables'));
    }*/

    public function doGetTimeTable($user)//updated 14-4-2018
    {
        $class = \Request::get('class');
        $session = \Request::get('session');
        $section = \Request::get('section');

        $sessions = Session::where('school_id',$user->school_id)->get();

        if($class && $session && $section)
        {
            $classData = addClass::where('id',$class)->first();
            $sessionData  = Session::where('id',$session)->first();
            $sectionData = Section::where('id',$section)->first();
            $timetables = TimeTable::where('time-table.school_id', $user->school_id)
                ->where('time-table.session_id',$session)//updated 14-4-2018
                ->where('time-table.class_id',$class)//updated 14-4-2018
                ->where('time-table.section_id',$section)//updated 14-4-2018
                ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
                ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
                ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
                ->select
                (
                    'time-table.id',
                    'class.class',
                    'section.section',
                    'subject.subject',
                    'teacher.name',
                    'time-table.period',
                    'time-table.start_time',
                    'time-table.end_time',
                    'time-table.day'
                )
                ->get();
        }
        else
        {
            $timetables = TimeTable::where('time-table.school_id', $user->school_id)
                ->where('time-table.session_id',$this->active_session->id)//updated 14-4-2018
                ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
                ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
                ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
                ->select
                (
                    'time-table.id',
                    'class.class',
                    'section.section',
                    'subject.subject',
                    'teacher.name',
                    'time-table.period',
                    'time-table.start_time',
                    'time-table.end_time',
                    'time-table.day'
                )
                ->get();
            $currentSession = $this->active_session->session;
        }
        return view('users.time_table.time_table', compact('currentSession','classData','sessionData','sectionData','sessions','timetables'));
    }
    
    /*
     * * updated 20-11-2017 by priya
     *
     *public function doPostTimeTable($user, $request)
     {
        $timeTableExists = TimeTable::where('school_id', $user->school_id)
                        ->where('class_id', $request['class'])
                        ->where('section_id', $request['section'])
                        ->where('subject_id', $request['subject'])
                        ->where('day', $request['day'])
                        ->where('period', $request['period'])
                        ->where('start_time', $request['start_time'])
                        ->first();
        /* phase 2 by siva
        $subjectExist = TimeTable::where('school_id', $user->school_id)
                        ->where('class_id', $request['class'])
                        ->where('section_id', $request['section'])
                        ->where('subject_id', $request['subject'])
                        ->where('day', $request['day'])
                        ->first();
        
        if($subjectExist)
        {
            $input['error'] = 'Time Table already exists';
            return \Redirect::back()->withInput($input);
        }/** siva **
        if($timeTableExists)
        {
            $input['error'] = 'Time Table already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            TimeTable::insert([
                'school_id' => $user->school_id,
                'class_id' => $request['class'],
                'section_id' => $request['section'],
                'subject_id' => $request['subject'],
                'period' => $request['period'],
                'day' => $request['day'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'teacher_id' => $request['teacher'],
            ]);
            $input['success'] = 'Time table is added successfully !!! ';
            return \Redirect::back()->withInput($input);
        }
    }*/
    public function doPostTimeTable($user, $request)
    {
        //Check Time table Already exists
        $timeTableExists = TimeTable::where('school_id', $user->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('class_id', $request['class'])
            ->where('section_id', $request['section'])
            ->where('subject_id', $request['subject'])
            ->where('day', $request['day'])
            ->where('period', $request['period'])
            //->where('start_time', $request['start_time'])
            ->first();

        //check period Already exist in day
        $checkPeriod = TimeTable::where('class_id', $request['class'])
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('section_id', $request['section'])
            ->where('day', $request['day'])
            ->where('period', $request['period'])
            ->where('school_id',\Auth::user()->school_id)
            ->first();

        //Check Same teacher exist for same period,same day
        $check=TimeTable::where('school_id',$user->school_id)
            ->where('session_id',$this->active_session->id)//updated 14-4-2018
            ->where('day',$request['day'])
            ->where('period',$request['period'])
            ->where('teacher_id',$request['teacher'])
            ->first();
        if($timeTableExists)
        {
            $input['error'] = 'Time Table already exists';
            return \Redirect::back()->withInput($input);
        }
        elseif($checkPeriod)
        {
            $input['error'] = 'TimeTable already exists for this period & day !!! ';
        }
        elseif($check)
        {
            $input['error'] = 'Time table Already Exist for this Employee in same period !!! ';
        }
        else
        {
            TimeTable::insert([
                'school_id' => $user->school_id,
                'class_id' => $request['class'],
                'section_id' => $request['section'],
                'subject_id' => $request['subject'],
                'period' => $request['period'],
                'day' => $request['day'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'teacher_id' => $request['teacher'],
                'session_id' => $this->active_session->id //updated 14-4-2018
            ]);
            $input['success'] = 'Time table is added successfully !!! ';
        }
        return \Redirect::back()->withInput($input);
    }
    /**@ End 20-11-2017 @ **/
    
public function doPostExamTimeTable($user,$request)
    {
        //return 'postExam';exit;
        $timeTableExists = \DB::table('exam_timetable')->where('school_id', $user->school_id)
                        ->where('session_id',$this->active_session->id)//updated 14-4-2018
                        ->where('class_id', $request['class'])
                        ->where('section_id', $request['section'])
                        ->where('subject_id', $request['subject'])
                        ->where('exam_date', $request['exam_date'])
                        ->where('start_time', $request['start_time'])
                        ->where('exam_type_id', $request['exam_type'])
                        ->first();
      
            if($timeTableExists)
            {
                //return 'postExam';exit;
                $input['error'] = 'Exam Time Table already exists';
                return \Redirect::back()->withInput($input);
            }
            else
            {
                //return 'postExamSuccess';exit;
                \DB::table('exam_timetable')->insert([
                    'school_id' => $user->school_id,
                    'class_id' => $request['class'],
                    'section_id' => $request['section'],
                    'subject_id' => $request['subject'],
                    'exam_date' => date('d-m-Y',strtotime($request['exam_date'])),
                    'start_time' => $request['start_time'],
                    'end_time' => $request['end_time'],
                    'teacher_id' => $request['teacher'],
                    'exam_type_id' => $request['exam_type'],
                    'session_id' => $this->active_session->id //updated 14-4-2018
                ]);
                //return date('d-m-Y',strtotime($request['exam_date']));exit;
                $input['success'] = 'Exam Time table is added successfully';
                return \Redirect::back()->withInput($input);
            }
    }
    
    public function doGetExamTimeTable($user)
    {
        //return 'postExam';exit;
        $getExamTypes = \DB::table('exam')->where('school_id', \Auth::user()->school_id)->get();
        $exam_type_id = \Request::get('exam_type');
         $class_id=\Request::get('class');

        $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)
                    ->where('session_id',$this->active_session->id)->get();
        //return count($getExamTypes);exit;
        if($exam_type_id && $class_id)
        {
            //return $exam_type_id;exit;
            $get_exam_type =\DB::table('exam')->where('school_id',\Auth::user()->school_id)
                            ->where('id',$exam_type_id)->first();

            $get_class=\DB::table('class')->where('school_id',\Auth::user()->school_id)
                            ->where('id',$class_id)->first();
            $timetables = \DB::table('exam_timetable')->where('exam_timetable.school_id', $user->school_id)
                            ->where('exam_timetable.session_id',$this->active_session->id)//updated 14-4-2018
                            ->where('exam_timetable.exam_type_id', $exam_type_id)
                            ->where('exam_timetable.class_id', $class_id)
                            ->leftJoin('class', 'exam_timetable.class_id', '=', 'class.id')
                            ->leftJoin('section', 'exam_timetable.section_id', '=', 'section.id')
                            ->leftJoin('subject', 'exam_timetable.subject_id', '=', 'subject.id')
                            ->leftJoin('teacher', 'exam_timetable.teacher_id', '=', 'teacher.id')
                            ->leftJoin('exam', 'exam_timetable.exam_type_id', '=', 'exam.id')
                            ->select
                            (
                                'exam_timetable.id',
                                'exam.exam_type',
                                'class.class',
                                'section.section',
                                'subject.subject',
                                'teacher.name', 
                                'exam_timetable.start_time', 
                                'exam_timetable.end_time',
                                'exam_timetable.exam_date'
                            )
                            ->get();
        }
        else
        {
            $timetables = [];
        }
        
        return view('users.time_table.exam_time_table', compact('timetables','getExamTypes','get_exam_type','$classes'));
    }

    public function doGetTimeTables($platform, $class, $section)
    {
        $tables = TimeTable::where('time-table.class_id', $class)->where('time-table.section_id', $section)
                    ->where('time-table.session_id',$this->active_session->id)//updated 14-4-2018
                    ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
                    ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
                    ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                    ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
                    ->select
                    (
                        'time-table.id',
                        'class.class',
                        'section.section',
                        'subject.subject',
                        'teacher.name as teacherName',
                        'time-table.period',
                        'time-table.start_time',
                        'time-table.end_time',
                        'time-table.day'
                    )
                    ->get();
        return \api(['data' => $tables]);
    }

    public function doEditTimeTable($user, $platform, $id)
    {
        $table = TimeTable::where('time-table.id', $id)
                    ->where('time-table.session_id',$this->active_session->id)//updated 14-4-2018
                    ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
                    ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
                    ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                    ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
                    ->select
                    (
                        'time-table.id',
                        'class.class',
                        'class.id as class_id',
                        'section.section',
                        'section.id as section_id',
                        'subject.subject',
                        'subject.id as subject_id',
                        'teacher.name as teacherName',
                        'teacher.id as teacher_id',
                        'time-table.period',
                        'time-table.start_time',
                        'time-table.end_time',
                        'time-table.day'
                    )
                    ->first();
        if(!$table)
            return \api::notFound(['errorMsg' => 'Invalid Parameter']);
        return \api(['data' => $table]);
    }

    public function doUpdateTimeTable($user, $request)
    {
        $timeTableExists = TimeTable::where('school_id', $user->school_id)
                        ->where('session_id',$this->active_session->id)//updated 14-4-2018
                        ->where('class_id', $request['class_id'])
                        ->where('section_id', $request['section_id'])
                        ->where('subject_id', $request['subject_id'])
                        ->where('day', $request['day'])
                        ->where('id', '!=', $request['id'])
                        ->first();
        if($timeTableExists)
        {
            return \api::notValid(['errorMsg' => 'Time Table already exists', 'id' => $timeTableExists->id]);
        }
        else
        {
            TimeTable::where('id', $request['id'])->update([
                'school_id' => $user->school_id,
                'class_id' => $request['class_id'],
                'section_id' => $request['section_id'],
                'subject_id' => $request['subject_id'],
                'period' => $request['period'],
                'day' => $request['day'],
                'start_time' => $request['start_time'],
                'end_time' => $request['end_time'],
                'teacher_id' => $request['teacher_id']
            ]);
            
            return \api::success(['data' => 'Time table is updated successfully']);
        }
    }
    
    // changes done by parthiban 19-11-2017(sunday)

    // public function doGetTimeTableByStudent($user, $platform)
    // {
    // 	$student = \DB::table('student')->where('user_id', $user->id)->first();
    // 	if(!$student)
    //             return \api::notValid(['errorMsg' => 'Invalid Parameter']);
    // 	$timeTables = TimeTable::where('time-table.class_id', $student->class_id)->where('time-table.section_id', $student->section_id)
    // 			->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
    // 			->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
    // 			->select
    // 			(
    // 				'time-table.id',
    // 				'subject.subject',
    // 				'teacher.name as teacherName',
    // 				'time-table.period',
    // 				'time-table.start_time',
    // 				'time-table.end_time',
    // 				'time-table.day'
    // 			)
    // 			->orderBy('time-table.id', 'ASC')
    // 			->get();
    // 	return \api::success(['data' => $timeTables]); 
    // }

    public function doGetTimeTableByStudent($user, $platform)
    {
        $student = \DB::table('student')->where('user_id', $user->id)->first();
        if(!$student)
                return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $daysArray = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
        $finalArray = [];        
        $periods = ["1","2","3","4","5","6","7","8","9","10"];
        foreach ($daysArray as $k => $days) {
            foreach ($periods as $p => $period) {            
                $timeTablesForDays = TimeTable::where('time-table.class_id', $student->class_id)
                    ->where('time-table.session_id',$this->active_session->id)//updated 14-4-2018
                    ->where('time-table.section_id', $student->section_id)
                    ->where('day', $days)
                    ->where('period', $period)            
                    ->leftJoin('subject', 'time-table.subject_id', '=', 'subject.id')
                    ->leftJoin('teacher', 'time-table.teacher_id', '=', 'teacher.id')
                    ->leftJoin('class', 'time-table.class_id', '=', 'class.id')
                    ->leftJoin('section', 'time-table.section_id', '=', 'section.id')
                    ->select('time-table.id', 'time-table.period', 'time-table.day', 'time-table.start_time', 'time-table.end_time', 'subject.subject', 'class.class','section.section','teacher.name as teacherName')            
                    ->first();                
                $finalArray[$days][$period] = $timeTablesForDays;
            }
        }
        return \api::success(['data' => $finalArray]);          
    }     
}