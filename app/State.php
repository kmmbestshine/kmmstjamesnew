<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'state';

    public function doExportMasterState($user, $platform)
    {
        $states = State::where('school_id', $user->school_id)->select('id', 'state')->get()->toArray();
        \Excel::create('state'.$user->id, function($excel) use ($sessions) {
            $excel->sheet('state', function($sheet) use ($sessions)
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
                    'State Id', 'State'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($states);
            });
        })->store('xls', 'exports');
    }
}
