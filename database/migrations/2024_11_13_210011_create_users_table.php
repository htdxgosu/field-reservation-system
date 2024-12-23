<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();  // Tạo cột id tự động tăng
            $table->string('name');  // Tạo cột name
            $table->string('phone', 10)->unique();  // Tạo cột phone, giới hạn 10 ký tự và duy nhất
            $table->string('email');  // Tạo cột email
            $table->timestamps();  // Tạo cột created_at và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');  // Xóa bảng khi rollback migration
    }
}
