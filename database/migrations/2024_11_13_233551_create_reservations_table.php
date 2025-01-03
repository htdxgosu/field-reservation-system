<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('field_id')->constrained()->onDelete('cascade');
            // Set start_time mặc định là thời gian hiện tại
            $table->timestamp('start_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            // Trạng thái bằng tiếng Việt
            $table->enum('status', ['chờ xác nhận', 'đã xác nhận', 'đã hủy','đã thanh toán'])->default('chờ xác nhận');

            // Thêm cột ghi chú
            $table->text('note')->nullable();  // Ghi chú có thể để trống
             // Thêm cột thành tiền
             $table->decimal('total_amount', 10, 2)->nullable(); // Thành tiền có thể null nếu chưa tính toán

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');  // Xóa bảng nếu rollback migration
    }
}
