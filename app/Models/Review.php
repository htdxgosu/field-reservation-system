<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    // Các trường có thể mass-assign
    protected $fillable = [
        'field_id', 'user_id', 'reservation_id', 'rating', 'comment','reply'
    ];

    // Mối quan hệ với bảng User (người dùng viết đánh giá)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mối quan hệ với bảng Field (sân được đánh giá)
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    // Mối quan hệ với bảng Reservation (đặt sân của người dùng)
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
