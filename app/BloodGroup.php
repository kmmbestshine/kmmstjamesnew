<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BloodGroup extends Model
{
    protected $table = 'blood_group';

    public function doPostBloodGroup($request, $user)
    {
    	$check = BloodGroup::where('blood_group', $request['blood_group'])->where('school_id', $user->school_id)->first();
    	if(!$check)
    	{
    		BloodGroup::insert([
    			'school_id'=>$user->school_id,
                'blood_group'=>$request['blood_group']
    			]);
    		$input['success'] = 'Blood Group is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		$input['error'] = 'Blood Group already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doUpdateBloodGroup($request, $user)
    {
        $check = BloodGroup::where('blood_group', $request['blood_group'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if(!$check)
        {
            BloodGroup::where('id', $request['id'])->update([
                'blood_group'=>$request['blood_group']
                ]);
            $input['success'] = 'Blood Group is updated successfully';
            return \Redirect::route('master.group')->withInput($input);
        }
        else
        {
            $input['error'] = 'Blood Group already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterBloodGroup($user)
    {
        $groups = BloodGroup::where('school_id', $user->school_id)
                    ->select
                    (
                        'id',
                        'blood_group'
                    )
                    ->get()->toArray();
        \Excel::create('bloodgroup', function($excel) use ($groups) {
            $excel->sheet('bloodgroup', function($sheet) use ($groups)
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
                    'Blood Group Id', 'Blood Group'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($groups);
            });
        })->download('xls');
    }
}
