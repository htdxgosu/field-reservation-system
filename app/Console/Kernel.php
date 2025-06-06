<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Đăng ký các lệnh Artisan trong ứng dụng.
     *
     * @return void
     */
    protected $commands = [
          \App\Console\Commands\SendConfirmationReminder::class,
    ];

    /**
     * Định nghĩa các tác vụ theo lịch trình.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
   protected function schedule(Schedule $schedule)
{
    $schedule->command('reservation:send-confirmation-reminder')
             ->everyMinute();

    // Thêm một công việc test để xem cron có chạy không
   $schedule->call(function () {
    file_put_contents(storage_path('logs/cron_check.log'), 'Cron chạy lúc: ' . now() . PHP_EOL, FILE_APPEND);
})->everyMinute();

}


    /**
     * Đăng ký các lệnh Artisan trong ứng dụng.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
