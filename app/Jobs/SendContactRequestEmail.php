<?php

namespace App\Jobs;

use App\Mail\ContactRequestMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendContactRequestEmail implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $name;
    protected $email;
    protected $phone;
    protected $subject;
    protected $message;

    public function __construct($name, $email, $phone, $subject, $message)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function handle()
    {
        Mail::to('htdxgosu22@gmail.com')->send(new ContactRequestMail(
            $this->name,
            $this->email,
            $this->phone,
            $this->subject,
            $this->message
        ));
    }
}
