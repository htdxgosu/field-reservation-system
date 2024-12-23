<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldType extends Model
{
    use HasFactory;

    // Các trường có thể mass-assign (được điền vào trong hàm create hoặc update)
    protected $fillable = [
        'name', 'description'
    ];

    // Mối quan hệ với bảng fields (một loại sân có thể có nhiều sân)
    public function fields()
    {
        return $this->hasMany(Field::class);
    }
}
