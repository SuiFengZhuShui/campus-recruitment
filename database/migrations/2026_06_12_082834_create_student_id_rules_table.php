<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentIdRulesTable extends Migration
{
    public function up()
    {
        Schema::create('student_id_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('school_id')->comment('FK → schools.id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->string('prefix', 20)->comment('学号前缀');
            $table->unsignedBigInteger('college_id')->comment('FK → colleges.id');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_id_rules');
    }
}
