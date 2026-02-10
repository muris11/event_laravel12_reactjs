<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tentang_kami_tim', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->text('deskripsi')->nullable();
            $table->string('foto', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('urutan');
            $table->index('is_active');
        });

        Schema::create('tentang_kami_tim_keahlian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tim_id');
            $table->string('keahlian', 100);
            $table->integer('urutan')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('tim_id')->references('id')->on('tentang_kami_tim')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tentang_kami_tim_keahlian');
        Schema::dropIfExists('tentang_kami_tim');
    }
};
