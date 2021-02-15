<?php

namespace App;
use Redirect, api, DB, Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
class Payment extends Model
{
    protected $table = 'payment';

    public $timestamps = true; 
	private $student;

    private $active_session;//updated 2-6-2018

    public function __construct()
    {
        /*updated 2-6-2018*/
        $this->active_session = Session::where('school_id', \Auth::user()->school_id)
            ->where('active','1')->first();
        $this->student = new Students;
    }

    /*end*/
	
	public function get_payment_student_by_id($school_id,$session_id,$student_id){
		$result = payment::where('school_id',$school_id)->where('session_id',$session_id)
            ->where('student_id',$student_id)
            ->where('session_id','=',$this->active_session->id)//updated 2-6-2018
            ->get();
		if(!$result){
			$result = 'No Result Found';
		}
		return $result;
		
	}
	public function get_payment_by_id($payment_id){
		$result = payment::where('id',$payment_id)
            ->where('session_id','=',$this->active_session->id)//updated 2-6-2018
            ->first();
		return $result;
	}

	// changes done by parthiban 28-09-2017 
	public function get_payment_by_id_parthiban($fee_id,$stu_id){
		$result = payment::where('fee_id',$fee_id)->where('student_id',$stu_id)
            ->where('session_id','=',$this->active_session->id)//updated 2-6-2018
            ->get();
		return $result;
	}
	
	public function get_balance_amount($fee_id){
		$result = payment::select('balance_amount','id','fee_detail')
            ->where('session_id','=',$this->active_session->id)//updated 2-6-2018
            ->where('fee_id',$fee_id)->first();
		return $result;
	}
    
    // changes done by parthiban 28-09-2017 
    public function get_balance_amountby_stu($fee_id,$stu_id){
		$result = payment::select('balance_amount','id','fee_detail','last_paid_date')->where('fee_id',$fee_id)
                        ->where('student_id',$stu_id)->orderBy('id', 'desc')->first();
		return $result;
	}
	
	public function insert_payment($data,$fee_type,$pay_month,$cheque_detail,$transaction_detail){
		foreach($data as $key=>$value){
			if(!isset($data[$key])&& empty($data[$key])){
				return $key . ' is empty';
			}
		}
		if($data['payment_type'] == 'cheque'){
			$data['payment_detail']=json_encode($cheque_detail);			
		}
		
		if($data['payment_type'] == 'online'){
			$data['payment_detail']=json_encode($transaction_detail);			
		} 
		if($data['balance_amount'] == 0){
			$data['paid'] = true;
		}
		switch($fee_type){
			case 'ANNUAL':
				$result = payment::insertGetId($data);
				if(is_numeric($result)){
					$payment = payment::where('id',$result)->first();
					return $this->get_detail_payment($payment);
				}
				break;
			case 'MONTHLY':
				$data['fee_detail']= json_encode(array_unique($pay_month));
				$result = payment::insertGetId($data);
				if(is_numeric($result)){
					$payment = payment::where('id',$result)->first();
					return $this->get_detail_payment($payment);
				}
				break;
			default:
				$result = payment::insertGetId($data);
				if(is_numeric($result)){
					
					$payment = payment::where('id',$result)->first();
					return $this->get_detail_payment($payment);
				}
		}
		return $result;
		
	}
	

	public function update_payment($where_data,$data,$fee_type,$pay_month,$cheque_detail,$transaction_detail){
		foreach($data as $key=>$value){
			if(!isset($data[$key])&& empty($data[$key])){
				return $key . ' is empty';
			}
		}
		$payment = payment::where('id',$where_data['payment_id'])->first();
		if($data['payment_type'] == 'cheque'){
			$current_payment_details = json_decode($payment->payment_detail,true);
			$cheque_detail[]=$current_payment_details;
			$data['payment_detail']=json_encode($cheque_detail);
		}
		
		if($data['payment_type'] == 'online'){
			$current_payment_details = json_decode($payment->payment_detail,true);
			$transaction_detail[]=$current_payment_details;
			$data['payment_detail']=json_encode($transaction_detail);			
		} 
		if($data['balance_amount'] == 0){
			$data['paid'] = true;
		}
		switch($fee_type){
			case 'ANNUAL':
				$result = payment::where('id',$where_data['payment_id'])
                                ->where('student_id',$where_data['student_id'])->update($data);
				if(is_numeric($result)){
					$payment = payment::where('id',$where_data['payment_id'])->first();
					return $this->get_detail_payment($payment);
				}
			break;
			case 'MONTHLY':
				$data['fee_detail']= json_encode(array_unique($pay_month));
				$result = payment::where('id',$where_data['payment_id'])
                                        ->where('student_id',$where_data['student_id'])->update($data);
				if(is_numeric($result)){
					$payment = payment::where('id',$where_data['payment_id'])->first();
					return $this->get_detail_payment($payment);
				}
			break;
			default:
				$result = payment::where('id',$where_data['payment_id'])
                                ->where('student_id',$where_data['student_id'])->update($data);
				if(is_numeric($result)){
					$payment = payment::where('id',$where_data['payment_id'])->first();
					return $this->get_detail_payment($payment);
				}
		}
		return $result;
	}
	
	public function get_detail_payment($payment){
		$result = array();
		$paid_student = DB::table('student')->where('id',$payment->student_id)
            ->where('session_id','=',$this->active_session->id)//updated 2-6-2018
            ->first();
		$result['fee'] =  DB::table('fee_structure')->where('id',$payment->fee_id)
            ->where('session','=',$this->active_session->session)//updated 2-6-2018
            ->first();
		$result['student'] = $paid_student;
		$result['school'] = DB::table('school')->where('id',$payment->school_id)->first();
		$result['payment'] = $payment;
		$result['class'] = $this->student->getStudentClass($paid_student->class_id,$paid_student->section_id);
		return $result;
	}
	
}





































