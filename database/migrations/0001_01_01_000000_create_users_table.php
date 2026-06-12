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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('role', ['school', 'college', 'enterprise', 'student'])->comment('角色');
            $table->unsignedBigInteger('college_id')->nullable()->comment('FK → colleges.id（学院/学生时有值）');
            $table->string('name', 50)->comment('显示名称');
            $table->string('phone', 20)->unique()->comment('手机号');
            $table->string('password', 255)->comment('bcrypt 哈希');
            $table->enum('status', ['active', 'disabled'])->default('active')->comment('账号状态');
            $table->timestamps();
            $table->index(['role', 'phone']);
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
