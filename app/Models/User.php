<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends  Authenticatable
{
    use HasFactory,Notifiable;

    // Các trường có thể mass-assign (được điền vào trong hàm create hoặc update)
    protected $fillable = [
        'name', 'phone', 'email', 'password', 'role'
    ];
    protected $hidden = ['password'];

    // Đảm bảo password được hash trước khi lưu
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::needsRehash($value) ? bcrypt($value) : $value;
    }

    // Mối quan hệ với bảng reservations (một người dùng có thể có nhiều đơn đặt sân)
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Mối quan hệ với bảng fields (nếu người dùng là chủ sân)
    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    // Mối quan hệ với bảng reviews (một người dùng có thể đánh giá nhiều sân)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    public function services()
    {
        return $this->hasMany(Service::class);
    }
    // Hàm để kiểm tra người dùng là chủ sân hay khách hàng
    public function isFieldOwner()
    {
        return $this->role === 'field_owner';
    }

    // Hàm để kiểm tra người dùng là khách hàng
    public function isCustomer()
    {
        return $this->role === 'customer';
    }
    public function fieldOwner()
    {
        return $this->hasOne(FieldOwner::class);
    }
    public function getTotalReservationsAttribute()
    {
        $ownerId = Auth::id();
        $fieldIds = Field::where('user_id', $ownerId)->pluck('id');
        return $this->reservations()->whereIn('field_id', $fieldIds)->count();
    }

    // Số lần đặt sân chưa xác nhận
    public function getCompleteReservationsAttribute()
    {
        $ownerId = Auth::id();
        $fieldIds = Field::where('user_id', $ownerId)->pluck('id');
        return $this->reservations()->whereIn('field_id', $fieldIds)->whereIn('status', ['đã xác nhận', 'đã thanh toán'])->count();
    }
      public function getTotalSpentByCustomer($ownerId)
    {
        // Lấy tất cả các sân mà chủ sân sở hữu
        $fields = Field::where('user_id', $ownerId)->pluck('id');
    
        // Lấy tổng tiền của các hóa đơn mà khách hàng đã thanh toán cho các sân mà chủ sân sở hữu
        return $this->reservations() // Liên kết tới reservations của khách hàng
            ->whereIn('reservations.field_id', $fields) // Chỉ rõ 'field_id' thuộc bảng 'reservations'
            ->join('invoices', 'reservations.id', '=', 'invoices.reservation_id') // Kết nối với bảng invoices
            ->sum('invoices.total_amount'); // Tính tổng số tiền từ các hóa đơn
    }

    // Số lần đặt sân bị hủy
    public function getCancelledReservationsAttribute()
    {
        $ownerId = Auth::id();
        $fieldIds = Field::where('user_id', $ownerId)->pluck('id');
        return $this->reservations()->whereIn('field_id', $fieldIds)->where('status', 'đã hủy')->count();
    }

    // Tính tỉ lệ hủy
    public function getCancellationRateAttribute()
    {
        return $this->total_reservations > 0 
            ? round(($this->cancelled_reservations / $this->total_reservations) * 100, 2)
            : 0;
    }
}
