<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User, App\School,App\schooluser_plan;

use App\Http\Requests;

use Elasticquent\ElasticquentTrait;
use App\smsuser;
use App\MobileUser;
class AdminController extends Controller
{
	public function __construct(){
		
	}


    public function admin(){
    	return \Redirect::route('login');
    }

    public function adminLogin()
    {
    	if(\Auth::check())
    		return \Redirect::route('admin.dashboard');
    	return view('admin.login');
    }

    public function loginRequest(Request $request, User $user)
    {
    	$usererror = [
        	'username' => 'Email',
        	'password' => 'Password'
        ];
    	$validator = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ], $usererror);
    	$validator->setAttributeNames($usererror);
    	if($validator->fails())
            return \Redirect::route('login')->withErrors($validator);
        return $user->loginCheck($request);
    }

    public function logOut()
    {
        if(\Auth::user()->type == 'admin')
        {
    	   \Auth::logout();
    	   return \Redirect::route('login');
        }
        else{
            \Auth::logout();
            return \Redirect::route('manage');
        }
    }
    public function dashboard()
    {
        $school=array();
        $DemoSchool=array();
        $RegularSchool=array();
        $school=School::select('school.id',\DB::raw('(Select count(*) from student where school_id=school.id) as students') )->get();
        $i=0;
        foreach ($school as $key => $value) {
            $i=$i+$school[$key]->students;   
        }
        $school['student']=$i;

        $DemoSchool=School::where('schoolcategory','0')->select(\DB::raw('(Select count(*) from student where school_id=school.id) as students'))->get();
        //dd($DemoSchool);
          $j=0;
        foreach ($DemoSchool as $key => $value) {
            $j=$j+$DemoSchool[$key]->students;   
        }
        $DemoSchool['student']=$j;
         $RegularSchool=School::where('schoolcategory','1')->select(\DB::raw('(Select count(*) from student where school_id=school.id) as students'))->get();
        //dd($DemoSchool);
          $k=0;
        foreach ($RegularSchool as $key => $value) {
            $k=$k+$RegularSchool[$key]->students;   
        }
        $RegularSchool['student']=$k;
        $ActiveSchool=School::where('schoolstatus','1')->select(\DB::raw('(Select count(*) from student where school_id=school.id) as students'))->get();
        //dd($DemoSchool);
          $l=0;
        foreach ($ActiveSchool as $key => $value) {
            $l=$l+$ActiveSchool[$key]->students;   
        }
        $ActiveSchool['student']=$l;
        $InActiveSchool=School::where('schoolstatus','0')->select(\DB::raw('(Select count(*) from student where school_id=school.id) as students'))->get();
        //dd($DemoSchool);
          $m=0;
        foreach ($InActiveSchool as $key => $value) {
            $m=$m+$InActiveSchool[$key]->students;   
        }
        $InActiveSchool['student']=$m;
        //dd($RegularSchool);
        return view('admin.index',compact('school','DemoSchool','RegularSchool','ActiveSchool','InActiveSchool'));
    }
    public function viewSchool()
    {
        $Category = \Request::get('Category');
        $Status = \Request::get('Status');
        //dd($Status);
        $excelexport = \Request::get('excelexport');
        if($Category or $Status)
        {
            if($Category != '' and $Status != '')
            {
                 $schools = School::where('school.deleted_at', NULL)
                    ->leftJoin('users', 'school.id', '=', 'users.school_id')
                    ->select('school.id', 'school.school_name', 'school.email', 'school.mobile', 'school.created_at', 'school.address', 'school.city', 'school.image', 'users.username', 'users.hint_password',
                        \DB::raw('(Select count(*) from student where school_id=school.id) as students')
                        )
                    ->orderBy('id', 'DESC')
                    ->where('users.type', 'school')
                    ->where('school.schoolcategory',$Category)
                    ->where('school.schoolstatus',$Status)
                    ->orderBy('school.school_name', 'ASC')
                    ->get();
                    //dd($schools);
                    if($excelexport == 1)
                    {
                        //dd('hi');
                        \Excel::create('schools-'.date("Y_m_d_H_i_s"), function($excel) use ($schools) {
                        $excel->sheet('Excel sheet', function($sheet) use ($schools) {
                            $sheet->setFontSize(12);
                            $sheet->setAllBorders('thin');
                            $sheet->loadView('admin..school_export')->with('schools', $schools);
                        });
                    })->download('xls');
                    }

            }
            elseif ($Category and $Status == '' ) {
                $schools = School::where('school.deleted_at', NULL)
                    ->leftJoin('users', 'school.id', '=', 'users.school_id')
                    ->select('school.id', 'school.school_name', 'school.email', 'school.mobile', 'school.created_at', 'school.address', 'school.city', 'school.image', 'users.username', 'users.hint_password',
                        \DB::raw('(Select count(*) from student where school_id=school.id) as students')
                        )
                    ->orderBy('id', 'DESC')
                    ->where('users.type', 'school')
                    ->where('schoolcategory',$Category)
                   ->orderBy('school.school_name', 'ASC')
                    ->get();
                    if($excelexport == 1)
                    {
                        //dd('hi');
                        \Excel::create('schools-'.date("Y_m_d_H_i_s"), function($excel) use ($schools) {
                        $excel->sheet('Excel sheet', function($sheet) use ($schools) {
                            $sheet->setFontSize(12);
                            $sheet->setAllBorders('thin');
                            $sheet->loadView('admin..school_export')->with('schools', $schools);
                        });
                    })->download('xls');
                    }
            }
            else
            {
                $schools = School::where('school.deleted_at', NULL)
                    ->leftJoin('users', 'school.id', '=', 'users.school_id')
                    ->select('school.id', 'school.school_name', 'school.email', 'school.mobile', 'school.created_at', 'school.address', 'school.city', 'school.image', 'users.username', 'users.hint_password',
                        \DB::raw('(Select count(*) from student where school_id=school.id) as students')
                        )
                    ->orderBy('id', 'DESC')
                    ->where('users.type', 'school')
                    ->where('schoolstatus',$Status)
                   ->orderBy('school.school_name', 'ASC')
                    ->get();
                    if($excelexport == 1)
                    {
                        //dd('hi');
                        \Excel::create('schools-'.date("Y_m_d_H_i_s"), function($excel) use ($schools) {
                        $excel->sheet('Excel sheet', function($sheet) use ($schools) {
                            $sheet->setFontSize(12);
                            $sheet->setAllBorders('thin');
                            $sheet->loadView('admin..school_export')->with('schools', $schools);
                        });
                    })->download('xls');
                    }
            }

        }
        else{
        $schools = School::where('school.deleted_at', NULL)
                    ->leftJoin('users', 'school.id', '=', 'users.school_id')
                    ->select('school.id', 'school.school_name', 'school.email', 'school.mobile', 'school.created_at', 'school.address', 'school.city', 'school.image', 'users.username', 'users.hint_password',
                        \DB::raw('(Select count(*) from student where school_id=school.id) as students')
                        )
                    ->orderBy('id', 'DESC')
                    ->where('users.type', 'school')
                    ->orderBy('school.school_name', 'ASC')
                    ->get();
                    if($excelexport == 1)
                    {
                        //dd('hi');
                        \Excel::create('schools-'.date("Y_m_d_H_i_s"), function($excel) use ($schools) {
                        $excel->sheet('Excel sheet', function($sheet) use ($schools) {
                            $sheet->setFontSize(12);
                            $sheet->setAllBorders('thin');
                            $sheet->loadView('admin..school_export')->with('schools', $schools);
                        });
                    })->download('xls');
                    }
         }           
        //view()->share(compact('schools'));
    	return view('admin.viewindex',compact('schools'));	
    }

    public function createSchool()
    {
    	$schools = School::where('deleted_at', NULL)->orderBy('id','DESC')->get();
        $schooluserplans = schooluser_plan::all();
        //dd($schools);
    	return view('admin.school-input', compact('schools','schooluserplans'));
    }

    public function schoolInput(Request $request, School $school)
    {
    	$usererror = [
        	'school_name' => 'School Name',
        	'school_email' => 'School Email',
        	'school_mobile' => 'School Contact Number',
            'school_address' => 'School Address',
            'school_city' => 'School City',
            'school_image' => 'School Image'
        ];
    	$validator = \Validator::make($request->all(), [
            'school_name' => 'required',
            'school_email' => 'required|email',
            'school_mobile' => 'required|numeric',
            'school_address' => 'required',
            'school_city' => 'required',
            'school_image' => 'required|image'
        ], $usererror);
    	$validator->setAttributeNames($usererror);
    	if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $school->postSchool($request);	
    }

    public function deleteSchool($id)
    {
        $stu_exist = \DB::table('student')->where('school_id', $id)->first();
        if($stu_exist){
            $input['error'] = 'Students avaliable in this school';
            return \Redirect::route('admin.dashboard')->withInput($input);
        }
    	School::where('id', $id)->delete();
        \DB::table('users')->where('school_id', $id)->where('type', 'school')->delete();
        $input['success'] = 'School is deleted successfully';
    	return \Redirect::route('admin.dashboard')->withInput($input);
    }

    public function editSchool($id)
    {
        $school = School::where('id', $id)->first();
        $schooluserplans = schooluser_plan::all();
        return view('admin.edit', compact('school','schooluserplans'));
    }

    public function viewSchools()
    {
    	return view('admin.account-create');
    }

    public function updateSchool(Request $request, School $school)
    {
        $usererror = [
            'id' => 'School Id',
            'school_name' => 'School Name',
            'school_email' => 'School Email',
            'school_mobile' => 'School Contact Number',
            'school_address' => 'School Address',
            'school_city' => 'School City'
        ];
        $validator = \Validator::make($request->all(), [
            'id' => 'required|numeric',
            'school_name' => 'required',
            'school_email' => 'required|email',
            'school_mobile' => 'required|numeric',
            'school_address' => 'required',
            'school_city' => 'required'
        ], $usererror);
        $validator->setAttributeNames($usererror);
        if($validator->fails())
            return \Redirect::back()->withErrors($validator);
        return $school->doUpdateSchool($request);   
    }
    
   
    public function smsusername()
    {
        $smsusers=\DB::table('smsusers')
                    ->join('school','smsusers.school_id', '=', 'school.id')
                      ->select('smsusers.*', 'school.school_name', 'school.id as schoolid')
                     ->get();
         foreach ($smsusers as $key => $value) {
             //dd($value->school_id);
            $student=\DB::table('student')->where('school_id',$value->school_id)->select('parent_id')->get();
            $parentid=array_unique(array_column($student, 'parent_id'));
            $mobileUserCount = MobileUser::whereIn('user_type_id', $parentid)->count();
            $smsusers[$key]->mobileuser=$mobileUserCount;
            $smsusers[$key]->totalstudent=count($student);
            
         }
         //dd($smsusers);
        $schoolid=array_unique(array_column($smsusers, 'school_id'));
        $schools=School::select('school_name','id')->whereNotIn('id', $schoolid)->get();
        //dd($schools);
        return view('admin.smsusers',compact('schools','smsusers'));
    }
    public function exportmobileuser($id)
    {
        //$input=$request->all();
        //dd($id);
        $student=\DB::table('student')->where('school_id',$id)->select('parent_id')->get();
            $parentid=array_unique(array_column($student, 'parent_id'));
            $mobileUserCount = MobileUser::whereIn('user_type_id', $parentid)->select('user_type_id')->get();
            
            $parentids=array();
            foreach ($mobileUserCount as $key => $value) {
                $parentids[]=$value->user_type_id;
            }
            
            $parentids=array_unique($parentids);
            
            $student=\DB::table('student')
            ->whereIn('student.parent_id',$parentids)
            ->join('parent','student.parent_id','=','parent.id')
            ->join('class','student.class_id', '=', 'class.id')
            ->join('section','student.section_id', '=', 'section.id')
            ->groupBy('parent_id')
            ->select('student.*','class.class','section.section','parent.name as parentname')
            ->get();
            
        \Excel::create('MobileUser_'.$id, function($excel) use ($student) {
            $excel->sheet('Excel sheet', function($sheet) use ($student) {
                
                   $sheet->loadView('admin.mobileuser')->with('student', $student);
            });
        })->export('xls');
    
    }
    public function smsuseradd(Request $request)
    {
        $input=$request->all();
        unset($input['_token']);

        $id = smsuser::insertGetId($input);    
        if(isset($id))
        {
            return \Redirect::route('smsusers');
        }
        
    }
    public function editsmsuser(Request $request)
    {
        //dd($request);
        $input=$request->all();
        $id=smsuser::where('id', $input['userid'])->update(['username' => $input['username'],'password'=> $input['password'],'smssource'=> $input['smssource']]);
        if(isset($id))
        {
         return \Redirect::route('smsusers');   
        }

    }
    public function deletesmsuser($id)
    {
        $delete=smsuser::where('id', $id)->delete();
        if(isset($delete))
        {
         return \Redirect::route('smsusers');      
        }
    }
}
