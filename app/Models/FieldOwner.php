<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldOwner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'address', 'identity', 'business_license', 'status','bank_id','bank_account',
    ];

    // Quan hệ với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
