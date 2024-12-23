<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;

class UserReservationSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Tạo một thể hiện mới.
     *
     * @param $reservation
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Xây dựng thông điệp.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Xác Nhận Đặt Sân Thành Công')
                    ->view('emails.user_reservation_success')
                    ->with([
                        'reservation' => $this->reservation,
                    ]);
    }
}
