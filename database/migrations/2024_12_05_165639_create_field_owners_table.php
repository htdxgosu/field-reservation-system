<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Liên kết với bảng users
            $table->string('address'); // Địa chỉ
            $table->string('identity'); // CMND/CCCD
            $table->string('business_license'); // Giấy phép kinh doanh
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])->default('pending'); // Trạng thái chủ sân
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
        Schema::dropIfExists('field_owners');
    }
}
