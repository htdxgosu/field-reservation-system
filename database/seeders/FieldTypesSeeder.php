<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FieldType; // Đảm bảo bạn đã import model FieldType

class FieldTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Thêm loại sân mẫu vào bảng field_types sử dụng Eloquent Model
        FieldType::create([
            'name' => '5 người',
            'description' => 'Sân dành cho mỗi đội 5 cầu thủ',
        ]);

        FieldType::create([
            'name' => '7 người',
            'description' => 'Sân dành cho mỗi đội 7 cầu thủ',
        ]);

        FieldType::create([
            'name' => '11 người',
            'description' => 'Sân dành cho mỗi đội 11 cầu thủ',
        ]);
    }
}
