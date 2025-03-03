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
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id');
            $table->string('semester');
            $table->integer('quiz1')->nullable();
            $table->integer('quiz2')->nullable();
            $table->integer('midterm')->nullable();
            $table->integer('project')->nullable();
            $table->integer('final')->nullable();
            $table->integer('total')->nullable();
            $table->enum('grade', ['A+', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'F'])->default('F');
            $table->enum('status', ['pass', 'fail'])->default('fail');
            $table->timestamps();
            $table->unique(['student_id', 'course_id', 'semester']);
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
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
