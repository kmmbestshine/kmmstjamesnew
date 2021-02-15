<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff';

    public function doPostStaffType($request, $user)
    {
    	$check = staff::where('staff_type', $request['staff_type'])->where('school_id', $user->school_id)->first();
    	if(!$check)
    	{
	    	Staff::insert([
	    			'staff_type' => $request['staff_type'],
	    			'school_id' => $user->school_id
	    		]);
	    	$input['success'] = 'Staff Type is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    	 	$input['error'] = 'Staff Type already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doDeleteStaffType($id)
    {
        Staff::where('id', $id)->delete();
        $input['success'] = 'Staff Type is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditStaffType($id)
    {
        $staff = Staff::where('id', $id)->first();
        return view('users.master.staff.edit', compact('staff'));
    }

    public function doUpdateStaffType($request, $user)
    {
        $check = staff::where('staff_type', $request['staff_type'])
                ->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if(!$check)
        {
            Staff::where('id', $request['id'])->update([
                    'staff_type' => $request['staff_type']
                ]);
            $input['success'] = 'Staff Type is added successfully';
            return \Redirect::route('master.staff')->withInput($input);
        }
        else
        {
            $input['error'] = 'Staff Type already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterStaffType($user)
    {
        $staffs = Staff::where('school_id', $user->school_id)
                    ->select('id', 'staff_type')->get()->toArray();
        
        \Excel::create('staffs', function($excel) use ($staffs) {
            $excel->sheet('staffs', function($sheet) use ($staffs)
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
                    'Staff Id', 'Staff'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($staffs);
            });
        })->download('xls');
    }
}
