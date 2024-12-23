<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    // Đặt tên bảng nếu nó không theo chuẩn Laravel (số nhiều của tên model)
    protected $table = 'invoices';

    // Các trường có thể gán giá trị đại chúng
    protected $fillable = [
        'reservation_id',
        'user_id',
        'field_id',
        'total_amount',
        'invoice_code',
    ];

    // Mối quan hệ với model Reservation
    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    // Mối quan hệ với model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mối quan hệ với model Field
    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}

