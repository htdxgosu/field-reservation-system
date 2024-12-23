<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class ReservationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Tạo một thể hiện email.
     *
     * @param  \App\Models\Reservation  $reservation
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Xây dựng thông điệp email.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->subject('Khách Hàng Đã Xác Nhận Đặt Sân Của Bạn')
                    ->view('emails.reservation_confirmed');
    }
}
