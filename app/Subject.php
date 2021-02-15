<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TimeTable;

class Subject extends Model
{
    protected $table = 'subject';

    public function doPostSubject($request, $user)
    {
        $check = Subject::where('subject', $request['subject'])
                        ->where('school_id', $user->school_id)
                        ->first();
        if(!$check)
        {
        	Subject::insert([
        		'school_id' => $user->school_id,
                'subject' => $request['subject']
        		]);
        	$input['success'] = 'Subject is added successfully';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            $input['error'] = 'Subject already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doDeleteSubject($id)
    {
        $timetable = TimeTable::where('subject_id',$id)->first();
        if(count($timetable)>0){
            $input['error'] = "Subject can't be deleted. subject mapped to time table";
            return \Redirect::back()->withInput($input);
        }
        Subject::where('id', $id)->delete();
        $input['success'] = 'Subject is Deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditSubject($id, $user)
    {
        $subject = Subject::where('id', $id)->first();
        return view('users.master.subject.edit', compact('subject'));
    }

    public function doUpdateSubject($request, $user)
    {
        $check = Subject::where('subject', $request['subject'])
                        ->where('school_id', $user->school_id)
                        ->where('id', '!=', $request['id'])
                        ->first();
        if(!$check)
        {
            Subject::where('id', $request['id'])->update([
                'subject' => $request['subject']
                ]);
            $input['success'] = 'Subject is updated successfully';
            return \Redirect::route('master.subject')->withInput($input);
        }
        else
        {
            $input['error'] = 'Subject already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterSubject($user)
    {
        $subjects = Subject::where('subject.school_id', $user->school_id)
                    ->select
                    (
                        'subject.id',
                        'subject.subject'
                    )
                    ->get()->toArray();
        \Excel::create('subject', function($excel) use ($subjects) {
            $excel->sheet('subject', function($sheet) use ($subjects)
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
                    'Subject Id', 'Subject'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($subjects);
            });
        })->download('xls');
    }
}
