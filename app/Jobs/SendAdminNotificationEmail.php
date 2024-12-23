<?php

namespace App\Jobs;

use App\Mail\AdminNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendAdminNotificationEmail implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function handle()
    {
        Mail::to('htdxgosu22@gmail.com')->send(new AdminNotificationMail($this->name));
    }
}
