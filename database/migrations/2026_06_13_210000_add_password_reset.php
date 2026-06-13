<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPasswordReset extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('remember_token', 100)->nullable()->after('password');
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email', 100)->index();
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('remember_token');
        });
        Schema::dropIfExists('password_resets');
    }
}
