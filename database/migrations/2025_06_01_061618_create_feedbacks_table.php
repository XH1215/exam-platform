<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeedbacksTable extends Migration
{
    public function up()
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('teacher_id');
            $table->decimal('grade', 5, 2);
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('assignment_id')
                ->references('id')->on('assignments')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('teacher_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['teacher_id']);
        });
        Schema::dropIfExists('feedbacks');
    }
}
