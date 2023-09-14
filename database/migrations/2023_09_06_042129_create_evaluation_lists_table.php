<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_lists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('restriction_id');
            $table->uuid('academic_id');
            $table->uuid('teacher_id');
            $table->uuid('course_id');
            $table->uuid('subject_id');
            $table->uuid('questionID');
            $table->string('teacher');
            $table->string('criteria');
            $table->text('question');
            $table->string('answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_lists');
    }
}
