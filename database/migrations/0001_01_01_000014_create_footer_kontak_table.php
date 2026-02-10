<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('footer_kontak', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->default('info@gastronomi.id');
            $table->string('phone', 100)->default('(021) 1234-5678');
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            $table->string('copyright_text', 255)->default('© 2024 Gastronomi. All rights reserved.');
            $table->string('social_facebook', 255)->nullable();
            $table->string('social_instagram', 255)->nullable();
            $table->string('social_twitter', 255)->nullable();
            $table->string('social_youtube', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_kontak');
    }
};
