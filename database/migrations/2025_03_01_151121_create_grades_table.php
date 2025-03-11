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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->unique(['student_id', 'course_id']);
            $table->integer('quiz1')->nullable();
            $table->integer('quiz2')->nullable();
            $table->integer('midterm')->nullable();
            $table->integer('assignments')->nullable();
            $table->integer('project')->nullable();
            $table->integer('final')->nullable();
            $table->integer('total')->nullable();
            $table->enum('grade', ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'F'])->nullable();
            $table->enum('status', ['pass', 'fail'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
