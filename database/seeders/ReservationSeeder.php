<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation; // Đảm bảo bạn đã import model Reservation
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Thêm dữ liệu mẫu vào bảng reservations sử dụng Eloquent Model
        Reservation::create([
            'user_id' => 1, // Giả sử user có ID là 1
            'field_id' => 1, // Giả sử sân có ID là 1
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addHour(),
            'status' => 'chờ xác nhận',
            'note' => 'Đặt sân cho buổi họp lớp',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Reservation::create([
            'user_id' => 2, // Giả sử user có ID là 2
            'field_id' => 2, // Giả sử sân có ID là 2
            'start_time' => Carbon::now()->addDays(1),
            'end_time' => Carbon::now()->addDays(1)->addHour(),
            'status' => 'đã xác nhận',
            'note' => 'Đặt sân cho trận bóng giao hữu',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        Reservation::create([
            'user_id' => 3, // Giả sử user có ID là 3
            'field_id' => 3, // Giả sử sân có ID là 3
            'start_time' => Carbon::now()->addDays(2),
            'end_time' => Carbon::now()->addDays(2)->addHour(),
            'status' => 'đã hủy',
            'note' => 'Sân bị hỏng, yêu cầu hủy',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
