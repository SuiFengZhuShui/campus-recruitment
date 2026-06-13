<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('FK → users.id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('student_no', 30)->comment('学号');
            $table->string('class_name', 100)->comment('班级');
            $table->string('grade', 10)->comment('年级（学号前4位提取）');
            $table->unsignedBigInteger('college_id')->comment('FK → colleges.id（学号自动匹配）');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->string('resume_path', 500)->nullable()->comment('简历文件路径');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
