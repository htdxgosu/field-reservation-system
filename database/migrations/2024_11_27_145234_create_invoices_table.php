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
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_code')->unique()->nullable(); 
        $table->unsignedBigInteger('reservation_id');  // Khóa ngoại liên kết với bảng reservations
        $table->unsignedBigInteger('user_id');  // Khóa ngoại người dùng
        $table->unsignedBigInteger('field_id');  // Khóa ngoại sân bóng
        $table->decimal('total_amount', 10, 2);  // Tổng tiền
        $table->timestamps();

        // Khóa ngoại
        $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('field_id')->references('id')->on('fields')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
