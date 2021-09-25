<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

// Custom events
use Illuminate\Support\Facades\Auth;

// Events
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Events\Login;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        
        Event::listen(Verified::class, function ($event) {
            $user = $event->user;
            $user->status = 1;
            $user->save();
        });

        Event::listen(Login::class, function ($event) {
            $user = $event->user;
            if($user->status == 'blocked') {
                Auth::logout();
            }
        });
    }
}
