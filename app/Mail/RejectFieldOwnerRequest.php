<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RejectFieldOwnerRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Thông báo: Yêu cầu đăng ký làm chủ sân đã bị từ chối')
                    ->view('emails.reject_field_owner_request')
                    ->with([
                        'name' => $this->user->name,
                        'reason' => 'Lý do từ chối sẽ được thông báo sau.',
                    ]);
    }
}
