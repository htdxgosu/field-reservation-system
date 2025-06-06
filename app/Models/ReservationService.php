<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationService extends Model
{
    protected $fillable = ['reservation_id', 'service_id','service_name','unit_price','quantity', 'total_price'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
