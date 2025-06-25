<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['logo', 'description', 'email', 'phone', 'address']);
        });
    }

    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
        });
    }
};
