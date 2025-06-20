<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shareable_links', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('expires_at');
            $table->integer('max_views')->nullable();
            $table->integer('view_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shareable_link_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shareable_link_id')->constrained('shareable_links');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referer')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shareable_link_views');
        Schema::dropIfExists('shareable_links');
    }
}; 