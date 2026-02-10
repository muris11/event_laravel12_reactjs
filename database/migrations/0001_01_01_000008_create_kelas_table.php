<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 255);
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('jadwal', 255)->nullable();
            $table->string('ruangan', 255)->nullable();
            $table->decimal('biaya', 15, 2)->default(0);
            $table->integer('total_peserta')->default(0);
            $table->string('foto', 255)->nullable();
            $table->longText('gambaran_event')->nullable();
            $table->string('link_navigasi', 500)->default('');
            $table->boolean('is_link_eksternal')->default(false);
            $table->timestamps();
            $table->foreign('kategori_id')->references('id')->on('categories')->nullOnDelete();
            $table->index('kategori_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
