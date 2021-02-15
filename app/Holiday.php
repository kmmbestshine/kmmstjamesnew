<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DateTime;
use DatePeriod;
use DateInterval;
class Holiday extends Model
{
    protected $table = 'holiday';

    public function doPostHoliday($request, $user)
    {
        $date = date("d-m-Y", strtotime($request['date']));
    	$holiday = Holiday::where('date', $date)->where('school_id', $user->school_id)->first();
    	if($holiday)
    	{
            $input['error'] = 'Holiday already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            $request['remarks'] = (isset($request['remarks']) ? $request['remarks'] : '');
            Holiday::insert([
                'school_id' => $user->school_id,
                'holiday' => $request['holiday'],
                'date' => $date,
                'remarks' => $request['remarks']
            ]);
            $input['success'] = 'Holiday is added successfully';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doUpdateHoliday($request, $user)
    {
        $date = date("d-m-Y", strtotime($request['date']));
    	$holiday = Holiday::where('date', $date)->where('id', '!=', $request['id'])->first();
    	if($holiday)
        {
            $input['error'] = 'Holiday already exists';
            return \Redirect::back()->withInput($input);
        }
    	else
        {
            $request['remarks'] = (isset($request['remarks']) ? $request['remarks'] : $holiday->remarks);
            Holiday::where('id', $request['id'])->update([
                'holiday' => $request['holiday'],
                'date' => $date,
                'remarks' => $request['remarks']
            ]);
            $input['success'] = 'Holiday is updated successfully';
            return \Redirect::route('master.holiday')->withInput($input);
        }
    }
    
    public function is_holiday($given_date){
        $date = new DateTime($given_date);
        $session = \DB::table('session')->where('school_id',\Auth::user()->school_id)->where('active','1')->first();
        $session_start_date = new dateTime($session->fromDate);
        $session_end_date = new dateTime($session->toDate);
        $holidays = \DB::table('holiday')->where('school_id',\Auth::user()->school_id)->select('date')->get(); 
        $holiday=array();
        foreach($holidays as $key=>$value){
            $holiday []= new DateTime($value->date);
        }
        if(($date->format('N')!=7) && (!in_array($date,$holiday)) && ($date > $session_start_date) && ($date < $session_end_date)){
            return false;
        }
        return true;
    }
}