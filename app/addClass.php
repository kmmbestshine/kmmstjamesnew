<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class addClass extends Model
{
    protected $table = 'class';

    private $active_session;//updated 2-6-2018

    function __construct()
    {
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
    }
    /*end*/
    /*
     updated 2-6-2018
     public function doPostClass($request, $user)
     {
        $class = addClass::where('class', $request['class'])->where('school_id', $user->school_id)->first();
         if($class)
         {
             $input['error'] = 'Class already exists';
             return \Redirect::back()->withInput($input);
         }
         else
         {
             addClass::insert([
                 'school_id' => $user->school_id,
                 'class' => $request['class']
             ]);
             $input['success'] = 'Class is added successfully';
             return \Redirect::back()->withInput($input);
         }
     }*/


    /** @ Updated 2-6-2018 by priya @ **/
    public function doPostClass($request, $user)
    {
        $sessions = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
        if(!$sessions)
        {
            $input['error'] = ' Activate Session to add Class !!! ';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            $class = addClass::where('class', $request['class'])
                ->where('school_id', $user->school_id)
                ->where('session_id',$this->active_session->id)//updated 2-6-2018
                ->first();
            if($class)
            {
                $input['error'] = ' Class already exists';
                return \Redirect::back()->withInput($input);
            }
            else
            {
                addClass::insert([
                    'school_id' => $user->school_id,
                    'class' => $request['class'],
                    'session_id'=> $this->active_session->id //updated 14-4-2018
                ]);
                $input['success'] = ' Class is added successfully';
            }
        }
        return \Redirect::back()->withInput($input);
    }
    public function doDeleteClass($id)
    {
         $exists_student = \DB::table('student')->where('class_id',$id)
             ->where('session_id',$this->active_session->id)//updated 2-6-2018
             ->first();
        /* check student exists in same session */
        if($exists_student){
            $input['error'] = 'student available in this class so can not delete';
            return \Redirect::back()->withInput($input);
        }
        addClass::where('id', $id)->delete();
        $input['success'] = 'Class is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditClass($id)
    {
        $class = addClass::where('id', $id)
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->first();
        return view('users.master.class.edit', compact('class'));
    }

    public function doUpdateClass($request, $user)
    {
       $class = addClass::where('class', $request['class'])
                    ->where('school_id', $user->school_id)
           ->where('session_id',$this->active_session->id)//updated 2-6-2018
           ->where('id', '!=', $request['id'])->first();
        if($class)
        {
            $input['error'] = 'Class already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            addClass::where('id', $request['id'])->update([
                'class' => $request['class']
            ]);
            $input['success'] = 'Class is updated successfully';
            return \Redirect::route('master.class')->withInput($input);
        }
    }

    public function doExportMasterClass($user)
    {
        $classes = addClass::where('school_id', $user->school_id)
            ->where('session_id',$this->active_session->id)//updated 2-6-2018
            ->select('id', 'class')->get()->toArray();
        // dd($classes);
        \Excel::create('class', function($excel) use ($classes) {
            $excel->sheet('class', function($sheet) use ($classes)
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
                    'Class Id', 'Class'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($classes);
            });
        })->download('xls');
    }
}
