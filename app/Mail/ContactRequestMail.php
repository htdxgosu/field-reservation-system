<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $phone;
    public $subject;
    public $msg;

    /**
     * Tạo một instance mới của lớp mail.
     *
     * @param  string  $name
     * @param  string  $email
     * @param  string  $phone
     * @param  string  $subject
     * @param  string  $msg
     */
    public function __construct($name, $email, $phone, $subject, $msg)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->subject = $subject;
        $this->msg = $msg;
    }

    /**
     * Xây dựng nội dung email.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->subject('Liên hệ từ khách hàng')
                    ->view('emails.contact') // Sử dụng view để tạo nội dung email
                    ->with([
                        'name' => $this->name,
                        'email' => $this->email,
                        'phone' => $this->phone,
                        'subject' => $this->subject,
                        'msg' => $this->msg,
                    ]);
    }
}

