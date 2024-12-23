<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmedMail;
use App\Mail\UserReservationSuccessMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;

class SendReservationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;

    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    public function handle()
    {
        // Gửi email cho chủ sở hữu sân
        Mail::to($this->reservation->field->owner->email)
            ->send(new ReservationConfirmedMail($this->reservation));

        // Gửi email cho người dùng
        Mail::to($this->reservation->user->email)
            ->send(new UserReservationSuccessMail($this->reservation));
    }
}