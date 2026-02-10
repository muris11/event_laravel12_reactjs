<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('borrowing_id', 36);
            $table->date('tanggal_pengembalian');
            $table->enum('kondisi_barang', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang', 'Perlu Perbaikan']);
            $table->text('catatan')->nullable();
            $table->string('foto', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('borrowing_id')->references('id')->on('borrowings')->cascadeOnDelete();
            $table->index('borrowing_id');
            $table->index('tanggal_pengembalian');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
