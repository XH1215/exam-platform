<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentStudentTable extends Migration
{
    public function up()
    {
        Schema::create('assignment_student', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->timestamps();

            $table->foreign('assignment_id')
                  ->references('id')->on('assignments')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down()
    {
        Schema::table('assignment_student', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropForeign(['student_id']);
            $table->dropUnique(['assignment_id', 'student_id']);
        });
        Schema::dropIfExists('assignment_student');
    }
}
