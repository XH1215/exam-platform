<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assignment_id');
            $table->text('question_text');
            $table->text('correct_answer');
            $table->json('options');
            $table->timestamps();

            $table->foreign('assignment_id')
                ->references('id')->on('assignments')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['assignment_id']);
        });
        Schema::dropIfExists('questions');
    }
}
