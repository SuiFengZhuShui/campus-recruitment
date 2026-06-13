<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterviewsTable extends Migration
{
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->comment('FK → applications.id');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->dateTime('scheduled_at')->comment('面试时间');
            $table->string('location', 200)->comment('面试地点/会议链接');
            $table->enum('type', ['online', 'on-site'])->default('on-site')->comment('线上面试/线下面试');
            $table->string('contact', 50)->nullable()->comment('联系人');
            $table->string('note', 500)->nullable()->comment('面试备注');
            $table->enum('status', ['invited', 'accepted', 'declined', 'completed'])->default('invited')->comment('状态');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interviews');
    }
}
