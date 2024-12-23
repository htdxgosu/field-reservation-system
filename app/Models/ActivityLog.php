<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id', 
        'user_id', 
        'field_id', 
        'action',
    ];

    // Nếu cần thiết, bạn có thể định nghĩa các quan hệ với các model khác như Reservation, User, và Field.
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}

