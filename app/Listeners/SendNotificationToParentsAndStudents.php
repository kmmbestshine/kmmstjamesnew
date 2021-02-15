<?php

namespace App\Listeners;

use App\Events\SendNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Students;
use App\Employee;
use App\MobileUser;
use App\StuParent;
use Auth;
use DB;
use Mail;

class SendNotificationToParentsAndStudents
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
    public function handle(SendNotification $event)
    {   
        // get notification content
        $notification_content = \DB::table('notification_type')->where('id', $event->request['notification_type'])->first();

        // changes done by parthiban(26-09-2017) version 3
        if($event->request['notification_send_to'] == "teacher"){
            $teachers = Employee::whereIn('class', $event->request['classes'])->where('school_id', Auth::user()->school_id)->get();
            foreach ($event->request['classes'] as $class_id) {
                // send notification to teachers
                DB::table('teachers_notification')->insert([
                    'notification_type_id' => $event->request['notification_type'],
                    'teacher_id' => "123",
                    'class_id' => $class_id,
                    'title' => $notification_content->title,
                    'content' => $notification_content->description,
                    'seen' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }else{

            foreach ($event->request['classes'] as $class_id) {
                // send notification to students
                DB::table('students_notification')->insert([
                    'notification_type_id' => $event->request['notification_type'],
                    'student_id' => "123",
                    'class_id' => $class_id,
                    'title' => $notification_content->title,
                    'content' => $notification_content->description,
                    'seen' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // send notification to parents
                DB::table('parents_notification')->insert([
                    'notification_type_id' => $event->request['notification_type'],
                    'parent_id' => "123",
                    'class_id' => $class_id,
                    'title' => $notification_content->title,
                    'content' => $notification_content->description,
                    'seen' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);    
            }

            $students = Students::whereIn('class_id', $event->request['classes'])->where('school_id', Auth::user()->school_id)->get();

            foreach ($students as $student) {
                $mobileUserCount = MobileUser::where('user_type_id',$student->parent_id)->count();

                if($mobileUserCount == 0){
                    $parentObj = StuParent::find($student->parent_id);
                    $string=$notification_content->description;
                    //$string1 = strtoupper(bin2hex(iconv('UTF-8', 'UCS-2', $string)));
                    $string = strtoupper(bin2hex(iconv('UTF-8', 'UTF-16BE', $string)));
                   
                    // send sms to parents
                    // file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=0&dlr=1&destination=91'.$parentObj->mobile.'&source=SCHOOL&message='. urlencode($notification_content->description));
                      file_get_contents('http://103.16.101.52:8080/sendsms/bulksms?username=shtk-schools&password=Kmm123&type=2&dlr=1&destination=91'.$parentObj->mobile.'&source=SCHOOL&message='.$string);

                }
            }
        }
    }
}
