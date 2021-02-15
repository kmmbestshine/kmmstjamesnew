<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    protected $table = 'bus';
    
    public function doPostBus($request, $user)
    {
    	$check = Bus::where('school_id', $user->school_id)->where('bus_no', $request['bus_no'])->first();
    	if($check)
    	{
    		$input['error'] = 'Bus already exists';
            return \Redirect::back()->withInput($input);
    	}
        $gps = Bus::where('school_id', $user->school_id)->where('gps_no', $request['gps_no'])->first();
        if($gps)
        {
            $input['error'] = 'GPS No already exists with'.$gps->bus_no.'No';
            return \Redirect::back()->withInput($input);
        }
    	else
    	{
    		Bus::insert([
    			'school_id' => $user->school_id,
    			'bus_no' => $request['bus_no'],
    			'bus_type' => $request['bus_type'],
    			'bus_owned_by' => $request['bus_owned_by'],
                'gps_no' => $request['gps_no'],
    			'capacity' => $request['capacity'],
    			'route' => $request['route'],
    			'city' => $request['city']
    		]);
    		$input['success'] = 'Bus is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    }
    
    public function doUpdateBus($request, $user)
    {
    	$check = Bus::where('school_id', $user->school_id)->where('bus_no', $request['bus_no'])->where('id', '!=', $request['id'])->first();
    	if($check)
        {
            $input['error'] = 'Bus already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            $update = Bus::where('id', $request['id'])->update([
                'bus_no' => $request['bus_no'],
                'bus_type' => $request['bus_type'],
                'bus_owned_by' => $request['bus_owned_by'],
                'gps_no' => $request['gps_no'],
                'capacity' => $request['capacity'],
                'route' => $request['route'],
                'city' => $request['city']
            ]);
            $input['success'] = 'Bus is updated successfully';
            return \Redirect::route('master.bus')->withInput($input);
        }
    }

    public function exportMasterBus($user)
    {
        $buses = Bus::where('bus.school_id', $user->school_id)
                    ->leftJoin('bus_stop', 'bus.id', '=', 'bus_stop.bus_id')
                    ->select
                    (
                        'bus.id',
                        'bus.bus_no',
                        'bus.bus_type',
                        'bus.bus_owned_by',
                        'bus.gps_no',
                        'bus.route',
                        'bus.city',
                        'bus_stop.stop',
                        'bus_stop.stop_index',
                        'bus_stop.lattitude',
                        'bus_stop.longitude'
                    )
                    ->get()->toArray();
        \Excel::create('bus', function($excel) use ($buses) {
            $excel->sheet('bus', function($sheet) use ($buses)
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
                    'Bus Id', 'Bus No', 'Bus Type', 'Bus Owner', 'GPS No', 'Route', 'City', 'Stop', 'Stop Index', 'Lattitude', 'Longitude'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($buses);
            });
        })->download('xls');
    }
}