<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class FieldOwnerApprovedMail extends Mailable
{
    use Queueable, SerializesModels, Dispatchable;

    protected $user;

    // Constructor để nhận user thông tin
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.field_owner_approved') // Tạo view riêng cho email
                    ->subject('Chấp nhận yêu cầu đăng ký làm chủ sân') // Tiêu đề email
                    ->with([
                        'name' => $this->user->name,
                    ]);
    }
}
