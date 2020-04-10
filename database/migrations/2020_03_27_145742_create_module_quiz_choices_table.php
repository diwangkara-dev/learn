<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleQuizChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_quiz_choices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('module_quiz_id');
            $table->string('content');
            $table->boolean('answer')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('module_quiz_id')->references('id')->on('module_quizzes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_quiz_choices');
    }
}
