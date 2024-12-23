<?php

namespace App\Jobs;

use App\Mail\FieldOwnerApprovedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendFieldOwnerApprovedEmail implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new FieldOwnerApprovedMail($this->user));
    }
}
