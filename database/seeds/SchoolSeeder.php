<?php

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

        $this->command->info("School admin created: admin / admin123 (school_id={$school->id})");
    }
}
