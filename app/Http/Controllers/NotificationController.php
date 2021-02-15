<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Redirect;
use Event;
use App\Events\SendBirthdayNotification;

class NotificationController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendBirthdayNotification()
    {   
        /* Event::fire(new SendBirthdayNotification());*/
        /** updated to send manual description **/
        $input = \Request::all();
        Event::fire(new SendBirthdayNotification($input));
        
        return redirect()->back()->with('birthdayNotificationSendSuccess', 'Birthday wishes has been send successfully !!!'); 
    }
}
