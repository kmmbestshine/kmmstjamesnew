<?php

namespace App\Listeners;

use App\Events\SendSmsNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Students;
use App\Employee;
use App\MobileUser;
use App\StuParent;
use Auth;
use DB;
use Mail;

class SendSms
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendNotification  $event
     * @return void
     */
    public function handle(SendSmsNotification $event)
    {   
        
        //dd($event);
        
        if($event->request['smstype']=='classwise'){
            
            $parentid=array();$sectionid=array();$classid=array();
            foreach ($event->request['classid'] as $key => $value) {
            foreach ($event->request['section'][$event->request['classname'][$key]] as $sectionkey => $sectionvalue) 
                {
                    $students=Students::where('school_id',\Auth::user()->school_id)
                                 ->where('class_id',$event->request['classid'][$key]->id)
                                 ->where('section_id',$sectionvalue)
                                 ->select('parent_id')
                                 ->get();

                                 array_push($parentid,$students);         
                    $sectionid[]=$sectionvalue;
                  
                       
                }
                  $classid[]=$event->request['classid'][$key]->id;
             
                    
            }
            
                       $data=array();
                       $data['school_id']=\Auth::user()->school_id;
                       $data['smstype']='classwise';
                       $data['section_ids']=implode(",", $sectionid);
                       $data['class_ids']=implode(",",$classid);
                       $data['description']=$event->request['description'];
                       DB::table('smssend_detail')->insert($data);  
                        $parentids=array();
                        foreach ($parentid as $parentkey => $parentvalue) {
                             foreach ($parentvalue as $parentkey => $value) {
                                 $parentids[]=$value;
                             }  
                        }
                        
                        foreach ($parentids as $key => $value) {             
                          $mobileUserCount = MobileUser::where('user_type_id',$parentids[$key]->parent_id)->count();
                          
                if($mobileUserCount == 0){                           
                                    $parentObj = StuParent::find($parentids[$key]->parent_id);
                                                           $string = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $event->request['description'])));
                   
                    
                     file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username='.$event->request['smsusername'].'&password='.$event->request['smsuserpassword'].'&type=2&dlr=1&destination=91'.$parentObj->mobile.'&source='.$event->request['smssource'].'&message='.$string);
                      
                              
                                }
                              }
                         $msg['success'] = 'Message Send Successfully';
                                    return \Redirect::back()->withInput($msg);      
                                  

                        
                 }else{

                     $students=Students::where('school_id',\Auth::user()->school_id)
                               ->select('parent_id')

                                 ->get();
                                  $data=array();
                       $data['school_id']=\Auth::user()->school_id;
                       $data['smstype']='allClass';
                       $data['description']=$event->request['description'];
                       DB::table('smssend_detail')->insert($data);  
                        foreach ($students as $key => $value) {
                            $mobileUserCount = MobileUser::where('user_type_id',$students[$key]->parent_id)->count();
                          
                      if($mobileUserCount == 0){      
                                $parentObj = StuParent::find($students[$key]->parent_id);
                                
                    //$string1 = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2', $string)));
                     $string = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $event->request['description'])));
                   
                    
                   file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username='.$event->request['smsusername'].'&password='.$event->request['smsuserpassword'].'&type=2&dlr=1&destination=91'.$parentObj->mobile.'&source='.$event->request['smssource'].'&message='.$string);
                            
                        }
                      }
                        $msg['success'] = 'Message Send Successfully';
                        return \Redirect::back()->withInput($msg);      
                 }   
        
    }
}
