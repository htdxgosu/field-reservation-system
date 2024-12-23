<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('reservation_id')->constrained()->onDelete('cascade'); // Liên kết với bảng reservations
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với bảng users
        $table->foreignId('field_id')->constrained()->onDelete('cascade'); // Liên kết với bảng fields
        $table->string('action'); // Ví dụ: "Đặt sân", "Xác nhận", "Hủy đơn"
        $table->timestamps(); // Thời gian tạo và cập nhật
    });
}

public function down()
{
    Schema::dropIfExists('activity_logs');
}

};
