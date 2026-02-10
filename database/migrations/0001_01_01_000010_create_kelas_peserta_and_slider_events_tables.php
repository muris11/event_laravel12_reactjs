<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas_peserta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kelas_id');
            $table->string('nama_peserta', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('no_telp', 50)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('kelas_id')->references('id')->on('kelas')->cascadeOnDelete();
        });

        Schema::create('slider_events', function (Blueprint $table) {
            $table->id();
            $table->json('selected_events');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_peserta');
        Schema::dropIfExists('slider_events');
    }
};
