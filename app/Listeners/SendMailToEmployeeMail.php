<?php

namespace App\Listeners;

use App\Events\EmployeeCreationAlertMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendMailToEmployeeMail
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
     * @param  EmployeeCreationAlertMail  $event
     * @return void
     */
    public function handle(EmployeeCreationAlertMail $event)
    {
        $data = $event->info;
      
        Mail::send('emails.employee', $data, function($message) use ($data) {
            $message->to($data['EMAIL']);
            $message->subject('Employee Username & Password Alert Mail');
        });
    }
}
