<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['user_id', 'name', 'price','is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservationServices()
    {
        return $this->hasMany(ReservationService::class);
    }
}
