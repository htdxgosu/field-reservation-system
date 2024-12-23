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
        $table->integer('rental_count')->default(0); // Số lần thuê, mặc định là 0
    });
}

public function down()
{
    Schema::table('fields', function (Blueprint $table) {
        $table->dropColumn('rental_count');
    });
}

};
