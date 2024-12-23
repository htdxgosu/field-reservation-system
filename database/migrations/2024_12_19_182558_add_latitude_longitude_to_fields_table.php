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
    Schema::table('fields', function (Blueprint $table) {
        $table->decimal('latitude', 10, 7)->nullable();  // Vị trí vĩ độ
        $table->decimal('longitude', 10, 7)->nullable(); // Vị trí kinh độ
    });
}

public function down()
{
    Schema::table('fields', function (Blueprint $table) {
        $table->dropColumn(['latitude', 'longitude']);
    });
}

};
