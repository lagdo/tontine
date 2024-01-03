<?php

namespace App\Listeners;

use App\Mail\FeedbackReceived;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Mydnic\Kustomer\Events\NewFeedback;

class FeedbackListener
{
    /**
     * Handle the event.
     *
     * @param  NewFeedback  $event
     * @return void
     */
    public function handle(NewFeedback $event)
    {
        if(!($mailTo = env('MAIL_FEEDBACK_TO')))
        {
            return;
        }

        $feedback = $event->feedback;
        $user = User::find($feedback->user_info['user_id'] ?? 0);
        Mail::to($mailTo)->send(new FeedbackReceived($feedback, $user));
    }
}
