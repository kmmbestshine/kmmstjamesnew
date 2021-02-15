<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Students;

class Caste extends Model
{
    protected $table = 'caste';

    public function doPostCaste($request, $user)
    {
    	$check = Caste::where('caste', $request['caste'])->where('school_id', $user->school_id)->first();
    	if(!$check)
    	{
    		Caste::insert([
    			'school_id'=>$user->school_id,
                'caste'=>$request['caste']
    			]);
    		$input['success'] = 'Caste is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		$input['error'] = 'Caste already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doDeleteCaste($id)
    {
        $students = Students::where('caste_id',$id)->first();
        if(count($students)>0){
            $input['error'] = "Caste can't be deleted. Caste mapped to student";
            return \Redirect::back()->withInput($input);
        }
        Caste::where('id', $id)->delete();
        $input['success'] = 'Caste is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditCaste($id)
    {
        $caste = Caste::where('id', $id)->first();
        return view('users.master.caste.edit', compact('caste'));
    }

    public function doUpdateCaste($request, $user)
    {
        $check = Caste::where('caste', $request['caste'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if(!$check)
        {
            Caste::where('id', $request['id'])->update([
                'caste'=>$request['caste']
                ]);
            $input['success'] = 'Caste is updated successfully';
            return \Redirect::route('master.caste')->withInput($input);
        }
        else
        {
            $input['error'] = 'Caste already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterCaste($user)
    {
        $castes = Caste::where('school_id', $user->school_id)
                    ->select
                    (
                        'id',
                        'caste'
                    )
                    ->get()->toArray();
        \Excel::create('castes', function($excel) use ($castes) {
            $excel->sheet('castes', function($sheet) use ($castes)
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
                    'Caste Id', 'Caste'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($castes);
            });
        })->download('xls');
    }
}
