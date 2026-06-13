<?php

use App\Models\College;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run()
    {
        $school = School::create([
            'name' => '校园招聘平台',
        ]);

        $college = College::create([
            'school_id' => $school->id,
            'name' => '计算机学院',
        ]);

        // School admin
        User::create([
            'role' => 'school',
            'username' => 'admin',
            'college_id' => null,
            'name' => '学校管理员',
            'phone' => '13800000000',
            'email' => 'admin@school.com',
            'password' => Hash::make('admin123'),
            'status' => 'active',
        ]);

        // College admin
        User::create([
            'role' => 'college',
            'username' => 'college',
            'college_id' => $college->id,
            'name' => '计算机学院管理员',
            'phone' => '13800000001',
            'email' => 'college@school.com',
            'password' => Hash::make('college123'),
            'status' => 'active',
        ]);

        $this->command->info("School admin: admin / admin123");
        $this->command->info("College admin: college / college123 (college_id={$college->id})");
    }
}
