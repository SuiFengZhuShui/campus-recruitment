<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterpriseDocsTable extends Migration
{
    public function up()
    {
        Schema::create('enterprise_docs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('enterprise_id')->comment('FK → enterprises.id');
            $table->foreign('enterprise_id')->references('id')->on('enterprises')->onDelete('cascade');
            $table->enum('type', ['license', 'id_card', 'authorization'])->comment('执照/身份证/授权书');
            $table->string('file_path', 500)->comment('存储路径');
            $table->string('file_name', 200)->comment('原始文件名');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('该项审核结果');
            $table->string('reject_reason', 500)->nullable()->comment('该项不通过原因');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enterprise_docs');
    }
}
