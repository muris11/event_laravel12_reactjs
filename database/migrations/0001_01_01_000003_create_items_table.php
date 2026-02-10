<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang', 255);
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->integer('tahun_perolehan')->default(2025);
            $table->text('deskripsi')->nullable();
            $table->string('foto', 500)->nullable();
            $table->timestamps();
            $table->foreign('kategori_id')->references('id')->on('categories')->nullOnDelete();
            $table->index('nama_barang');
            $table->index('kategori_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
