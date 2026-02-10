<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_status_log', function (Blueprint $table) {
            $table->id();
            $table->string('borrowing_id', 36);
            $table->string('status_lama', 50)->nullable();
            $table->string('status_baru', 50)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('borrowing_id')->references('id')->on('borrowings')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_status_log');
    }
};
