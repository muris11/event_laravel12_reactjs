<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Event slider
        Schema::create('event_slider', function (Blueprint $table) {
            $table->id();
            $table->string('image_url', 500);
            $table->string('description', 500)->nullable();
            $table->integer('order_position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('orientasi', 20)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('crop_mode', 20)->default('smart');
            $table->timestamps();
        });

        // Tentang kami slider
        Schema::create('tentang_kami_slider', function (Blueprint $table) {
            $table->id();
            $table->string('image_url', 500);
            $table->string('description', 500)->nullable();
            $table->integer('order_position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('orientasi', 20)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('crop_mode', 20)->default('smart');
            $table->timestamps();
        });

        // Layanan content
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100);
            $table->string('section_key', 100);
            $table->string('content_type', 20)->default('text');
            $table->longText('content_value')->nullable();
            $table->timestamps();
            $table->unique(['section', 'section_key'], 'unique_layanan_section_key');
        });

        // Layanan slider
        Schema::create('layanan_slider', function (Blueprint $table) {
            $table->id();
            $table->string('image_url', 500);
            $table->string('description', 500)->nullable();
            $table->integer('order_position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('orientasi', 20)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('crop_mode', 20)->default('smart');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan_slider');
        Schema::dropIfExists('layanan');
        Schema::dropIfExists('tentang_kami_slider');
        Schema::dropIfExists('event_slider');
    }
};
