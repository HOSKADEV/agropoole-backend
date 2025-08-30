<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     */
    public function build()
    {

      $roleKey = $this->user->role_is();

        return $this
            ->subject('Welcome to Agropool')
            ->view('emails.welcome')
            ->with([
                'text_ar' => trans("mail.{$roleKey}", [], 'ar'),
                'text_fr' => trans("mail.{$roleKey}", [], 'fr'),
            ]);
    }
}
