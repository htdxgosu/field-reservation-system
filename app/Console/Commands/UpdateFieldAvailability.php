<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Field;
use App\Models\Reservation;
use Illuminate\Support\Facades\Log;

class UpdateFieldAvailability extends Command
{
    // Đặt tên cho command
    protected $signature = 'fields:update-availability';

    // Mô tả command
    protected $description = 'Cập nhật trạng thái sân khi thời gian kết thúc đã qua';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
{
    // Log tất cả các sân trong cơ sở dữ liệu
    $allFields = Field::all();
    Log::info('All fields in the database: ', $allFields->toArray());

    // Lấy tất cả các sân có trạng thái 'Đang sử dụng'
    $fields = Field::where('availability', 'like', 'Đang sử dụng')->get();

    // Log số lượng sân trả về
    Log::info('Number of fields currently in use: ' . $fields->count());

    // Log danh sách các sân đang sử dụng
    Log::info('Fields currently in use: ', $fields->toArray());

    foreach ($fields as $field) {
        // Lấy thông tin đặt sân gần nhất của sân này
        $reservation = Reservation::where('field_id', $field->id)
            ->where('status', 'đã xác nhận')
            ->latest('end_time')
            ->first();

        // Log thông tin về đơn đặt sân (reservation) nếu có
        if ($reservation) {
            Log::info('Reservation found for field ' . $field->id . ': ', $reservation->toArray());

            // Nếu đơn đặt sân đã kết thúc và thời gian đã qua, cập nhật trạng thái sân
            if (Carbon::parse($reservation->end_time)->isPast()) {
                Log::info('Updating availability for field ' . $field->id . ' to "Đang trống"');
                $field->update(['availability' => 'Đang trống']);
            }
        } else {
            // Nếu không có đơn đặt, cập nhật trạng thái sân về "Đang trống"
            Log::info('No reservation found for field ' . $field->id . ', updating availability to "Đang trống"');
            $field->update(['availability' => 'Đang trống']);
        }
    }

    // Log thời gian task chạy
    Log::info('Task running at: ' . now());

    $this->info('Trạng thái sân đã được cập nhật.');
}

}
