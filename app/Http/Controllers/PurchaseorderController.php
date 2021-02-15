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

class PurchaseorderController extends Controller
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

    public function purchaseOrder(){
    $ventors = \DB::table('ventor')->get();
    return view('users.purchase.add',['ventors'=>$ventors]);
  }

  public function createPurchaseorder(){
    $input = \Request::all();
    $order_qty=$input['order_qty'];
    $p_qtu=$input['p_qtu'];
    $units=$input['units'];
    $u_price=$input['u_price'];
    $amt=$input['amt'];

     // Invoice Id
            $schoolname=\DB::table('school')->where('id', \Auth::user()->school_id)->select('school_name')->first();
            //dd($schoolname);
            $po_school_name=str_replace(" ","",$schoolname->school_name);
            $schoolname=substr($po_school_name, 0, 3);
            $check_max_po_no=\DB::table('purchase_nos')->whereNotNull('po_id')->where('school_id', \Auth::user()->school_id)->orderBy('po_id', 'desc')->first();
            if($check_max_po_no)
            {
                $schoolid=(\Auth::user()->school_id);
                $replacedata=$schoolname.'PO'.$schoolid;
                $poid=str_replace($replacedata,'',$check_max_po_no->po_id)+1;
                $polen=4-strlen($poid);
                $finalid='';
                if($polen != 0){
                    for($i=0;$i<$polen;$i++)
                    {
                        if($i==0)
                        {
                             $finalid='0'.$poid;   
                        }else
                        {
                            $finalid='0'.$finalid;
                        }
                    }

                }else{
                    $finalid=$poid;
                }
                $request['po_id']=$schoolname.'PO'.\Auth::user()->school_id.$finalid;
            }
            else
            {
                $request['po_id']=$schoolname.'PO'.\Auth::user()->school_id.'0001';
                $invoice=$request['po_id'];
            }
             $po_ids=$request['po_id'];
            // dd($po_ids,$request['po_id']);
            DB::table('purchase_nos')->insert([
                'school_id' => Auth::user()->school_id,
                'totalamount' => '0',
                 'paidamount' => $input['paid_amt'],
                'dueamount' => '0',
                'ventor_id' => $input['ventorname'],
                 'remarks' => $input['remarks'],
                'po_id' => $request['po_id'],
                 'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                
                ]);

    
   // dd($input);
     foreach($input['goodsname'] as $key =>$value)
            {
     DB::table('purchases')->insert(
                array(
                'school_id' => \Auth::user()->school_id,
                'user_id' => auth()->user()->id,
                'po_id' => $request['po_id'],
                'goods_name' => $value,
                'order_qty' => $order_qty[$key],
                'purchased_qty' => $p_qtu[$key],
                'units' => $units[$key],
                'unit_price' => $u_price[$key],
                'amount' => $amt[$key],
                'purchase_date' => date('d-m-Y'),
                'created_by' => auth()->user()->id,
                'modified_by' => auth()->user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
            }


    return back()->with("status","Purchase Order saved Successfully.");
  }
  public function viewventor(){
   // dd('jjjj');
    $ventorlist=DB::table('ventor')->get();
   // dd($ventorlist);
    return view('users.purchase.ventor.list',compact('ventorlist'));
  }
public function purchasevendordelete( $id)
    {
            $ventor = \DB::table('ventor')->where('id',$id)->delete();
            
            return redirect()->back()->with('success', 'successfully Deleted');
    }
  public function addventor(){
    return view('users.purchase.ventor.add');
  }

  public function storeVentor(){
    $input = \Request::all();
    //dd($input);
    DB::table('ventor')->insert(
                array(
                'ventor_name' => $input['ventor_name'],
                'address' => $input['address'],
                'phone_no' => $input['phone_no'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
    return back()->with("status","Ventor Created Succesfully.");
  }
 public function printpurchaseorder($id) {
$purchases = \DB::table('purchases')->where('po_id',$id)
                          ->get();
    $purchase_nos = \DB::table('purchase_nos')->where('po_id',$id)
                          ->first();
    $ventors=\DB::table('ventor')->where('id',$purchase_nos->ventor_id)
                          ->first();
    $school = School::where('id', \Auth::user()->school_id)->first();
               // dd($purchases,$purchase_nos,$ventors,$school);
        return view('users.purchase.printpo', compact('purchases','purchase_nos','ventors','school'));
    }
  public function editPurchaseorder(){
    $purchase_orders = \DB::table('purchase_nos')
    ->where('school_id', \Auth::user()->school_id)->join('ventor', 'purchase_nos.ventor_id', '=', 'ventor.id')->orderBy('purchase_nos.po_id', 'desc')->get();
    return view('users.purchase.edit',['purchase_orders'=>$purchase_orders]);
  }

  public function updatePurchaseorder($id){
    $purchases = \DB::table('purchases')->where('po_id',$id)->get();
    $purchase_nos = \DB::table('purchase_nos')->where('po_id',$id)->first();
    $ventors=\DB::table('ventor')->where('id',$purchase_nos->ventor_id)->first();
    return view('users.purchase.update',['purchases'=>$purchases,'purchase_nos'=>$purchase_nos,'ventors'=>$ventors]);

  }

   public function postPurchaseorderupdate(){
    $input = \Request::all();
   // dd($input);
    $ventor_name=$input['ventor_name'];
    $goodsname=$input['goodsname'];
    $order_qty=$input['order_qty'];
    $p_qtu=$input['p_qtu'];
    $u_price=$input['u_price'];
    $units=$input['units'];
    $amt=$input['amt'];
    $p_qtu=$input['p_qtu'];
    $tot_amt=$input['tot_amt'];
    $paid_amt=$input['paid_amt'];
    $due_amt=$input['due_amt'];
    $remarks=$input['remarks'];
    $po_id=$input['po_id'];
            DB::table('purchase_nos')->where('po_id',$po_id)
                ->update([
                'school_id' => Auth::user()->school_id,
                'totalamount' => $input['tot_amt'],
                 'paidamount' => $input['paid_amt'],
                'dueamount' => $input['due_amt'],
                'ventor_id' => $input['ventor_id'],
                 'remarks' => $input['remarks'],
                'po_id' => $input['po_id'],
                // 'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                
                ]);
            $purchase=DB::table('purchases')->where('po_id',$po_id)->get();
            foreach ($purchase as $key => $value) {
               $purchase_ids[]=$value->id;
            }
     foreach($purchase_ids as $key =>$value)
            {
     DB::table('purchases')->where('id',$value)->update(
                array(
                'school_id' => \Auth::user()->school_id,
                'user_id' => auth()->user()->id,
                'po_id' => $input['po_id'],
                'goods_name' => $goodsname[$key],
                'order_qty' => $order_qty[$key],
                'purchased_qty' => $p_qtu[$key],
                'units' => $units[$key],
                'unit_price' => $u_price[$key],
                'amount' => $amt[$key],
                'purchase_date' => date('d-m-Y'),
               // 'created_by' => auth()->user()->id,
                'modified_by' => auth()->user()->id,
               // 'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ));
            }
    return back()->with("status","Purchase Order saved Successfully.");
  }
  public function purchasedelete( $id)
    {
            $purchase_nos = \DB::table('purchase_nos')->where('po_id',$id)->delete();
            $purchase = \DB::table('purchases')->where('po_id',$id)->delete();
            return redirect()->back()->with('success', 'successfully Deleted');
    }

   public function purchaseReport(){
   $rempurchase = \DB::table('purchase_nos')->join('ventor', 'purchase_nos.ventor_id', '=', 'ventor.id')->orderBy('purchase_nos.created_at', 'DEC')
   ->where('purchase_nos.dueamount', '>', 0)->select('purchase_nos.po_id','purchase_nos.totalamount','purchase_nos.paidamount','purchase_nos.dueamount','purchase_nos.created_at','purchase_nos.id as purchaseId')->get();
        $paidpurchase = \DB::table('purchase_nos')->join('ventor', 'purchase_nos.ventor_id', '=', 'ventor.id')->orderBy('purchase_nos.created_at', 'DEC')->where('purchase_nos.dueamount', '<=', 0)->get();
        $ventors=\DB::table('ventor')->get();
        return view('users.purchase.list', compact('paidpurchase', 'rempurchase','ventors'));
  }

   public function update( $id)
    {
            $purchase = \DB::table('purchase_nos')->where('id',$id)->first();
            $purchase->dueamount = 0;
            $purchase->paidamount = $purchase->totalamount;
             DB::table('purchase_nos')->where('id',$id)->update(
                array(
                   // 'totalamount' => $input['tot_amt'],
                 'paidamount' => $purchase->paidamount,
                'dueamount' => $purchase->dueamount,
               
                'updated_at' => date('Y-m-d H:i:s')
                ));
           
            return redirect()->back()->with('success_message', 'successfully paid Remining Balance');
    }
}
