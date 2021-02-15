<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
	public function doAttendanceReport($user)
	{
		$attendances = Attendance::where('attendance.school_id', $user->school_id)
						->leftJoin('class', 'attendance.class_id', '=', 'class.id')
						->leftJoin('section', 'attendance.section_id', '=', 'section.id')
						->leftJoin('student', 'attendance.student_id', '=', 'student.id')
						->select('student.name', 'student.roll_no', 'class.class', 'section.section', 'attendance.attendance', 'attendance.remarks', 'attendance.date', 'attendance.attendance_by')
						->orderBy('attendance.date', 'ASC')->get()->toArray();

		\Excel::create('attendancereport', function($excel) use ($attendances) {
            $excel->sheet('attendancereport', function($sheet) use ($attendances)
            {           
                $style = array(
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );

                $sheet->getDefaultStyle()->applyFromArray($style);
                $sheet->setFontSize(12);
                $sheet->setAllBorders('thin');

                $sheet->row(1, array(
                    'Student Name', 'Roll No', 'Class', 'Section', 'Attendance', 'Remarks', 'Date', 'Attendance By'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($attendances);
            });
        })->download('xls');
	}
}