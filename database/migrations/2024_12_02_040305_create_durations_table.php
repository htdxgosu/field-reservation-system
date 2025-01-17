<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDurationsTable extends Migration
{
    public function up()
    {
        Schema::create('durations', function (Blueprint $table) {
            $table->id();
            $table->integer('duration');  // Đơn vị phút (ví dụ: 30, 60, 90)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('durations');
    }
}
