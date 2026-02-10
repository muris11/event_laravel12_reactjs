<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kontak', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title', 200)->default('Hubungi Kami');
            $table->string('hero_subtitle', 200)->default('');
            $table->text('hero_description')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_items', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 50)->default('phone');
            $table->string('title', 100);
            $table->string('action_url', 500)->nullable();
            $table->integer('order_position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('contact_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_item_id');
            $table->string('detail_text', 255);
            $table->integer('detail_order')->default(0);
            $table->timestamps();
            $table->index('contact_item_id');
            $table->foreign('contact_item_id')->references('id')->on('contact_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_details');
        Schema::dropIfExists('contact_items');
        Schema::dropIfExists('kontak');
    }
};
