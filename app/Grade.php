<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $table = 'grade';

    public function doPostGrade($request, $user)
    {
        $amount = Grade::where('school_id', $user->school_id)->where('grade', $request['grade'])->first();
        if($amount)
        {
            $input['error'] = 'Grade already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            Grade::insert([
                'school_id' => $user->school_id,
                'min' => $request['min'],
                'max' => $request['max'],
                'grade' => $request['grade']
            ]);
            $input['success'] = 'grade is added successfully';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doDeleteGrade($id)
    {
        Grade::where('id', $id)->delete();
        $input['success'] = 'Grade is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditGrade($id)
    {
        $grade = Grade::where('id', $id)->first();
        return view('users.master.grade.edit', compact('grade'));
    }

    public function doUpdateGrade($request, $user)
    {
        $amount = Grade::where('max', '>=', $request['max'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if($amount)
        {
            $input['error'] = 'Grade already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            Grade::where('id', $request['id'])->update([
                'min' => $request['min'],
                'max' => $request['max'],
                'grade' => $request['grade']
            ]);
            $input['success'] = 'Grade is updated successfully';
            return \Redirect::route('master.grade')->withInput($input);
        }
    }

}
