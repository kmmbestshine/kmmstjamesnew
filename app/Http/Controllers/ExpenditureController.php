<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\addClass;
use App\Attendance;
use App\BloodGroup;
use App\Bus;
use App\BusStop;
use App\Caste;
use App\Driver;
use App\Employee;
use App\Events;
use App\Exam;
use App\Fee;
use App\FeeFrequency;
use App\FeeStructure;
use App\FeeSummary;
use App\Feedback;
use App\Gallery;
use App\Holiday;
use App\Homework;
use App\Leave;
use App\Library;
use App\NotificationType;
use App\Post;
use App\Report;
use App\Result;
use App\StuParent;
use App\Religion;
use App\School;
use App\Section;
use App\Session;
use App\Splash;
use App\Staff;
use App\Salary;
use App\Students;
use App\Subject;
use App\TimeTable;
use App\Teacher_attendance;
use App\User;
use App\Installment;
use App\FeeStructuree;
use App\Payment;
use App\Expenditure;
use App\ExpCategory;

use Validator,
    Redirect,
    Auth,
    api;


use App\Http\Requests\ExpenditureCreate;

//INSERT INTO `schooluser_plan` (`id`, `Modules`, `Basic`, `Standard`, `Premium`, `created_at`, `updated_at`) VALUES (NULL, 'EXPENDITURE', '0', '1', '1', NULL, NULL), (NULL, 'FURNITURE', '0', '1', '1', NULL, NULL);
class ExpenditureController extends Controller
{
     public function __construct() {
        // $activeRoute = \Route::getCurrentRoute()->getAction()['as'];
        // view()->share(compact('activeRoute'));
        if (Auth::check()) {
            $this->user = \Auth::user();
            if(Auth::user()->type == 'school' || Auth::user()->type == 'user_role'){
                $classes = addClass::where('school_id', \Auth::user()->school_id)->get();
                $students = Students::where('school_id', \Auth::user()->school_id)->count();
                $employees = Employee::where('school_id', \Auth::user()->school_id)->count();
                $busCount = Bus::where('school_id', \Auth::user()->school_id)->count();
                $school_image = School::where('id', \Auth::user()->school_id)->first();
                $examtypes = Exam::where('school_id', \Auth::user()->school_id)->get();
                $birthdays = Students::where('student.dob', 'LIKE', '%' . date('d-m') . '%')->where('student.school_id', \Auth::user()->school_id)->leftJoin('class', 'student.class_id', '=', 'class.id')->select('student.id', 'student.name', 'student.roll_no', 'class.class')->get();
            }else{
                $classes = addClass::where('school_id', \Auth::user()->school_id)->get();
                $employeeObj = Employee::where('user_id', \Auth::user()->id)->where('school_id', \Auth::user()->school_id)->first();
                $students = Students::where('school_id', \Auth::user()->school_id)->where('class_id', $employeeObj->class)->where('section_id', $employeeObj->section)->count();
                $employees = Employee::where('school_id', \Auth::user()->school_id)->where('class', $employeeObj->class)->where('section', $employeeObj->section)->count();
                $school_image = School::where('id', \Auth::user()->school_id)->first();
            }

            $roler = [];
            if (Auth::user()->type == 'user_role') {
                $roleuser = \DB::table('user_role')->where('role_id', Auth::user()->id)->get();
                foreach ($roleuser as $role) {
                    array_push($roler, $role->value);
                }
            }
            $userplans=[];
            if(!$school_image->userplan){
                $school_image->userplan='Basic';
            }
            if($school_image->userplan){
                $userplandetail= \DB::table('schooluser_plan')->where($school_image->userplan, 1)->select('Modules')->get();
                if($userplandetail){
                    foreach ($userplandetail as $key => $value) {
                        array_push($userplans, $userplandetail[$key]->Modules);
                    }
                }
            }
            if($school_image->userplanAdded){
                $explodearray=explode(",",$school_image->userplanAdded);
                $userplansadded=\DB::table('schooluser_plan')->whereIn('id', $explodearray)->select('Modules')->get();
                if($userplansadded){
                    foreach ($userplansadded as $key => $value) {
                        array_push($userplans, $userplansadded[$key]->Modules);
                    }
                }
            }
            view()->share(compact('classes', 'employees', 'students', 'school_image', 'birthdays', 'examtypes', 'busCount', 'abses', 'roler','userplans'));
        }
    }
    //
   public function storeCategory(){
        $request=\Request::all(); 
        $is_exist=ExpCategory::where('category','=',$request['newcategory'])->where('school_id',\Auth::user()->school_id)->first();
        if($is_exist){
            $data="";
        }else{
           // $empid=EmpDetails::where('email',\Auth::user()->email)->first();
            ExpCategory::insert(['school_id'=>\Auth::user()->school_id,'user_id'=>\Auth::user()->id,'category'=>$request['newcategory']]);
            $data=$request['newcategory'];
        }
    return $data;
   }

    public function expList(){
    	$permission=array('add','edit','view','delete','download');
    	$expenditurelist=array();
        $expenditurelist=Expenditure::where('school_id','=',\Auth::user()->school_id)
        ->where('is_delete','=','0')
        ->get();
        $deletedlist=array();
        $deletedlist=Expenditure::where('school_id','=',\Auth::user()->school_id)
        ->where('is_delete','=','1')
        ->get();
    return view('users.exp.list',compact('permission','expenditurelist','deletedlist'));
    }

    public function expCreate(){
        $expenditure=ExpCategory::where('school_id',\Auth::user()->school_id)->get();
    return view('users.exp.add',compact('expenditure'));
    }

    public function expensesreport(){
        $expenditure=ExpCategory::where('school_id',\Auth::user()->school_id)->get();
    return view('users.exp.report_view',compact('expenditure'));
    }

    public function expensesreportGenerate(){
        $input = \Request::all();

        if(($input["fromexpdate"]=='')&&($input["category"]=='')){
            $input["Error"]="Please Select Any one for Generate Report";
            return \Redirect::back()->withInput($input);
        }else{
            if(($input["fromexpdate"]!='')&& ($input["toexpdate"]!='')&& ($input["category"]!='')){        
                    $expenditureList =\DB::table('expenditure_details')->where('school_id', \Auth::user()->school_id)                 
                    ->whereBetween('date', array($input["fromexpdate"], $input["toexpdate"]))
                    ->where('category',$input["category"])
                    ->where('is_delete','=','0')
                    ->get();
                   
                $file_name="Expenditure_Report_From".$input["fromexpdate"]."To".$input["toexpdate"]."_".$input["category"];
                $reporthead="Expenditure Report From- ".$input["fromexpdate"]. " To -" . $input["toexpdate"]." and Category-".  $input["category"];
            }else{
                if(($input["fromexpdate"]!='')&& ($input["toexpdate"]!='')){                   
                    $expenditureList=Expenditure::where('school_id','=',\Auth::user()->school_id)
                        ->where('is_delete','=','0')
                        ->whereBetween('date', array($input["fromexpdate"], $input["toexpdate"]))
                        ->get();
                    $file_name="Expenditure_Report_From".$input["fromexpdate"]."To".$input["toexpdate"];
                    $reporthead="Expenditure Report From".$input["fromexpdate"]."To".$input["toexpdate"];
                }
                if($input["category"]!=''){
                    $expenditureList=Expenditure::where('school_id','=',\Auth::user()->school_id)
                        ->where('is_delete','=','0')
                        ->where('category',$input["category"])
                        ->get();
                    $file_name="Expenditure_Report_".$input["category"];
                    $reporthead="Expenditure Report Based on Category - ".$input["category"];
                }
            }
            /*if(count((array)$expenditureList)>0){
                \Excel::create($file_name, function($excel) use ($expenditureList,$reporthead) {
                    $excel->sheet('Excel sheet', function($sheet) use ($expenditureList,$reporthead) {
                        $sheet->loadView('users.exp.report_load')->with('expenditureList',$expenditureList)->with('reporthead',$reporthead);
                    });
                })->store('xls', storage_path('/public/excel'))->export('xls');
            }else{
                $input["Error"]="Report Not Avialable";
                return \Redirect::back()->withInput($input);
            }
        }
    }*/
    foreach ($expenditureList as $key => $value) {
                
                $amount[]=$value->amount;
               
            }
            $totamt = array_sum($amount);
            $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->select('school_name')->first();
            //dd($schoolname);
        }
         return view('users.exp.schoolExprepdet', compact('schoolname','reporthead','file_name','totamt','expenditureList','name', 'date', 'purpose', 'category', 'comment', 'amount', 
            'cheque_no', 'cheque_date', 'bank_name', 'online_bankname', 'transaction_no', 'payment_mode', 'approved_by','given_by','created_at'));
    }

    public function deleteExpense($id){
        $expenseView=Expenditure::where('id',$id)->update(['is_delete'=>'1']);
        \Session::flash('Success-exp','Expenditure Deleted Successfully');
    return redirect()->route('user.expList');
    }

    public function viewExpense($id){
        $expenditurelist=Expenditure::where('id',$id)->first();
    return view('users.exp.view',compact('expenditurelist'));
    }

    public function expenseUpdate(ExpenditureCreate $request,$id){
        $input=$request->all();
        dd('hi',$id);
        
        Expenditure::where('id',$id)->update([
                'date'=>date('Y-m-d',strtotime($input['expdate'])),
                'name'=>$input['toname'],
                'purpose'=>$input['purpose'],
                'category'=>$input['category'],
                'descrption'=>$input['Description'],
                'quantity'=>$input['quantity'],
                'comment'=>$input['comment'],
                'amount'=>$input['amount'],
                'approved_by'=>$input['approvedby'],
                'given_by'=>$input['givenby']
            ]);
        \Session::flash('Success-exp','Expenditure Updated Successfully');
    return redirect()->route('user.expList');
    }

    public function editExpense($id){
        $expenseView=Expenditure::where('id',$id)->first();
        $expenditure=ExpCategory::where('school_id',\Auth::user()->school_id)->get();
    return view('users.exp.edit',compact('expenditure','expenseView'));
    }

    public function expensePost(ExpenditureCreate $request){
        $input=$request->all();
        //dd($input);
        Expenditure::insert([
                'school_id'=>\Auth::user()->school_id,
                'user_id'=>\Auth::user()->id,
                'date'=>date('Y-m-d',strtotime($input['expdate'])),
                'name'=>$input['toname'],
                'purpose'=>$input['purpose'],
                'category'=>$input['category'],
                //'descrption'=>$input['Description'],
                //'quantity'=>$input['quantity'],
                //'comment'=>$input['comment'],
                'cheque_no'=>$input['cheqno'],
                'cheque_date'=>$input['cheqdate'],
                'bank_name'=>$input['bank_name'],
                'online_bankname'=>$input['bank_name1'],
                'transaction_no'=>$input['trans_no'],
                'payment_mode'=>$input['pmMode'],
                'amount'=>$input['amount'],
                'approved_by'=>$input['approvedby'],
                'given_by'=>$input['givenby']
            ]);
        \Session::flash('Success-exp','Expenditure Added Successfully');
    return redirect()->route('user.expList');
    }
    
}
