<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherEvaluationStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_evaluation_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('restriction_id');
            $table->uuid('evaluator_id');
            $table->uuid('academic_id');
            $table->uuid('teacher_id');
            $table->uuid('course_id');
            $table->uuid('subject_id');
            $table->text('teacher');
            $table->text('course');
            $table->text('subject');
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
        Schema::dropIfExists('teacher_evaluation_statuses');
    }
}
