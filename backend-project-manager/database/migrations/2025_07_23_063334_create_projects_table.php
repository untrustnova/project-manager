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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('project_name');
            $table->date('start_date');
            $table->date('deadline');
            $table->unsignedBigInteger('project_director')->nullable();
            $table->enum('level', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('status', ['ongoing', 'pending', 'completed'])->default('pending');
            $table->timestamps();
            
            // Foreign key
            $table->foreign('project_director')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['status', 'deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
