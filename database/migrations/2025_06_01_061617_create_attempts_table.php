<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttemptsTable extends Migration
{
    public function up()
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->bigIncrements('attempt_id');
            $table->unsignedBigInteger('assignment_id');
            $table->unsignedBigInteger('student_id');
            $table->json('answer_record');
            $table->string('encrypted_score');
            $table->timestamps();

            $table->foreign('assignment_id')
                ->references('id')->on('assignments')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('attempts', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
            $table->dropForeign(['student_id']);
        });
        Schema::dropIfExists('attempts');
    }
}
