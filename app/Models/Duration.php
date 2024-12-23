<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use HasFactory;

    // Nếu bảng không tuân theo quy ước đặt tên (table tên là durations)
    protected $table = 'durations';

    // Nếu bạn muốn khai báo các cột có thể mass assignable
    protected $fillable = [
        'duration',  
       
    ];

    // Các trường không được mass-assignable
    protected $guarded = [];
}
