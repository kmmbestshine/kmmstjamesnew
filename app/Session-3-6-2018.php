<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'session';
    
    public function get_active_session_id($school_id){
		return Session::select('id')->where('school_id',$school_id)->where('active','1')->first();
	}
        
    public function doPostSession($request, $user)
    {
        $check = Session::where('session', $request['session'])->where('school_id', $user->school_id)->first();
        if(!$check)
        {
            $avalable_session = Session::where('school_id', $user->school_id)->first();
            if($avalable_session){
                $input['error'] = "please contact admin";
                return \Redirect::back()->withInput($input);
            }
        	Session::insert([
                    'school_id' => $user->school_id,
                    'session' => $request['session'],
                    'fromDate' => $request['fromDate'],
                    'toDate' => $request['toDate'],
                    'active' => 1,//updated 6-11-2017 by priya
        		]);
            $input['success'] = 'Session is added successfully';
        	return \Redirect::back()->withInput($input);
        }
        else
        {
            $input['error'] = 'Session already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doGetSessions($user)
    {
    	$sessions = Session::where('school_id', $user->school_id)->get();
        if(count($sessions)>0)
            return \api::success(['data' => $sessions]);    
        return \api::success(['data' => 'No Rows Found!!!']);    
    }
    public function get_session_dates(){
        
        $sessions = Session::where('school_id', \Auth::user()->school_id)
                ->where('active','1')->first();
        $result['from']=$sessions->fromDate;
        $result['to']=$sessions->toDate;
        return $result;
    }
    public function doDeleteSession($id)
    {
        $session = Session::where('id', $id)->first();
        if(!$session)
            return \api::notValid(['errorMsg' => 'Invalid Parameter']);
        $exists_student = \DB::table('student')->where('session_id',$id)->first();
        /* check student exists in same session */
        if($exists_student){
            $input['error'] = 'student available in this session so can not delete';
            return \Redirect::back()->withInput($input);
        }
        $session = Session::where('id', $id)->delete();
        $input['success'] = 'Session is deleted successfully';
        return \Redirect::back()->withInput($input);
    }

    public function doEditSession($id)
    {
        $session = Session::where('id', $id)->first();
        return view('users.master.session.edit', compact('session'));
    }

    public function doUpdateSession($request, $user)
    {
        $check = Session::where('session', $request['session'])
                        ->where('school_id', $user->school_id)
                        ->where('id', '!=', $request['id'])
                        ->first();
        if(!$check)
        {
            Session::where('id', $request['id'])->update([
                    'session' => $request['session'],
                    'fromDate' => $request['fromDate'],
                    'toDate' => $request['toDate']
                ]);
            $input['success'] = 'Session is updated successfully';
            return \Redirect::route('master.session')->withInput($input);
        }
        else
        {
            $input['error'] = 'Session already exists';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doExportMasterSession($user)
    {
        $sessions = Session::where('school_id', $user->school_id)->select('id', 'session')->get()->toArray();
        \Excel::create('session', function($excel) use ($sessions) {
            $excel->sheet('session', function($sheet) use ($sessions)
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
                    'Session Id', 'Session'
                ));

                $sheet->row(1, function($row){
                    $row->setBackground('#dddddd');
                    $row->setFontWeight('bold');
                });
                $sheet->rows($sessions);
            });
        })->download('xls');
    }
}
