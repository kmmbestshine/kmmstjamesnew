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
use App\FurnitureCategory;
use App\FurnitureDetails;
use App\FurnitureSubCategory;
use App\FurnitureDistribution;
use DB;
use Validator,
    Redirect,
    Auth,
    api;
use App\Http\Requests\AddFurniture;
use App\Http\Requests\SaveDistribute;

class AccountController extends Controller
{
   private $active_session;//updated 2-6-2018

    public function __construct()
    {
        /*updated 2-6-2018*/
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
        if (Auth::check()) {
            $this->user = \Auth::user();
            if(Auth::user()->type == 'school' || Auth::user()->type == 'user_role'){
                $classes = addClass::where('school_id', \Auth::user()->school_id)
                    ->where('session_id','=',$this->active_session->id)//updated 2-6-2018
                    ->get();
                $students = Students::where('school_id', \Auth::user()->school_id)->count();
                $employees = Employee::where('school_id', \Auth::user()->school_id)->count();
                $busCount = Bus::where('school_id', \Auth::user()->school_id)->count();
                $school_image = School::where('id', \Auth::user()->school_id)->first();
                $examtypes = Exam::where('school_id', \Auth::user()->school_id)->get();
                $birthdays = Students::where('student.dob', 'LIKE', '%' . date('d-m') . '%')
                    ->where('student.session_id','=',$this->active_session->id)//updated 2-6-2018
                    ->where('student.school_id', \Auth::user()->school_id)->leftJoin('class', 'student.class_id', '=', 'class.id')->select('student.id', 'student.name', 'student.roll_no', 'class.class')->get();
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
                if($userplandetail)
                {
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

    public function accountsindex(){
    return view('users.accounts.index');
  }
  public function category(){
        $sectors = \DB::table('account_sectors')->get();
    $account_type = 'income';
    $incomes = \DB::table('accounts')->where('type', $account_type)
                          ->orderBy('id', 'desc')
                          ->take(50)
                          ->get();
    $account_type = 'expense';
    $expenses = \DB::table('accounts')->where('type', $account_type)
                          ->orderBy('id', 'desc')
                          ->take(50)
                          ->get();
    $sector = [];
    return view('users.accounts.sectors',compact('sectors','sector','incomes','expenses'));
   }
   public function storeSector(){
    $input = \Request::all();
    //dd($input);
    DB::table('account_sectors')->insert(
                array(
                'school_id' => \Auth::user()->school_id,
                'user_id' => auth()->user()->id,
                'name' => $input['name'],
                'type' => $input['type'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
    return back()->with("status","Account Sector Created Succesfully.");
  }
   public function deleteSector($id){
    $input = \Request::all();
    //dd($input,$id);
    DB::table('account_sectors')->where('id',$id)->delete();
    return back()->with("status","Account Sector Deleted Succesfully.");
  }
  public function income(){
    $sectors = \DB::table('account_sectors')->where('type', 'income')->get();
    return view('users.accounts.income',['sectors'=>$sectors]);
  }
  public function storeIncome(){
    $input = \Request::all();
    //dd($input);
    DB::table('accounts')->insert(
                array(
                'school_id' => \Auth::user()->school_id,
                'user_id' => auth()->user()->id,
                'name' => $input['received_from'],
                'type' => 'income',
                'income_type' => $input['sector_name'],
                'amount' => $input['amount'],
                'date' => $input['date'],
                'description' => $input['description'],
                'mode' => $input['pmMode'],
                'cheq_no' => $input['cheqno'],
                'cheq_date' => $input['cheqdate'],
                'cheq_bankname' => $input['bank_name'],
                'trans_no' => $input['trans_no'],
                'online_bankname' => $input['bank_name1'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
    return back()->with("status","Income saved Successfully.");
  }
  public function createBankaccount(){
    return view('users.accounts.bankaccount');
  }
  public function postBankaccount(){ 
    $input = \Request::all();
   // dd($input);
    DB::table('bank_account')->insert(
                array(
                'account_no' => $input['acc_no'],
                'bank_name' => $input['bank_name'],
                'branch_name' => $input['branch_name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
    return back()->with("status","Bank Account Created Succesfully.");
  }

public function incomeList(){
    $incomes = [];
    $sectors = \DB::table('account_sectors')->where('type', 'income')->get();
   

    return view('users.accounts.income-list',['incomes'=>$incomes,'sectors'=>$sectors]);
  }
  public function deleteincome($id)
    {    $productcategory1 = \DB::table('accounts')->get();
         $productcategory = \DB::table('accounts')->where('accounts.id',$id)->first();
         dd($productcategory,$id,$productcategory1);
            $message = $productcategory->delete();
            if ($message) {
                return redirect()->route('acc.list.income')->with('status', 'successfully Deleted');
            }
    }
  
  public function postincomeList(){
    $input = \Request::all();
    $from=$input['from_date'];
    $to=$input['to_date'];
    $incomes=[];
    if($input['sector_name'] == '0'){
        $incomes = \DB::table('accounts')->where('type', 'income')
                            ->whereBetween('date', [$input['from_date'], $input['to_date']])->get();
        //  $sector="All";
    }else{
        $incomes = \DB::table('accounts')->where('type', 'income')
                            ->whereBetween('date', [$input['from_date'], $input['to_date']])->where('income_type', $input['sector_name'])->get();
     // $sector=$input['sector_name'];
    }
    $sectors = \DB::table('account_sectors')->where('type', 'income')->get();
     $school=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
    // dd($incomes,$sectors,$school,$sector,$from,$to);
    return view('users.accounts.income-list',compact('incomes','sectors','school','input'));
  }

  public function expense(){
    //dd('kkkkkk');
    $sectors = \DB::table('account_sectors')->where('type', 'expense')
                          ->get();
     $bank_account = \DB::table('bank_account')->get();
    return view('users.accounts.expense',['sectors'=>$sectors,'bank_account'=>$bank_account]);
  }

  public function storeexpense(){
    $input = \Request::all();
        DB::table('accounts')->insert(
                array(
                'school_id' => \Auth::user()->school_id,
                'user_id' => auth()->user()->id,
                'name' => $input['paid_to'],
                'type' => 'expense',
                'income_type' => $input['sectorname'],
                'amount' => $input['amount'],
                'date' => $input['date'],
                'cash_deposit' => $input['cash_deposit'],
                'description' => $input['description'],
                'mode' => $input['pmMode'],
                'cheq_no' => $input['cheqno'],
                'cheq_date' => $input['cheqdate'],
                'cheq_bankname' => $input['bank_name'],
                'trans_no' => $input['trans_no'],
                'online_bankname' => $input['bank_name1'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
    return back()->with("status","expense saved Successfully.");
  }
  public function deleteexpense($id)
    {
         $productcategory = \DB::table('accounts')->where('accounts.id',$id)->limit(1);
            $message = $productcategory->delete();
            if ($message) {
                return redirect()->route('acc.list.expense')->with('status', 'successfully Deleted');
            }
    }

  public function expenseList(){
    $sectors = \DB::table('account_sectors')->where('type', 'expense')->get();
    $expenses = [];
    return view('users.accounts.expense-list',['expenses'=>$expenses,'sectors'=>$sectors]);
  }

  public function postexpenseList(){
    $input = \Request::all();
if($input['sector_name'] == '0'){
        $expenses = \DB::table('accounts')->where('type', 'expense')->whereBetween('date', [$input['from_date'], $input['to_date']])->get();
    }else{
        $expenses = \DB::table('accounts')->where('type', 'expense')->whereBetween('date', [$input['from_date'], $input['to_date']])->where('income_type', $input['sector_name'])
                          ->get();
    }
    $sectors = \DB::table('account_sectors')->where('type', 'expense')
                          ->get();
    $school=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
    return view('users.accounts.expense-list',compact('expenses','sectors','school','input'));
  }

  public function accountsconsolidate(){
    $input = \Request::all();
    $sectors = \DB::table('account_sectors')->where('type', 'income')
                          ->get();
    return view('users.accounts.report',['sectors'=>$sectors]);
  }

  public function accconsolidatereport(){
    $input = \Request::all();
    if($input){
         $incomes_sectorid = \DB::table('accounts')->where('type', 'income')
                            ->whereBetween('date', [$input['from_date'], $input['to_date']])
                            ->get();
         foreach ($incomes_sectorid as $key => $value) {
            $sector_id[]=$value->income_type;
         }

         $collection = collect($sector_id);
            $sector_id = $collection->unique()->values()->all();
            $getsector=[];
         foreach ($sector_id as $key => $value) {
             $getsector[]= \DB::table('account_sectors')->where('type', 'income')->where('id', $value)
                            ->get();
         }
        // dd($sector_id,$getsector);
    }
$tot_amt=0;
    
    foreach ($getsector as  $sector) {
        foreach ($sector as  $sector1) {
          $getincome = \DB::table('accounts')->where('type', 'income')
                        ->whereBetween('date', [$input['from_date'], $input['to_date']])
                        ->where('income_type',$sector1->id)->get();
            $totincome = \DB::table('accounts')->where('type', 'income')
                        ->whereBetween('date', [$input['from_date'], $input['to_date']])
                        ->where('income_type',$sector1->id)->sum('amount'); 

                    $sub_amount=0;
            foreach ($getincome as $key => $value) {
               $sub_amount += $value->amount;
            }

             $sector1->sub_amount = $sub_amount;
        }
       
    }
    // for expense
    if($input){
         $expense_sectorid = \DB::table('accounts')->where('type', 'expense')
                            ->whereBetween('date', [$input['from_date'], $input['to_date']])
                            ->get();
         foreach ($expense_sectorid as $key => $value) {
            $sector_ids[]=$value->income_type;
         }

         $collection = collect($sector_ids);
            $sector_ids = $collection->unique()->values()->all();
            $getsectors=[];

         foreach ($sector_ids as $key => $value) {
             $getsectors[]= \DB::table('account_sectors')->where('type', 'expense')->where('id', $value)
                            ->get();
         }
        // dd($sector_id,$getsector);
    }

     
    foreach ($getsectors as  $sectors) {
        foreach ($sectors as  $sectors1) {
            //dd($sectors1->id);
          $getexpense = \DB::table('accounts')->where('type', 'expense')
                        ->whereBetween('date', [$input['from_date'], $input['to_date']])
                        ->where('income_type',$sectors1->id)->get();
                   

                    $sub_expamount=0;
            foreach ($getexpense as $key => $value) {
               $sub_expamount += $value->amount;
            }

             $sectors1->sub_expamount = $sub_expamount;
        }
       
    }
    //dd($getsector);
    $school=\DB::table('school')->where('id', \Auth::user()->school_id)->first();
    $tot_amt=0;
    $tot_amt += $sub_amount;
   // dd($tot_amt);
   
    return view('users.accounts.report',['getsector'=>$getsector,'getsectors'=>$getsectors,'school'=>$school,'input'=>$input]);
  }
}
