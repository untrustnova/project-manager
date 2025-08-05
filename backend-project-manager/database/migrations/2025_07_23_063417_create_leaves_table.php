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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->string('leave_category');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->text('description')->nullable();
            $table->boolean('bring_laptop')->default(false);
            $table->boolean('still_be_contacted')->default(false);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('submitted_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['submitted_by_user_id', 'start_date', 'leave_category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
