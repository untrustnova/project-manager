<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            $table->unsignedBigInteger('user_id');
            $table->date('activity_date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->text('note')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'activity_date']);
            $table->unique(['user_id', 'activity_date']); // One record per user per day
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
