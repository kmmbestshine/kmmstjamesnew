<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'events';

    public function doPostEvents($request, $user)
    {
    	$check = Events::where('events', $request['events'])->where('school_id', $user->school_id)->first();
    	if(!$check)
    	{
    		Events::insert([
    			'school_id' => $user->school_id,
                'events' => $request['events']
    			]);
    		$input['success'] = 'Event is added successfully';
            return \Redirect::back()->withInput($input);
    	}
    	else
    	{
    		$input['error'] = 'Event already exists';
            return \Redirect::back()->withInput($input);
    	}
    }

    public function doDeleteEvents($id)
    {
        Events::where('id', $id)->delete();
        $input['success'] = 'Event Deleted Successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditEvents($id)
    {
        $event = Events::where('id', $id)->first();
        return view('users.master.events.edit', compact('event'));
    }

    public function doUpdateEvents($request, $user)
    {
        $check = Events::where('events', $request['events'])
                ->where('school_id', $user->school_id)
                ->where('id', '!=', $request['id'])
                ->first();
        if(!$check)
        {
            Events::where('id', $request['id'])->update([
                'events'=>$request['events']
                ]);
            $input['success'] = 'Event is added successfully';
            return \Redirect::route('master.events')->withInput($input);
        }
        else
        {
            $input['error'] = 'Event already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterEvents($user)
    {
        $events = Events::where('school_id', $user->school_id)
                    ->select('id', 'events')->get()->toArray();
        
        \Excel::create('events', function($excel) use ($events) {
            $excel->sheet('events', function($sheet) use ($events)
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
                    'Event Id', 'Event'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($events);
            });
        })->download('xls');
    }
}
