<?php

namespace App\Listeners;

use App\Events\SendBirthdayNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Auth;
use DB;
use Mail;

class SendBirthdayNotificationToStudents
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
     * @param  SendBirthdayNotification  $event
     * @return void
     */
    public function handle(SendBirthdayNotification $event)
    {
        /** @ Updated for manual send  @ **/
        $content = $event->request['content'];
        if(empty($content))
        {
            $content = 'Happy Birthday';
        }
        /** end **/
        
        $students = \DB::table('student')->where('student.dob', 'LIKE', '%' . date('d-m') . '%')->where('student.school_id', Auth::user()->school_id)
            ->join('class', 'student.class_id', '=', 'class.id')
            ->select('student.id', 'student.name', 'class.class', 'student.roll_no', 'student.class_id')->get();


        $classArray = [];
        foreach ($students as $student) {
            if(!in_array($student->class_id,$classArray)){            
                array_push($classArray, $student->class_id);
            }
        }
  
        foreach ($classArray as $class_id) {
            // send notification to students
            DB::table('students_notification')->insert([
                'notification_type_id' => null,
                'student_id' => "123",
                'class_id' => $class_id,
                'title' => 'Birthday Wishes',
                'content' =>$content,
                'seen' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
