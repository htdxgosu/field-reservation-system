<?php

namespace App\Jobs;

use App\Mail\RejectFieldOwnerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendRejectFieldOwnerRequestEmail implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        Mail::to($this->user->email)->send(new RejectFieldOwnerRequest($this->user));
    }
}
