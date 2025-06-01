<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->uuid('attempt_id')->primary();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('score_id')->nullable();
            $table->json('answer_record');
            $table->timestamps();

            $table->foreign('assignment_id')->references('id')->on('assignments')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('score_id')->references('id')->on('scores')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
