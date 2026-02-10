<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id');
            $table->string('kode', 100)->unique();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang', 'Perlu Perbaikan'])->default('Baik');
            $table->enum('status', ['Tersedia', 'Menunggu', 'Dipinjam', 'Rusak'])->default('Tersedia');
            $table->timestamps();
            $table->foreign('barang_id')->references('id')->on('items')->cascadeOnDelete();
            $table->index('kode');
            $table->index('status');
            $table->index('barang_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_units');
    }
};
