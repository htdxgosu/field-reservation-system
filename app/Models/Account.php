<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Account extends Authenticatable
{
    protected $fillable = ['username', 'password', 'role'];

    // Ẩn trường password khi trả về JSON
    protected $hidden = ['password'];

    // Đảm bảo password được hash trước khi lưu
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}

