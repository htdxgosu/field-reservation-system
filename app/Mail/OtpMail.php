<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;

    /**
     * Tạo một phiên bản mới của thông báo OTP.
     *
     * @param  int  $otpCode
     * @return void
     */
    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    /**
     * Xây dựng nội dung thông báo.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->view('emails.otp')  // Tạo view cho email
                    ->subject('Mã OTP xác thực')  // Tiêu đề email
                    ->with(['otpCode' => $this->otpCode]);  // Truyền mã OTP vào view
    }
}
