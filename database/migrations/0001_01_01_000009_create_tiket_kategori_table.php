<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_kategori', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelas_id');
            $table->string('nama_kategori', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 15, 2)->default(0);
            $table->text('manfaat')->nullable();
            $table->boolean('is_populer')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->foreign('kelas_id')->references('id')->on('kelas')->cascadeOnDelete();
            $table->index('kelas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_kategori');
    }
};
