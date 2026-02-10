<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nama_peminjam', 255);
            $table->unsignedBigInteger('barang_id');
            $table->string('unit_kode', 100);
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->text('keperluan')->nullable();
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Dipinjam', 'Selesai', 'Menunggu Verifikasi Pengembalian'])->default('Menunggu');
            $table->string('alasan_penolakan', 500)->nullable();
            $table->date('tanggal_verifikasi')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('barang_id')->references('id')->on('items')->cascadeOnDelete();
            $table->index('status');
            $table->index('tanggal_pinjam');
            $table->index('nama_peminjam');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
