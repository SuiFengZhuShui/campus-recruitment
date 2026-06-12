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
        Schema::create('enterprise_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->constrained('enterprises')->cascadeOnDelete()->comment('FK → enterprises.id');
            $table->enum('type', ['license', 'id_card', 'authorization'])->comment('执照/身份证/授权书');
            $table->string('file_path', 500)->comment('存储路径');
            $table->string('file_name', 200)->comment('原始文件名');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('该项审核结果');
            $table->string('reject_reason', 500)->nullable()->comment('该项不通过原因');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprise_docs');
    }
};
