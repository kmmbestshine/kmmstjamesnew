<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Holiday;
use App\Attendancestatus;

class Attendance extends Model {

    protected $table = "attendance";

    public function doPostAttendanceByTeacher($input, $user, $teacher) {//mari for v3
        $i = 0;
		$posted_date = date('Y-m-d', strtotime($input['date']));
		$attendance_session = date('a', strtotime($input['date']));
        $exist=Attendancestatus::whereDate('date','=',$posted_date)
            ->where('school_id','=',$user->school_id)
            // ->where('class_id','=',$teacher->class)
            // ->where('section_id','=',$teacher->section)
            ->where('class_id','=',$input['class_id'])
            ->where('section_id','=',$input['section_id'])            
            //->where('teacher_id','=',$teacher->id)// 06/10 update
            ->where('attendance_session','=',$attendance_session )->first();
        if(empty($exist)){
            Attendancestatus::insert([
            'date'=>$posted_date,
            'school_id' => $user->school_id,
            // 'class_id' => $teacher->class,
            // 'section_id' => $teacher->section,            
            'class_id' => $input['class_id'],
            'section_id' => $input['section_id'],
            'teacher_id' => $teacher->id,
            'attendance_session' =>$attendance_session
            ]);
        }
        else{ // 06/10 update
            return api(['data' => 'contact admin to change attendance']);
        }
		//}
        foreach (json_decode($input['attendance']) as $key => $atten) {
            $i++;
            $posted_date = date('Y-m-d', strtotime($input['date']));
            $attendance_session = date('a', strtotime($input['date']));
            $atten->remarks = (isset($atten->remarks) ? $atten->remarks : '');
            $atten_exist = \DB::table('attendance')->where('class_id', $input['class_id'])
                    ->where('section_id', $input['section_id'])->where('attendance_session', $attendance_session)
                    ->where('date', $posted_date)->where('student_id', $atten->student_id)
                    ->first();
            if ($atten->attendance_type == 'L' || $atten->attendance_type == 'A') {

                if ($atten_exist) {
                    return api(['data' => 'contact admin to change attendance']);
                    /* Attendance::where('id', $atten_exist->id)->update([
                      'attendance' => $atten->attendance_type,
                      'attendance_session' => $attendance_session,
                      'remarks' => $atten->remarks,
                      'teacher_id' => $teacher->id,
                      'attendance_by' => 'teacher'
                      ]); */
                } else {
                     if($atten->attendance_type == 'A'){
                        $teacher = \DB::table('teacher')->where('class', $input['class_id'])->where('section', $input['section_id'])->first();
                        $students = \DB::table('student')
                            ->where('student.id','=', $atten->student_id)->join('parent', 'student.parent_id', '=', 'parent.id')
                            ->select('student.name as student_name', 'parent.mobile')
                            ->first();
                        $message = urlencode($students->student_name . ' is absent on ' . date('d-m-Y') . '.');
                    // dd($message);
                        //file_get_contents('http://103.16.101.52/sendsms/bulksms?username=shtk-bestshine&password=70922728&type=0&dlr=1&destination=91' . $students->mobile . '&source=shintk&message=' . $message);
                        file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=0&dlr=1&destination=91'.$students->mobile.'&source=SCHOOL&message='. $message);
                    }
                    Attendance::insert([
                        'school_id' => $user->school_id,
                        'teacher_id' => $teacher->id,
                        'class_id' => $input['class_id'],
                        'section_id' => $input['section_id'],
                        'student_id' => $atten->student_id,
                        'attendance' => $atten->attendance_type,
                        'attendance_session' => $attendance_session,
                        'remarks' => $atten->remarks,
                        'date' => $posted_date,
                        'attendance_by' => 'teacher'
                    ]);
                }
            } else {
                if ($i == 1) {
                    if ($atten_exist) {
                        return api(['data' => 'contact admin to change attendance']);
                        /* Attendance::where('id', $atten_exist->id)->update([
                          'attendance' => $atten->attendance_type,
                          'attendance_session' => $attendance_session,
                          'remarks' => $atten->remarks,
                          'teacher_id' => $teacher->id,
                          'attendance_by' => 'teacher'
                          ]); */
                    } else {
                        Attendance::insert([
                            'school_id' => $user->school_id,
                            'teacher_id' => $teacher->id,
                            'class_id' => $input['class_id'],
                            'section_id' => $input['section_id'],
                            'student_id' => $atten->student_id,
                            'attendance' => 'P',
                            'attendance_session' => $attendance_session,
                            'remarks' => $atten->remarks,
                            'date' => $posted_date,
                            'attendance_by' => 'teacher'
                        ]);
                    }
                }
            }
        //}
		}
        return api(['data' => 'Attendance is added successfully']);
    }

   /* public function doPostAttendance($input, $user) {
		//dd($input);
        $holiday = new Holiday();
        $is_holiday = $holiday->is_holiday($input['date']);
        if ($is_holiday) {
            $input['error'] = 'Not allowed to place attendance at holiday ';
            return \Redirect::route('user.attendance')->withInput($input);
        }
		
		
        if (is_array($input['attendance'])) {
            foreach ($input['attendance'] as $key => $atten) {
                $posted_date = date('Y-m-d', strtotime($input['date']));
                $key = explode('_', $key);
                $stu_id = $key[0];
				//mari for v3
				$attend_is_taken=Attendancestatus::where('date','=',$posted_date)
				->where('school_id','=',\Auth::user()->school_id)
				->where('class_id','=',$input['class'])
				->where('section_id','=',$input['section'])
				->where('attendance_session','=',$key[1])->first();
				if(empty($attend_is_taken)){
					Attendancestatus::insert([
					'date'=>$posted_date,
					'school_id' =>\Auth::user()->school_id,
					'class_id' => $input['class'],
					'section_id' => $input['section'],
					'teacher_id' =>"",
					'attendance_session' =>$key[1]
								]);
				}
                else{
                    Attendancestatus::where('id','=',$attend_is_taken->id)->
                    where('school_id','=',\Auth::user()->school_id)->update([
                        'school_id'=>\Auth::user()->school_id
                        //'updated_at'=>date('Y-m-d h:i:s')
                    ]);
                }
                if ($atten == 'off') {
                    $teacher = \DB::table('teacher')->where('class', $input['class'])->where('section', $input['section'])->first();

                    $students = \DB::table('student')
                            ->where('student.id', $stu_id)->join('parent', 'student.parent_id', '=', 'parent.id')
                            ->select('student.name as student_name', 'parent.mobile')
                            ->first();

                    $message = urlencode($students->student_name . ' is absent on ' . date('d-m-Y') . '.');
                    // dd($message);
                    //file_get_contents('http://103.16.101.52/sendsms/bulksms?username=shtk-bestshine&password=70922728&type=0&dlr=1&destination=91' . $students->mobile . '&source=shintk&message=' . $message);
                    file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=0&dlr=1&destination=91'.$students->mobile.'&source=SCHOOL&message='. $message);

                    $atten_exist = \DB::table('attendance')->where('class_id', $input['class'])
                                    ->where('section_id', $input['section'])->where('date', $posted_date)
                                    ->where('student_id', $stu_id)->where('attendance_session', $key[1])->first();

                    if ($atten_exist) {
                        Attendance::where('id', $atten_exist->id)->update([
                            'attendance' => 'A',
                            'teacher_id' => $teacher->id,
                            'attendance_session' => $key[1],
                            'attendance_by' => 'school'
                        ]);

                    } else {
                        Attendance::insert([
                            'school_id' => $user->school_id,
                            'teacher_id' => $teacher->id,
                            'class_id' => $input['class'],
                            'section_id' => $input['section'],
                            'student_id' => $stu_id,
                            'attendance' => 'A',
                            'date' => date('Y-m-d', strtotime($input['date'])),
                            'attendance_session' => $key[1],
                            'attendance_by' => 'school'
                        ]);
                    }
                } else {
                    $atten_exist = \DB::table('attendance')->where('class_id', $input['class'])
                                    ->where('section_id', $input['section'])->where('date', $posted_date)
                                    ->where('student_id', $stu_id)->where('attendance_session', $key[1])->first();

                    if ($atten_exist) {
                        Attendance::where('class_id', $input['class'])
                                ->where('section_id', $input['section'])->where('date', $posted_date)
                                ->where('student_id', $stu_id)->where('attendance_session', $key[1])
                                ->update(['attendance' => 'P']);
                    }
                }
            }
        }
        $input['success'] = 'Attendance is added Successfully';
        return \Redirect::route('user.attendance')->withInput($input);
    }*/

    /* updated 11-5-2018*/

    public function doPostAttendance($input, $user)
    {
        //check sunday
        $days = date("N",strtotime($input['date']));

        //check session exist
        $getCurrentSession = \DB::table('session')
            ->where('school_id', \Auth::user()->school_id)
            ->where('active',1)
            ->whereRaw('? BETWEEN fromDate AND toDate', [$input['date']])
            ->first();

        //check holiday
        $holiday = new Holiday();
        $is_holiday = $holiday->is_holiday($input['date']);
        if($days == '7')
        {
            $input['error'] = ' Not allowed to place attendance at Sundays ';
            return \Redirect::route('user.attendance')->withInput($input);
        }
        elseif(!$getCurrentSession)
        {
            $input['error'] = '  Session has been expired  ';
            return \Redirect::route('user.attendance')->withInput($input);
        }
        elseif ($is_holiday)
        {
            $input['error'] = ' Not allowed to place attendance at holiday ';
            return \Redirect::route('user.attendance')->withInput($input);
        }
        else
        {
            $posted_date = $input['date'];
            $session = $input['attendance_session'];
            $attend_is_taken=Attendancestatus::where('date','=',$posted_date)
                ->where('school_id','=',\Auth::user()->school_id)
                ->where('class_id','=',$input['class'])
                ->where('section_id','=',$input['section'])
                ->where('attendance_session','=',$session)->first();

            //updated 21-2-2018
            if($session == 'am')
            {
                $period = 'Morning';
            }
            else
            {
                $period = 'Afternoon';
            }
            if(empty($attend_is_taken))
            {
                Attendancestatus::insert([
                    'date'=>$posted_date,
                    'school_id' =>\Auth::user()->school_id,
                    'class_id' => $input['class'],
                    'section_id' => $input['section'],
                    'teacher_id' =>"",
                    'attendance_session' =>$session
                ]);
                if (is_array($input['student_id']))
                {
                    foreach($input['student_id'] as $key => $value)
                    {
                        if($input['attendance'][$key] != '')
                        {
                            if ($input['attendance'][$key] == 'a')
                            {
                                //$students = \DB::table('student')
                                   // ->where('student.session_id','=',$this->active_session->id)//updated 9-4-2018
                                   // ->where('student.id', $input['student_id'][$key])
                                   // ->join('parent', 'student.parent_id', '=', 'parent.id')
                                    //->select('student.name as student_name', 'parent.mobile')
                                    //->first();

                                //updated 21-2-2018

                                //$message = urlencode('Dear Parents,'.$students->student_name . ' is absent on ' . date('d-m-Y') . '.');
                                //$message = urlencode('Dear Parents,'.$students->student_name . ' is absent on ' . date('d-m-Y') . '('.$period.' '.$session.').');

                              //  file_get_contents('http://103.16.101.52/sendsms/bulksms?username=shtk-bestshine&password=70922728&type=0&dlr=1&destination=91' . $students->mobile . '&source=shintk&message=' . $message);
                               //file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=0&dlr=1&destination=91'.$students->mobile.'&source=SCHOOL&message='. $message);
                                $students=Students::where('school_id',\Auth::user()->school_id)
                                 ->where('student.id', $input['student_id'][$key])
                               ->select('parent_id','name')
                                 ->first();

                                 $parents = \DB::table('parent')
                                    ->where('school_id',\Auth::user()->school_id)//updated 9-4-2018
                                    ->where('id', $students->parent_id)
                                    ->first();
                               

                                     $schoolname= \DB::table('school')
                                    ->where('user_id',\Auth::user()->id)
                                    ->first();
                                $smsusername= \DB::table('smsusers')
                                    ->where('school_id',\Auth::user()->school_id)
                                    ->select('username','password','type','smssource')
                                    ->first();
                                 $message = urlencode('Dear Parents,' . $students->name . ' is absent on ' . date('d-m-Y') . '('.$period.' '.$session.'). - By -' .$schoolname->school_name);
                                file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username='.$smsusername->username.'&password='.$smsusername->password.'&type=0&dlr=1&destination=91'.$parents->mobile.'&source='.$smsusername->smssource.'&message='.$message);

                                Attendance::insert([
                                    'school_id' => $user->school_id,
                                    'teacher_id' => "",
                                    'class_id' => $input['class'],
                                    'section_id' => $input['section'],
                                    'student_id' => $input['student_id'][$key],
                                    'attendance' => 'A',
                                    'date' => $input['date'],
                                    'attendance_session' => $session,
                                    'attendance_by' => 'school'
                                ]);
                            }
                            /*elseif($input['attendance'][$key] == 'l')
                            {
                                Attendance::insert([
                                    'school_id' => $user->school_id,
                                    'teacher_id' => "",
                                    'class_id' => $input['class'],
                                    'section_id' => $input['section'],
                                    'student_id' => $input['student_id'][$key],
                                    'attendance' => 'L',
                                    'date' => $input['date'],
                                    'attendance_session' => $session,
                                    'attendance_by' => 'school'
                                ]);
                            }*/
                        }
                    }
                    $input['success'] = '  Attendance is added Successfully';
                }
            }
            else
            {
                $input['error'] = '  Attendance already taken for this Class & Section ';
            }
        }
        return \Redirect::route('user.attendance')->withInput($input);
    }

    public function doUpdateAttendance($input, $user)
    {
        $posted_date = $input['date'];
        $session = $input['attendance_session'];
        $attend_is_taken=Attendancestatus::where('date','=',$posted_date)
            ->where('school_id','=',\Auth::user()->school_id)
            ->where('class_id','=',$input['class'])
            ->where('section_id','=',$input['section'])
            ->where('attendance_session','=',$session)->first();
        if($attend_is_taken)
        {
            if (is_array($input['student_id']))
            {
                foreach($input['student_id'] as $key => $value)
                {
                    if($input['attendance'][$key] != '')
                    {
                        $student_attend_taken = Attendance::where('date','=',$posted_date)
                            ->where('school_id','=',\Auth::user()->school_id)
                            ->where('class_id','=',$input['class'])
                            ->where('section_id','=',$input['section'])
                            ->where('attendance_session','=',$session)
                            ->where('student_id','=',$input['student_id'][$key])
                            ->first();
                        //updated 21-2-2018
                        if($session == 'am')
                        {
                            $period = 'Morning';
                        }
                        else
                        {
                            $period = 'Afternoon';
                        }
                        if($student_attend_taken)
                        {
                            //return 'attendance taken for student';
                            $update_attendance = Attendance::where('student_id',$input['student_id'][$key])
                                ->where('class_id',$input['class'])
                                ->where('section_id',$input['section'])
                                ->where('attendance_session',$session)
                                ->where('school_id',$user->school_id);
                            if ($input['attendance'][$key] == 'a')
                            {
                                $update_attendance = $update_attendance->update([
                                    'attendance' => 'A'
                                ]);
                                $students=Students::where('school_id',\Auth::user()->school_id)
                                 ->where('student.id', $input['student_id'][$key])
                               ->select('parent_id','name')
                                 ->first();

                                 $parents = \DB::table('parent')
                                    ->where('school_id',\Auth::user()->school_id)//updated 9-4-2018
                                    ->where('id', $students->parent_id)
                                    ->first();
                                 $schoolname= \DB::table('school')
                                    //->where('school_id',\Auth::user()->school_id)
                                    ->where('user_id',\Auth::user()->id)
                                    ->first();
                                 $message = urlencode(' Dear Parents,' . $students->name . ' is absent on ' . date('d-m-Y') . '('.$period.' '.$session.').- By-'.$schoolname->school_name);
                                //updated 21-2-2018
                                 //dd($students,$schoolname,$parents);
                                 $smsusername= \DB::table('smsusers')
                                    ->where('school_id',\Auth::user()->school_id)
                                    ->select('username','password','type','smssource')
                                    ->first();
                                file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username='.$smsusername->username.'&password='.$smsusername->password.'&type=0&dlr=1&destination=91'.$parents->mobile.'&source='.$smsusername->smssource.'&message='.$message);

                                //return 'student absent';
                                //$students = \DB::table('student')
                                    //->where('student.session_id','=',$this->active_session->id)//updated 9-4-2018
                                   // ->where('student.id', $input['student_id'][$key])
                                    //->join('parent', 'student.parent_id', '=', 'parent.id')
                                    //->select('student.name as student_name', 'parent.mobile')
                                    //->first();

                                //updated 21-2-2018

                                //$message = urlencode('Dear Parents,'.$students->student_name . ' is absent on ' . date('d-m-Y') . '.');
                                //$message = urlencode('Dear Parents,'.$students->student_name . ' is absent on ' . date('d-m-Y') . '('.$period.' '.$session.').');

                               // file_get_contents('http://103.16.101.52/sendsms/bulksms?username=shtk-bestshine&password=70922728&type=0&dlr=1&destination=91' . $students->mobile . '&source=shintk&message=' . $message);
                                //file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=0&dlr=1&destination=91'.$students->mobile.'&source=SCHOOL&message='. $message);

                                
                            }
                            /*elseif($input['attendance'][$key] == 'l')
                            {
                                //return 'student leave';
                                $update_attendance = $update_attendance->update([
                                    'attendance' => 'L'
                                ]);
                            }
                            else
                            {
                                // return 'student present';
                                $update_attendance = $update_attendance->update([
                                    'attendance' => 'P'
                                ]);
                            }*/
                            if($update_attendance)
                            {
                                $input['success'] = '  Attendance is updated Successfully';
                            }
                        }
                        else
                        {
                            //return 'attendance not taken for student';
                            if ($input['attendance'][$key] == 'a')
                            {
                                $students = \DB::table('student')
                                    ->where('student.session_id','=',$this->active_session->id)//updated 9-4-2018
                                    ->where('student.id', $input['student_id'][$key])
                                    ->join('parent', 'student.parent_id', '=', 'parent.id')
                                    ->select('student.name as student_name', 'parent.mobile')
                                    ->first();

                                //updated 21-2-2018

                                //$message = urlencode('Dear Parents,'.$students->student_name . ' is absent on ' . date('d-m-Y') . '.');
                                $message = urlencode('Dear Parents,'.$students->student_name . ' is absent on ' . date('d-m-Y') . '('.$period.' '.$session.').');

                               // file_get_contents('http://103.16.101.52/sendsms/bulksms?username=shtk-bestshine&password=70922728&type=0&dlr=1&destination=91' . $students->mobile . '&source=shintk&message=' . $message);
                                file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=0&dlr=1&destination=91'.$students->mobile.'&source=SCHOOL&message='. $message);

                                Attendance::insert([
                                    'school_id' => $user->school_id,
                                    'teacher_id' => "",
                                    'class_id' => $input['class'],
                                    'section_id' => $input['section'],
                                    'student_id' => $input['student_id'][$key],
                                    'attendance' => 'A',
                                    'date' => $input['date'],
                                    'attendance_session' => $session,
                                    'attendance_by' => 'school'
                                ]);
                            }
                           /* elseif($input['attendance'][$key] == 'l')
                            {
                                Attendance::insert([
                                    'school_id' => $user->school_id,
                                    'teacher_id' => "",
                                    'class_id' => $input['class'],
                                    'section_id' => $input['section'],
                                    'student_id' => $input['student_id'][$key],
                                    'attendance' => 'L',
                                    'date' => $input['date'],
                                    'attendance_session' => $session,
                                    'attendance_by' => 'school'
                                ]);
                            }*/
                            $input['success'] = '  Attendance is updated Successfully';
                        }
                    }
                }
            }
        }
        else
        {
            $input['error'] = '  Add Attendance to update it ';
        }
        return \Redirect::back()->withInput($input);
    }

    /*end*/


    public function saveAttendance($request, $user) {
        if (is_array($request['attendance'])) {
            foreach ($request['attendance'] as $atten) {
                $teacher = \DB::table('teacher')->where('user_id', $user->id)->first();
                $stu = json_decode($atten);
                $stu->remarks = (isset($stu->remarks) ? $stu->remarks : '');
                $atten_exist = \DB::table('attendance')->where('class_id', $request['class_id'])
                                ->where('section_id', $request['section_id'])
                                ->where('date', $request['date'])->where('student_id', $stu->student_id)->first();
                if ($atten_exist) {
                    Attendance::where('id', $atten_exist->id)->update([
                        'attendance' => $stu->attendance_type,
                        'remarks' => $stu->remarks,
                        'teacher_id' => $user->id
                    ]);
                } else {
                    Attendance::insert([
                        'class_id' => $request['class_id'],
                        'section_id' => $request['section_id'],
                        'attendance' => $stu->attendance_type,
                        'remarks' => $stu->remarks,
                        'date' => $request['date'],
                        'student_id' => $stu->student_id,
                        'teacher_id' => $teacher->id,
                        'school_id' => $user->school_id
                    ]);
                }
            }
        }
        return \api::success(['data' => 'Attendance Saved Successfully']);
    }

    public function doGetAttendanceByStudent($user) {
        $student = \DB::table('student')->where('user_id', $user->id)->first();
        if (!$student)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $attendance = Attendance::where('student_id', $student->id)->where('date', 'LIKE', '%' . date('m') . '%')->get();
        return \api::success(['data' => $attendance]);
    }

    public function doGetAttendanceByDate($user, $id, $date) {
        $sessionDate = $date . '-' . date('Y');
        $student = \DB::table('student')->where('user_id', $id)->first();
        if (!$student)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $atten = Attendance::where('student_id', $student->id)->where('date', $sessionDate)
                ->leftJoin('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                ->select('attendance.id', 'attendance.attendance', 'attendance.remarks', 'attendance.date', 'teacher.name as teacherName')
                ->orderBy('attendance.id', 'DESC')
                ->first();
        if (!$atten)
            return \api::notValid(['errorMsg' => 'Attendance Not Found']);
        return \api(['data' => $atten]);
    }

    public function doGetAttendanceByMonth($user, $month) {
        $sessionDate = $month . '-' . date('Y');
        $student = \DB::table('student')->where('user_id', $user->id)->first();
        if (!$student)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $atten = Attendance::where('student_id', $student->id)->where('date', 'LIKE', '%' . $sessionDate . '%')
                ->leftJoin('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                ->select('attendance.id', 'attendance.attendance', 'attendance.remarks', 'attendance.date', 'teacher.name as teacherName')
                ->orderBy('attendance.date', 'DESC')
                ->get();
        if (!$atten)
            return \api::notValid(['errorMsg' => 'Attendance Not Found']);
        return \api(['data' => $atten]);
    }

    public function getAttendanceByParent($user, $platform, $id, $date) {
        $logged_parent = \DB::table('parent')->where('user_id', $user->id)->first();
        if (!$student)
            return \api::notValid(['errorMsg' => 'Parent is Invalid']);
        $student = \DB::table('student')->where('user_id', $user->id)->where('parent_id', $logged_parent->id)->first();
        if (!$student)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $atten = Attendance::where('student_id', $student->id)->where('date', 'LIKE', '%' . $month . '%')
                ->leftJoin('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                ->select('attendance.id', 'attendance.attendance', 'attendance.remarks', 'attendance.date', 'teacher.name as teacherName')
                ->orderBy('attendance.id', 'DESC')
                ->get();
        if (!$atten)
            return \api::notValid(['errorMsg' => 'Attendance Not Found']);
        return \api(['data' => $atten]);
    }

    public function singleStudentAttendance($user) {
        $input = \Request::all();

        $student = \DB::table('student')->where('registration_no', $input['regno'])->first();
        $input['from'] = date('d-m-Y', strtotime($input['from']));
        $input['to'] = date('d-m-Y', strtotime($input['to']));
        $attendances = \DB::table('attendance')->where('attendance.school_id', $user->school_id)
                ->where('attendance.student_id', $student->id)
                ->whereBetween('attendance.date', [$input['from'], $input['to']])
                ->join('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                ->join('student', 'attendance.student_id', '=', 'student.id')
                ->select('attendance.id', 'attendance.attendance', 'attendance.date', 'attendance.remarks', 'attendance.attendance_by', 'teacher.name as teacher_name', 'student.name as student_name', 'student.registration_no')
                ->get();
        \Excel::create('registration' . $input['regno'], function($excel) use ($attendances) {
            $excel->sheet('Excel sheet', function($sheet) use ($attendances) {

                // $style = array(
                //      'alignment' => array(
                //          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                //      )
                //  );
                //  $sheet->getDefaultStyle()->applyFromArray($style);

                $sheet->setFontSize(12);
                $sheet->setAllBorders('thin');

                // $sheet->setWidth('I', 600);
                $sheet->loadView('users.report.attendanceExport')->with('attendances', $attendances);
            });
        })->store('xls', storage_path('/public/excel'));
        $fileURL = storage_path() . '/public/excel/registration' . $input['regno'] . '.xls';
        \Session::put('attendanceUrl', $fileURL);

        $type = 'singleStudent';
        return view('users.report.index', compact('attendances', 'fileURL', 'type'));
    }

    public function classAttendanceReports($user) {
        $input = \Request::all();
        $input['from'] = date('d-m-Y', strtotime($input['from']));
        $input['to'] = date('d-m-Y', strtotime($input['to']));



        $attendances = \DB::table('attendance')->where('attendance.school_id', $user->school_id)
                ->where('attendance.class_id', $input['class'])
                ->where('attendance.section_id', $input['section'])
                ->whereBetween('attendance.date', [$input['from'], $input['to']])
                ->join('teacher', 'attendance.teacher_id', '=', 'teacher.id')
                ->join('student', 'attendance.student_id', '=', 'student.id')
                ->select('attendance.id', 'attendance.attendance', 'attendance.date', 'attendance.remarks', 'attendance.attendance_by', 'teacher.name as teacher_name', 'student.name as student_name')
                ->get();
        \Excel::create('class attendance', function($excel) use ($attendances) {
            $excel->sheet('Excel sheet', function($sheet) use ($attendances) {

                // $style = array(
                //      'alignment' => array(
                //          'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                //      )
                //  );
                //  $sheet->getDefaultStyle()->applyFromArray($style);

                $sheet->setFontSize(12);
                $sheet->setAllBorders('thin');

                // $sheet->setWidth('I', 600);
                $sheet->loadView('users.report.attendanceExport')->with('attendances', $attendances);
            });
        })->store('xls', storage_path('/public/excel'));
        $fileURL = storage_path() . '/public/excel/class attendance.xls';
        \Session::put('attendanceUrl', $fileURL);

        $type = 'singleStudent';
        $classes = $classes = \DB::table('class')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.report.index', compact('attendances', 'fileURL', 'type', 'classes'));
    }

}
