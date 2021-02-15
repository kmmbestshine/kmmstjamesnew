<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\SendBirthdayNotification' => [
            'App\Listeners\SendBirthdayNotificationToStudents',
        ],
        'App\Events\SendNotification' => [
            'App\Listeners\SendNotificationToParentsAndStudents',
        ],
        'App\Events\StudentCreationAlertMail' => [
            'App\Listeners\SendMailToParentsMail',
        ],
        'App\Events\EmployeeCreationAlertMail' => [
            'App\Listeners\SendMailToEmployeeMail',
        ],       
         'App\Events\SendSmsNotification' => [
            'App\Listeners\SendSms',
        ],   
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
