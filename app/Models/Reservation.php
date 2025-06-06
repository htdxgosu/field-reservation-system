<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    // Các trường có thể mass-assign (được điền vào trong hàm create hoặc update)
    protected $fillable = [
        'user_id', 'field_id', 'start_time', 'duration_id', 'status', 'note','total_amount','email_sent', 'original_amount', 
    ];

    // Mối quan hệ với bảng users (mỗi đặt sân thuộc một người dùng)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Mối quan hệ với bảng fields (mỗi đặt sân thuộc một sân cụ thể)
    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id');
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
      public function services()
    {
        return $this->belongsToMany(Service::class, 'reservation_services')
                     ->withPivot('quantity', 'total_price', 'service_name', 'unit_price')
                    ->withTimestamps();
    }
    // Quan hệ với bảng durations
    public function duration()
    {
        return $this->belongsTo(Duration::class);
    }
        public function calculateEndTime()
    {
        $startTime = \Carbon\Carbon::parse($this->start_time);
        $duration = \App\Models\Duration::find($this->duration_id); 
        $durationInMinutes = (int) $duration->duration;
        $endTime = $startTime->addMinutes($durationInMinutes);

        return $endTime->format('d/m/Y H:i'); 
    }

}
