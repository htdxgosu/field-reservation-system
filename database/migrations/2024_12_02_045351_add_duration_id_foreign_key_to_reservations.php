<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationIdForeignKeyToReservations extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Đảm bảo duration_id là unsignedBigInteger
            $table->unsignedBigInteger('duration_id');

            // Thêm khóa ngoại
            $table->foreign('duration_id')->references('id')->on('durations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Xóa khóa ngoại khi rollback migration
            $table->dropForeign(['duration_id']);
            $table->dropColumn('duration_id');
        });
    }
}
