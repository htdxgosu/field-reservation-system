<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveEndTimeFromReservations extends Migration
{
    public function up()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('end_time'); // Xóa cột end_time
        });
    }

    public function down()
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('end_time');
        });
    }
}
