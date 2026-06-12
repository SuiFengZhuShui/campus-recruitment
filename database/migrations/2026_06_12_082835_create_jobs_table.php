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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->constrained('enterprises')->cascadeOnDelete()->comment('FK → enterprises.id');
            $table->string('title', 200)->comment('岗位名称');
            $table->unsignedInteger('count')->comment('招聘人数');
            $table->string('city', 50)->comment('工作城市');
            $table->unsignedInteger('salary_min')->comment('薪资下限');
            $table->unsignedInteger('salary_max')->comment('薪资上限');
            $table->string('education', 30)->comment('学历要求');
            $table->string('major', 200)->nullable()->comment('专业要求');
            $table->string('skills', 500)->nullable()->comment('技能标签（逗号分隔）');
            $table->enum('type', ['full-time', 'internship'])->comment('全职/实习');
            $table->text('duty')->comment('岗位职责');
            $table->text('requirement')->comment('任职要求');
            $table->text('welfare')->nullable()->comment('福利待遇');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('上架/下架');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
