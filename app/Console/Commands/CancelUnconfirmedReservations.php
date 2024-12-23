<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class CancelUnconfirmedReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:cancel-unconfirmed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hủy các đơn chưa xác nhận mà thời gian bắt đầu đã qua';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Lấy tất cả các đơn chưa xác nhận mà thời gian bắt đầu đã qua
        $reservations = Reservation::where('status', 'chờ xác nhận') // Trạng thái chưa xác nhận
            ->where('start_time', '<', now()) // Thời gian bắt đầu đã qua
            ->get();

        foreach ($reservations as $reservation) {
            // Cập nhật trạng thái của đơn thành 'Đã hủy'
            $reservation->update(['status' => 'đã hủy']);

            // Log thông báo cập nhật trạng thái
            Log::info('Reservation ' . $reservation->id . ' has been canceled due to missed start time.');
        }

        $this->info('Trạng thái đơn đã được cập nhật.');
        Log::info('Task running at: ' . now());
    }
}
