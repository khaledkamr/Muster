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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->date('due_date');
            $table->decimal('score', 3, 1)->nullable();
            $table->enum('status', ['pending', 'submitted'])->default('pending');
            $table->timestamps();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('professor_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
