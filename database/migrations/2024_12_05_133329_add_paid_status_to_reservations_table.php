<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidStatusToReservationsTable extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Thêm trạng thái mới vào cột 'status'
            $table->enum('status', ['chờ xác nhận', 'đã xác nhận', 'đã hủy', 'đã thanh toán'])->default('chờ xác nhận')->change();
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Quay lại trạng thái ban đầu nếu cần
            $table->enum('status', ['chờ xác nhận', 'đã xác nhận', 'đã hủy'])->default('chờ xác nhận')->change();
        });
    }
}
