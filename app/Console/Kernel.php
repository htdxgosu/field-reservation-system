<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;  // Đảm bảo bạn đã import đúng class Schedule
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;  // Đảm bảo bạn sử dụng đúng class này
use App\Console\Commands\UpdateFieldAvailability; // Import command của bạn
use App\Console\Commands\CancelUnconfirmedReservations;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateFieldAvailability::class,  // Đăng ký Command của bạn ở đây
        CancelUnconfirmedReservations::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Bạn có thể lên lịch cho Command tự động chạy ở đây, ví dụ:
        $schedule->command('fields:update-availability')->everyMinute(); // Cập nhật mỗi giờ
         // Lên lịch chạy command mỗi giờ
       $schedule->command('reservations:cancel-unconfirmed')->everyMinute();
       // Thêm dòng log để kiểm tra
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
