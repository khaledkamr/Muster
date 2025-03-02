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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('description');
            $table->string('department');
            $table->enum('semester', ['first', 'second', 'both'])->default('both');
            $table->enum('type', ['elective', 'compulsory'])->default('compulsory');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->unsignedBigInteger('professor_id');
            $table->timestamps();
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
