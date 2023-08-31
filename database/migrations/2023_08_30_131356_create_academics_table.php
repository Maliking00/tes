<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('academicYear');
            $table->string('academicSemester');
            $table->boolean('academicSystemDefault')->default(0);
            $table->enum('academicEvaluationStatus', ['notStarted', 'starting', 'closed'])->default('notStarted');
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
        Schema::dropIfExists('academics');
    }
}
