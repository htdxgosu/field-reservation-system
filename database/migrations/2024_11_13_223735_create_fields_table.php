<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên sân
            $table->string('location'); // Địa chỉ sân
            $table->decimal('price_per_hour', 10, 2); // Giá thuê theo giờ
            $table->decimal('peak_price_per_hour', 10, 2)->nullable(); // Giá thuê giờ cao điểm
            $table->foreignId('field_type_id')->constrained('field_types'); // Khóa ngoại liên kết với bảng loại sân
            $table->text('description')->nullable(); // Mô tả sân
            $table->string('image_url')->nullable(); // Đường dẫn hình ảnh
            $table->string('second_image_url')->nullable(); // Thêm cột 'second_image_url'
            $table->enum('availability', ['Đang trống', 'Đang sử dụng', 'Đã được đặt']); // Tình trạng sân
            $table->timestamps(); // Thời gian tạo và cập nhật
             // Thêm cột user_id (khóa ngoại liên kết với bảng users)
             $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
             // Thêm cột opening_time và closing_time để lưu giờ mở cửa và đóng cửa
             $table->time('opening_time');
             $table->time('closing_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fields');
    }
}
