<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('category')->default('media')->after('file_size');
            $table->dropColumn('thumbnail_path');
        });
    }

    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('thumbnail_path')->nullable();
            $table->dropColumn('category');
        });
    }
};
