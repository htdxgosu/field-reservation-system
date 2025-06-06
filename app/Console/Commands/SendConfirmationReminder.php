<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationConfirmationReminder; // Tạo mailable

class SendConfirmationReminder extends Command
{
    protected $signature = 'reservation:send-confirmation-reminder';
    protected $description = 'Gửi email nhắc nhở yêu cầu xác nhận cho các đơn đặt còn 30 phút mà chưa xác nhận';

        public function handle()
        {
           $this->info('Đang kiểm tra các đơn đặt...');
    
   // Kiểm tra dữ liệu và thêm log để chắc chắn
    $reservations = Reservation::where('status', 'chờ xác nhận')
        ->where('start_time', '<=', now()->addMinutes(30)) // Còn 30 phút nữa
        ->where('start_time', '>', now()) // Đơn đặt chưa đến giờ bắt đầu
        ->where('email_sent', 0) // Kiểm tra chỉ gửi email nếu chưa gửi
        ->get();
    
    if ($reservations->isEmpty()) {
        $this->info('Không có đơn nào cần gửi email nhắc nhở');
    }
    
    foreach ($reservations as $reservation) {
        // Gửi email nhắc nhở cho người dùng
        Mail::to($reservation->user->email)
            ->send(new ReservationConfirmationReminder($reservation));
     // Cập nhật trạng thái email_sent sau khi gửi email
        $reservation->update(['email_sent' => 1]);
        $this->info('Email gửi cho đơn đặt ID: ' . $reservation->id);
    }
    // Hủy các đơn đến giờ bắt đầu mà chưa xác nhận
        $cancelReservations = Reservation::where('status', 'chờ xác nhận')
            ->where('start_time', '<=', now())
            ->get();

        if ($cancelReservations->isEmpty()) {
            $this->info('[Hủy] Không có đơn nào cần hủy.');
        }

        foreach ($cancelReservations as $reservation) {
            $reservation->update(['status' => 'đã hủy']);
            ActivityLog::create([
                'reservation_id' => $reservation->id,
                'user_id' => $reservation->user_id,
                'field_id' => $reservation->field_id,
                'action' => 'hủy tự động do không xác nhận',
            ]);
            $this->info('[Hủy] Đã hủy đơn ID: ' . $reservation->id);
        }
    
        }
}
