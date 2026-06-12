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
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('FK → users.id');
            $table->string('name', 200)->comment('企业全称');
            $table->string('credit_code', 50)->comment('统一社会信用代码');
            $table->string('industry', 50)->comment('所属行业');
            $table->string('scale', 30)->nullable()->comment('企业规模');
            $table->text('intro')->nullable()->comment('企业简介');
            $table->string('contact_name', 30)->comment('联系人');
            $table->string('contact_phone', 20)->comment('联系人手机');
            $table->string('email', 100)->comment('企业邮箱');
            $table->foreignId('college_id')->nullable()->constrained('colleges')->nullOnDelete()->comment('FK → colleges.id（审核通过时指定）');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('审核状态');
            $table->text('audit_remark')->nullable()->comment('审核备注（驳回时必填）');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprises');
    }
};
