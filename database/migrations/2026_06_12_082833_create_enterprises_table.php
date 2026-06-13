<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterprisesTable extends Migration
{
    public function up()
    {
        Schema::create('enterprises', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->comment('FK → users.id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name', 200)->comment('企业全称');
            $table->string('credit_code', 50)->comment('统一社会信用代码');
            $table->string('industry', 50)->comment('所属行业');
            $table->string('scale', 30)->nullable()->comment('企业规模');
            $table->text('intro')->nullable()->comment('企业简介');
            $table->string('contact_name', 30)->comment('联系人');
            $table->string('contact_phone', 20)->comment('联系人手机');
            $table->string('email', 100)->comment('企业邮箱');
            $table->unsignedBigInteger('college_id')->nullable()->comment('FK → colleges.id（审核通过时指定）');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('set null');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('审核状态');
            $table->text('audit_remark')->nullable()->comment('审核备注（驳回时必填）');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enterprises');
    }
}
