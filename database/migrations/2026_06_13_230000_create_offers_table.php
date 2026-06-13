<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
    public function up()
    {
        Schema::create('offers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('application_id')->comment('FK → applications.id');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->string('position', 200)->comment('录用岗位');
            $table->string('salary', 100)->comment('薪资待遇');
            $table->date('start_date')->comment('入职日期');
            $table->string('note', 500)->nullable()->comment('备注');
            $table->enum('status', ['draft', 'sent', 'accepted', 'declined'])->default('draft')->comment('状态');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
}
