<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tentang_kami', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100);
            $table->string('section_key', 100);
            $table->string('content_type', 20)->default('text');
            $table->longText('content_value')->nullable();
            $table->timestamps();
            $table->unique(['section', 'section_key'], 'unique_tentang_section_key');
        });

        Schema::create('partner', function (Blueprint $table) {
            $table->id();
            $table->string('section', 100);
            $table->string('section_key', 100);
            $table->string('content_type', 20)->default('text');
            $table->longText('content_value')->nullable();
            $table->timestamps();
            $table->unique(['section', 'section_key'], 'unique_partner_section_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tentang_kami');
        Schema::dropIfExists('partner');
    }
};
