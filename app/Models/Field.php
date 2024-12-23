<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Field extends Model
{
    use HasFactory;

    // Các trường có thể mass-assign (được điền vào trong hàm create hoặc update)
    protected $fillable = [
        'name', 'location', 'price_per_hour', 'peak_price_per_hour',
        'field_type_id', 'description', 'image_url', 'second_image_url', 'availability',
        'user_id', 
        'opening_time', 
        'closing_time','rental_count',
        'latitude', 
        'longitude',
    ];

    // Mối quan hệ với bảng field_types (mỗi sân thuộc một loại sân)
    public function fieldType()
    {
        return $this->belongsTo(FieldType::class, 'field_type_id');
    }
    // Mối quan hệ với bảng users (mỗi sân thuộc sở hữu của một người dùng)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Mối quan hệ với bảng reservations (một sân có thể có nhiều lần đặt)
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function reviews() {
        return $this->hasMany(Review::class);
    }
    

    /**
     * Hàm định dạng giá tiền theo kiểu của Việt Nam.
     * Ví dụ: 1000000 -> 1.000.000đ
     */
    public function getFormattedPricePerHourAttribute()
    {
        return number_format($this->price_per_hour, 0, ',', '.') . 'đ';
    }

    public function getFormattedPeakPricePerHourAttribute()
    {
        return number_format($this->peak_price_per_hour, 0, ',', '.') . 'đ';
    }
    /**
     * Lấy danh sách giờ trống hôm nay
     */
    public function getAvailableHoursForDate($date)
    {
        // Lấy danh sách các đơn đặt sân cho ngày đã chọn
        $reservations = Reservation::where('field_id', $this->id) // Lọc theo sân hiện tại
            ->whereDate('start_time', $date)  // Lọc theo ngày
            ->orderBy('start_time', 'asc')  // Sắp xếp theo giờ bắt đầu
            ->get();
        
        // Xác định giờ mở và đóng cửa
        $startOfDay = Carbon::parse($date . ' ' . $this->opening_time);
        $endOfDay = Carbon::parse($date . ' ' . $this->closing_time);
        
        $availableHours = [];
        $lastEndTime = $startOfDay;
    
        foreach ($reservations as $reservation) {
            // Tính giờ bắt đầu và kết thúc của đơn đặt
            $start = Carbon::parse($reservation->start_time);
            $duration = $reservation->duration->duration;  // Duration tính bằng phút
            $end = $start->copy()->addMinutes($duration);  // Tính giờ kết thúc
    
            if ($start->gt($lastEndTime)) {
                // Thêm khoảng trống vào mảng
                $availableHours[] = [
                    'start' => $lastEndTime->format('H:i'),
                    'end' => $start->format('H:i')
                ];
            }
    
            // Cập nhật giờ kết thúc cuối cùng
            $lastEndTime = $end;
        }
    
        // Kiểm tra khoảng trống từ giờ cuối cùng đến khi đóng cửa
        if ($lastEndTime->lt($endOfDay)) {
            $availableHours[] = [
                'start' => $lastEndTime->format('H:i'),
                'end' => $endOfDay->format('H:i')
            ];
        }
    
        return $availableHours;
    }
    

    public function getAvailableStartTimes()
{
    // Lấy giờ mở cửa và giờ đóng cửa của sân
    $opening_time = Carbon::parse($this->opening_time); // Giờ mở cửa của sân
    $closing_time = Carbon::parse($this->closing_time); // Giờ đóng cửa của sân

    // Kiểm tra nếu giờ mở cửa sau giờ đóng cửa thì trả về mảng trống
    if ($opening_time->greaterThan($closing_time)) {
        return [];
    }

    // Tạo mảng các giờ bắt đầu từ giờ mở cửa đến giờ đóng cửa
    $available_times = [];
    while ($opening_time->lessThan($closing_time)) {
        // Thêm giờ vào mảng
        $available_times[] = $opening_time->format('H:i');
        
        // Tăng lên 30 phút cho mỗi giờ bắt đầu
        $opening_time->addMinutes(30);
    }

    return $available_times;
}
   public static function getMostRentedFields()
    {
        return self::orderBy('rental_count', 'desc') 
                    ->limit(3) 
                    ->get(); 
    }
    public static function topFieldsThisMonth()
    {
        return self::orderBy('rental_count', 'desc') // Sắp xếp theo số lượng thuê
            ->whereMonth('updated_at', Carbon::now()->month) // Lọc theo tháng hiện tại
            ->whereYear('updated_at', Carbon::now()->year) // Lọc theo năm hiện tại
            ->limit(3) // Lấy 3 sân đầu tiên
            ->get();
    }
        public function calculateDistance($latitude, $longitude)
    {
        $earthRadius = 6371;  // Đơn vị: km

        // Chuyển đổi tọa độ từ độ sang radian
        $latFrom = deg2rad($latitude);
        $lonFrom = deg2rad($longitude);
        $latTo = deg2rad($this->latitude);  // Tọa độ sân
        $lonTo = deg2rad($this->longitude); // Tọa độ sân

        // Tính sự khác biệt về kinh độ và vĩ độ
        $lonDiff = $lonTo - $lonFrom;
        $latDiff = $latTo - $latFrom;

        // Áp dụng công thức Haversine
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos($latFrom) * cos($latTo) *
            sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * asin(sqrt($a));

        $distance = $earthRadius * $c;
        return round($distance, 1); // Làm tròn khoảng cách
    }
    public function averageRating()
    {
        $average = $this->reviews()->avg('rating');
         return $average !== null ? round($average, 1) : 0;
    }
}

