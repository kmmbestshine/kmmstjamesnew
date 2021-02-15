<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Holiday;

class Attendance extends Model {

    protected $table = "attendance";

    public function doPostAttendanceByTeacher($input, $user, $teacher) {
        //dd(json_decode($input['attendance']));
        $holiday = new Holiday();
        $is_holiday = $holiday->is_holiday($input['date']);
        $i = 0;
        if ($is_holiday) {
            return api(['data' => 'Not allowed to place attendance at holiday ']);
        }
        foreach (json_decode($input['attendance']) as $key => $atten) {
            $i++;
            $posted_date = date('Y-m-d', strtotime($input['date']));
            $attendance_session = date('a', strtotime($input['date']));
            $atten->remarks = (isset($atten->remarks) ? $atten->remarks : '');
            $atten_exist = \DB::table('attendance')->where('class_id', $teacher->class)
                    ->where('section_id', $teacher->section)->where('attendance_session', $attendance_session)
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
                    Attendance::insert([
                        'school_id' => $user->school_id,
                        'teacher_id' => $teacher->id,
                        'class_id' => $teacher->class,
                        'section_id' => $teacher->section,
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
                            'class_id' => $teacher->class,
                            'section_id' => $teacher->section,
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
        }
        return api(['data' => 'Attendance is added successfully']);
    }

    public function doPostAttendance($input, $user) {
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
                if ($atten == 'off') {
                    $teacher = \DB::table('teacher')->where('class', $input['class'])->where('section', $input['section'])->first();

                    $students = \DB::table('student')
                            ->where('student.id', $stu_id)->join('parent', 'student.parent_id', '=', 'parent.id')
                            ->select('student.name as student_name', 'parent.mobile')
                            ->first();

                    $message = urlencode($students->student_name . ' is absent on ' . date('d-m-Y') . '.');
                    // dd($message);
                    file_get_contents('http://103.16.101.52/sendsms/bulksms?username=shtk-shinesol&password=456&type=0&dlr=1&destination=91' . $students->mobile . '&source=shintk&message=' . $message);

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
    }

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
