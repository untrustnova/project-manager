<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->unsignedBigInteger('user_id');
            $table->date('activity_date'); // tanggal absen
            $table->time('check_in')->nullable(); // waktu masuk
            $table->time('check_out')->nullable(); // waktu pulang
            $table->enum('status', ['hadir', 'telat', 'izin', 'sakit', 'cuti', 'alfa'])->default('hadir');
            $table->text('note')->nullable(); // alasan / catatan opsional
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'activity_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity');
    }
};
