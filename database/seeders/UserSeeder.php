<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Đừng quên import model User
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Thêm người dùng mẫu vào bảng users sử dụng Eloquent Model
        User::create([
            'name' => 'User 1',
            'phone' => '0123456789',
            'email' => 'user1@example.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'User 2',
            'phone' => '0123456788',
            'email' => 'user2@example.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        User::create([
            'name' => 'User 3',
            'phone' => '0123456787',
            'email' => 'user3@example.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
