<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB, Redirect;

class Section extends Model
{
    protected $table = 'section';

    private $active_session;//updated 2-6-2018

    function __construct()
    {
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
    }

    /*end*/

    public function doPostSection($request, $user)
    {
    	$sec = Section::where('class_id', $request['class'])
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->where('section', $request['section'])->where('school_id', $user->school_id)->first();
    	if(!$sec)
    	{
    		Section::insert([
    			'school_id' => $user->school_id, 
                'class_id' => $request['class'], 
                'section' => $request['section'],
                'subjects' => json_encode($request['subjects']),
                'session_id' => $this->active_session->id //updated 2-6-2018
    		]);
    		$input['success'] = 'Section is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		$input['error'] = 'Section already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doDeleteSEction($id)
    {
        $exists_student = \DB::table('student')
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->where('section_id',$id)->first();
        /* check student exists in same session */
        if($exists_student){
            $input['error'] = 'student available in this section so can not delete';
            return \Redirect::back()->withInput($input);
        }
        $section = Section::where('id', $id)->delete();
        $input['success'] = 'Section is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditSection($id, $user)
    {
        $section = Section::where('id', $id)->first();
        $classes = \DB::table('class')->where('school_id', $user->school_id)
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->get();
        $subjects = DB::table('subject')->where('school_id', $user->school_id)->get();
        return view('users.master.section.edit', compact('section', 'classes', 'subjects'));
    }

    public function doUpdateSection($request, $user)
    {
        $sec = Section::where('class_id', $request['class'])
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->where('section', $request['section'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if(!$sec)
        {
            Section::where('id', $request['id'])->update([ 
                'class_id' => $request['class'], 
                'section' => $request['section'],
                'subjects' => json_encode($request['subjects'])

            ]);
            $input['success'] = 'Section is updated successfully';
            return \Redirect::route('master.section')->withInput($input);
        }
        else
        {
            $input['error'] = 'Session already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterSection($user)
    {
        $section = [];
        $allsections = Section::where('section.school_id', $user->school_id)
            ->where('section.session_id',$this->active_session->id)//updated 2-6-2018
                    ->leftJoin('class', 'section.class_id', '=', 'class.id')
                    ->select
                    (
                        'section.id', 
                        'section.section',
                        'class.id as class_id',
                        'class.class',
                        'section.subjects'
                    )
                    ->orderBy('section.id', 'ASC')
                    ->get();
        foreach($allsections as $section)
        {
            $subjects = \DB::table('subject')->whereIn('id', json_decode($section->subjects))->select('subject')->get();
            $subs = [];
            foreach($subjects as $subject)
            {
                $subs[] = $subject->subject;
            }
            $mainsub = implode(", ", $subs);
            $sections[] = array(
                            'id' => $section->id,
                            'section' => $section->section,
                            'class_id' => $section->class_id,
                            'class' => $section->class,
                            'subjects' => $mainsub
                            );
        }
        \Excel::create('section', function($excel) use ($sections) {
            $excel->sheet('section', function($sheet) use ($sections)
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
                    'Section Id', 'Section', 'Class Id', 'Class', 'Subjects'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($sections);
            });
        })->download('xls');
    }
}
