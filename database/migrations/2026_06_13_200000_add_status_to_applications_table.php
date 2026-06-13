<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToApplicationsTable extends Migration
{
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('status', ['pending', 'reviewed', 'interviewed', 'accepted', 'rejected'])
                ->default('pending')
                ->after('student_id')
                ->comment('投递状态');
            $table->string('remark', 500)->nullable()->after('status')->comment('企业备注');
        });
    }

    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['status', 'remark']);
        });
    }
}
