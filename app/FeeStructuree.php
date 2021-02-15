<?php
namespace App;
use Redirect, api, DB, Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Auth;


class FeeStructuree extends Model
{
    protected $table = 'fee_structure';

    private $active_session;//updated 2-6-2018
    function __construct()
    {
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
    }

    /*end*/
    public $timestamps = true;
	
	public function addFees($request,$user){
		$inputs = $request->input();
		$rows = $inputs['rows'];
		$message = '';
		for($i=0;$i<$rows;$i++){
			
			if($inputs['payment_type_'.$i.''] != 'ANNUAL'){
				$result = feestructuree::insert([
					'school_id' => Auth::user()->school_id,
					'class_id' => $inputs['class_id'],
					'session' => $inputs['session'],
					'fees_name' => $inputs['fee_name_'.$i.''],
					'student_type'=> $inputs['student_type_'.$i.''],
					'payment_type' => $inputs['payment_type_'.$i.''],
					'amount' => $inputs['amount_'.$i.''],
					'last_date' => $inputs['last_date_'.$i.''],
					'fine' => $inputs['fine_'.$i.''],					
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
				if($result){
					$message = 'Fees inserted';
				}else{
					$message = 'Fees not inserted';
				}
				
			}else{
                if(empty($inputs['installment_id_'.$i.''])){
                    return redirect()->back()->with('error', "fill installment");
                }
				$result = feestructuree::insert([
					'school_id' => Auth::user()->school_id,
					'class_id' => $inputs['class_id'],
					'session' => $inputs['session'],
					'fees_name' => $inputs['fee_name_'.$i.''],
					'student_type'=> $inputs['student_type_'.$i.''],
					'payment_type' => $inputs['payment_type_'.$i.''],
					'installment_id' => json_encode($inputs['installment_id_'.$i.'']),
					'amount' => $inputs['amount_'.$i.''],
					'last_date' => $inputs['last_date_'.$i.''],
					'fine' => $inputs['fine_'.$i.''],					
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
				if($result){
					$message = 'Fees inserted';
				}else{
					$message = 'Fees not inserted';
				}
			}
		}
		return redirect()->back()->with('success', $message );
	}
	
	public function deleteFee($id){
		$result = feestructuree::where('id',$id)->delete();
		return $result;
	}
	
	public function get_by_id($school_id,$fee_id){
		return feestructuree::where('school_id',$school_id)->where('id',$fee_id)
            ->where('session','=',$this->active_session->session)//updated 2-6-2018
            ->first();
	}
        
        public function getFeeByclass($school_id,$session,$class_id){
		return feestructuree::where('school_id',$school_id)
                        ->where('session',$session)->where('class_id',$class_id)
                        ->where('student_id','=','0')//updated 17-3-2018
                        ->get();
	}
}
