<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $table = 'religion';

    public function doPostReligion($request, $user)
    {
    	$check = Religion::where('religion', $request['religion'])->where('school_id', $user->school_id)->first();
    	if(!$check)
    	{
    		Religion::insert([
    			'school_id'=>$user->school_id,
                'religion'=>$request['religion']
    			]);
    		$input['success'] = 'Religion is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		$input['error'] = 'Religion already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doUpdateReligion($request, $user)
    {
        $check = Religion::where('religion', $request['religion'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if(!$check)
        {
            Religion::where('id', $request['id'])->update([
                'religion'=>$request['religion']
                ]);
            $input['success'] = 'Religion is updated successfully';
            return \Redirect::route('master.religion')->withInput($input);
        }
        else
        {
            $input['error'] = 'Religion already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterReligion($user)
    {
        $religions = Religion::where('school_id', $user->school_id)
                    ->select
                    (
                        'id',
                        'religion'
                    )
                    ->get()->toArray();

        \Excel::create('religion', function($excel) use ($religions) {
            $excel->sheet('religion', function($sheet) use ($religions)
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
                    'Religion Id', 'Religion'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($religions);
            });
        })->download('xls');
    }
}
