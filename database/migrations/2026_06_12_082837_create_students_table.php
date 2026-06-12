<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('FK → users.id');
            $table->string('student_no', 30)->comment('学号');
            $table->string('class_name', 100)->comment('班级');
            $table->string('grade', 10)->comment('年级（学号前4位提取）');
            $table->foreignId('college_id')->constrained('colleges')->cascadeOnDelete()->comment('FK → colleges.id（学号自动匹配）');
            $table->string('resume_path', 500)->nullable()->comment('简历文件路径');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
