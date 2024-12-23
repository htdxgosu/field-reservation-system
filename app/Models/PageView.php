<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    use HasFactory;

    protected $table = 'page_views';  // Tên bảng trong cơ sở dữ liệu

    // Các trường có thể được gán đại diện cho bảng page_views
    protected $fillable = [
        'page_url',
        'viewed_at',
    ];

    // Tắt quản lý timestamps nếu không cần
    public $timestamps = false;
}
