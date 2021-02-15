<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use PDF;

class FrontController extends Controller
{

    public function home()
    {
    	return view('index');
    }

    public function about()
    {
    	return view('about');
    }

    public function contact()
    {
    	return view('contact');
    }

    public function gallery()
    {
        return view('galleries');
    }
    public function facility()
    {
        return view('facilities');
    }



    public function bcpRegistration()
    {
    	return view('bcp');
    }

    public function postRegistration()
    {
    	$input = \Request::all();

    	$input['bdo'] = (isset($input['bdo'])?$input['bdo']:'');
    	$input['dcp'] = (isset($input['dcp'])?$input['dcp']:'');
    	$input['ddo'] = (isset($input['ddo'])?$input['ddo']:'');
    	$input['tcp'] = (isset($input['tcp'])?$input['tcp']:'');
    	$input['tdo'] = (isset($input['tdo'])?$input['tdo']:'');
    	$check = \DB::connection('business')->table('users')->where('mobile', $input['mobile'])->first();
    	if(!$check)
    	{
    	$user = \DB::connection('business')->table('users')->insertGetId([
    			'name'=>$input['name'],
    			'email'=>$input['email'],
    			'password'=>\Hash::make($input['mobile']),
    			'hint_password'=>$input['mobile'],
    			'mobile'=>$input['mobile']
    	]);
    	if($user<=9999)
    	{
    		$no = '000'.$user;
    	}
    	else
    	{
    		$no = $user;
    	}
    	\DB::connection('business')->table('users')->where('id', $user)->update(['username'=>'BCP'.$no]);
    	\DB::connection('business')->table('bcp')->insert([
    			'business_name'=>$input['firm'],
    			'user_id'=>$user,
    			'address'=>$input['address'],
    			'city'=>$input['city'],
    			'tehsil'=>$input['tehsil'],
    			'dist'=>$input['dist'],
    			'state'=>$input['state'],
    			'pin_code'=>$input['pin_code'],
    			'bdo'=>$input['bdo'],
    			'dcp'=>$input['dcp'],
    			'ddo'=>$input['ddo'],
    			'tcp'=>$input['tcp'],
    			'tdo'=>$input['tdo']
    		]);
    			$msg['success'] = 'Success to Registered Account';
    		return \Redirect::back()->withInput($msg);
    	}
    	else
    	{
    		$msg['error'] = 'Already registered';
    		return \Redirect::back()->withInput($msg);
    	}
    }

    public function dateConvert()
    {
        $get = \DB::table('student')->get();
        function validateDate($date)
        {
            $d = \DateTime::createFromFormat('d-m-Y', $date);
            return $d && $d->format('d-m-Y') == $date;
        }

        foreach($get as $value)
        {
            if($value->date_of_joining)
            {
                if(!validateDate($value->date_of_joining))
                {
                    \DB::table('student')
                        ->where('id', $value->id)
                        ->update([
                            'date_of_joining' =>date('d-m-Y', \PHPExcel_Shared_Date::ExcelToPHP($value->date_of_joining))
                        ]);
                    
                }   
            }
        }
    }

    /***********************************************************************
                                ENQUIRY MODULE
    ************************************************************************/
    public function getUserDetail()//updated 25-10-2017 by priya
    {
        //return ' add';

        $input = \Request::all();
        $userError = ['name' => 'User Name', 'email_id' => 'User Email Id', 'mobile' => 'User Mobile', 'message' => 'Message'];
        $validator = \Validator::make($input, [
            'name' => 'required',
            'email_id' => 'required|email|unique:enquiry_details,email_id',
            //'email_id' => 'required',
            'mobile' => 'required|numeric|digits:10|unique:enquiry_details,mobile',
            'message' =>'required'
        ], $userError);

        $validator->setAttributeNames($userError);
        //dd($validator);
        if ($validator->fails())
        {
            // return 'error';
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            //return 'success';
            $sendEmail =\DB::table('enquiry_details')->insert([
                'user_name' => $input['name'],
                'email_id' => $input['email_id'],
                'mobile' => $input['mobile'],
                'message' => $input['message']
            ]);
            if($sendEmail)
            {
                // return 'Mail success';
                $current_date = date('Y-m-d');
                $getMailDetails = \DB::table('enquiry_details')->whereDate('created_at','=',$current_date)->count();

                //send email to customer care
                $mailSend=\Mail::send('emails.enquiry',['name' => $input['name'],'user_mail' => $input['email_id'],'user_mobile' => $input['mobile'],'user_message' => $input['message'],'totalEnquiry' =>$getMailDetails],function ($message)
                {
                    $message->from('shineschoolappusername@gmail.com', 'Shine School');
                    $message->to('enquiryshineschool251017@gmail.com')->subject('User Enquiry Details');
                    //$message->to('priyavaigaran@gmail.com')->subject('User Enquiry Details');

                });
                if($mailSend)
                {
                    // return 'Mail send';
                    $input['success'] = 'Thanks to contact Shine School App !!! Customer Care Team Will Contact You Within 24 Hours in Working Days !!!';
                }
                else
                {
                    // return 'Mail not send';
                    $input['error'] = 'Your Message has been not send Successfully !!!';
                }
            }
            else
            {
                $input['error'] = 'Your Detail is not saved !!!';

            }
            return \Redirect::back()->withInput($input);
        }
    }

    /***********************************************************************
                                SUBCRIBE NEWSLETTER MODULE
    ************************************************************************/

    public function getSubcribeNewsletter()
    {
        //return ' Subscribe';
        $input = \Request::all();
        $userError = ['email' => 'User Email Id'];
        $validator = \Validator::make($input, [
            'email' => 'required|email|unique:subcribe_newsletters,email_id'
           // 'email' => 'required'
        ], $userError);
        $validator->setAttributeNames($userError);
        if ($validator->fails())
        {
            //return 'error';
            return \Redirect::back()->withErrors($validator)->withInput($input);
        }
        else
        {
            // return 'success';
            $subscribe =\DB::table('subcribe_newsletters')->insert([
                'email_id' => $input['email']
            ]);
            if($subscribe)
            {
                $subscriber = $input['email'];
                $current_date = date('Y-m-d');
                $getSubscriberDetails = \DB::table('subcribe_newsletters')->whereDate('created_at','=',$current_date)->count();

                //send mail to subscriber
                $mailSend = \Mail::send('emails.subscriber_pdf',[], function($message) use($subscriber)
                {
                    $message->from('shineschoolappusername@gmail.com', 'ShineSchool Team');
                    $message->to($subscriber)->subject('ShineSchool PDF');
                });

                //send mail to customer care
                \Mail::send('emails.subscriber_details',['subscriber_email' => $input['email'],'total_subscriber' => $getSubscriberDetails], function($newsletter)
                {
                    $newsletter->from('shineschoolappusername@gmail.com', 'ShineSchool Team');
                    $newsletter->to('enquiryshineschool251017@gmail.com')->subject('Subscriber Details');
                });

                if($mailSend)
                {
                   // return 'Mail send';
                    $input['msg'] = 'Thanks For Subscribing Us...Check Your Inbox !!!';
                }
                else
                {
                    //return 'Mail not send';
                    $input['nomsg'] = 'Your Mail ID is Not valid !!!';
                }
                //$input['msg'] = 'Your Message has been send Successfully !!!';
            }
            else
            {
                $input['nomsg'] = 'Your Detail is not saved !!!';

            }
            return \Redirect::back()->withInput($input);
        }
    }


}