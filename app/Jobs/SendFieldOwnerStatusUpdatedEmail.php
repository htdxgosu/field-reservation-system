<?php

namespace App\Jobs;

use App\Mail\FieldOwnerStatusUpdatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendFieldOwnerStatusUpdatedEmail implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $user;
    protected $statusMessage;

    public function __construct($user, $statusMessage)
    {
        $this->user = $user;
        $this->statusMessage = $statusMessage;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new FieldOwnerStatusUpdatedMail($this->user, $this->statusMessage));
    }
}
