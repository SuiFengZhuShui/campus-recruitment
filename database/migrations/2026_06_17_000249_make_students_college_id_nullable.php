<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeStudentsCollegeIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Skip — column already nullable from original migration.
        // This migration exists to document the intent; the original
        // create_students_table already defines college_id as nullable.
    }

    public function down()
    {
        // No-op — column was always nullable.
    }
}
