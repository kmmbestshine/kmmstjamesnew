<?php

namespace App\Listeners;

use App\Events\StudentCreationAlertMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class SendMailToParentsMail
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
     * @param  StudentCreationAlertMail  $event
     * @return void
     */
    public function handle(StudentCreationAlertMail $event)
    {
        $data = $event->info;
		//dd($data);
        Mail::send('emails.parent', $data, function($message) use ($data) {
            $message->to($data['EMAIL']);
            $message->subject('Student Username & Password Alert Mail');
        });
		
		//dd(Mail::failures());
    }
}
