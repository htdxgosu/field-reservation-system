<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Field; // Đảm bảo bạn đã import model Field
use Illuminate\Support\Facades\DB;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tạo dữ liệu mẫu cho bảng fields
        $fields = [
            [
                'name' => 'Sân 5 người A',
                'location' => 'Địa chỉ 1',
                'price_per_hour' => 150000,
                'peak_price_per_hour' => 200000,
                'field_type_id' => 1,
                'description' => 'Sân bóng nhân tạo cho 5 người',
                'image_url' => 'img/san-1.jpg',
                'second_image_url' => 'img/san-1-second.jpg',
                'availability' => 'Đang trống',
                'user_id' => 1, // Giả sử user_id = 1 tồn tại
                'opening_time' => '07:00:00',
                'closing_time' => '22:00:00',
            ],
            [
                'name' => 'Sân 5 người B',
                'location' => 'Địa chỉ 2',
                'price_per_hour' => 160000,
                'peak_price_per_hour' => 210000,
                'field_type_id' => 1,
                'description' => 'Sân bóng phù hợp cho các trận đấu nhỏ',
                'image_url' => 'img/san-2.jpg',
                'second_image_url' => 'img/san-2-second.jpg',
                'availability' => 'Đang trống',
                'user_id' => 1,
                'opening_time' => '07:00:00',
                'closing_time' => '22:00:00',
            ],
            [
                'name' => 'Sân 7 người A',
                'location' => 'Địa chỉ 3',
                'price_per_hour' => 200000,
                'peak_price_per_hour' => 250000,
                'field_type_id' => 2,
                'description' => 'Sân 7 người với cỏ nhân tạo',
                'image_url' => 'img/san-3.jpg',
                'second_image_url' => 'img/san-3-second.jpg',
                'availability' => 'Đang trống',
                'user_id' => 2,
                'opening_time' => '07:00:00',
                'closing_time' => '22:30:00',
            ],
            [
                'name' => 'Sân 7 người B',
                'location' => 'Địa chỉ 4',
                'price_per_hour' => 210000,
                'peak_price_per_hour' => 260000,
                'field_type_id' => 2,
                'description' => 'Sân rộng rãi cho 7 người',
                'image_url' => 'img/san-4.jpg',
                'second_image_url' => 'img/san-4-second.jpg',
                'availability' => 'Đang trống',
                'user_id' => 2,
                'opening_time' => '07:30:00',
                'closing_time' => '22:30:00',
            ],
            [
                'name' => 'Sân 11 người A',
                'location' => 'Địa chỉ 5',
                'price_per_hour' => 300000,
                'peak_price_per_hour' => 350000,
                'field_type_id' => 3,
                'description' => 'Sân bóng tiêu chuẩn cho 11 người',
                'image_url' => 'img/san-5.jpg',
                'second_image_url' => 'img/san-5-second.jpg',
                'availability' => 'Đang trống',
                'user_id' => 3,
                'opening_time' => '06:00:00',
                'closing_time' => '23:00:00',
            ],
            [
                'name' => 'Sân 11 người B',
                'location' => 'Địa chỉ 6',
                'price_per_hour' => 320000,
                'peak_price_per_hour' => 370000,
                'field_type_id' => 3,
                'description' => 'Sân rộng rãi cho 11 người',
                'image_url' => 'img/san-6.jpg',
                'second_image_url' => 'img/san-6-second.jpg',
                'availability' => 'Đang trống',
                'user_id' => 3,
                'opening_time' => '06:30:00',
                'closing_time' => '23:30:00',
            ],
        ];

        // Thêm dữ liệu vào bảng fields
        foreach ($fields as $field) {
            Field::create($field);
        }
    }
}
