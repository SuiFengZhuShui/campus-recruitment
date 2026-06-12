<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::create([
            'name' => '校园招聘平台',
        ]);

        User::create([
            'role' => 'school',
            'college_id' => null,
            'name' => '学校管理员',
            'phone' => '13800000000',
            'password' => Hash::make('admin123'),
            'status' => 'active',
        ]);

        $this->command->info("School admin created: 13800000000 / admin123 (school_id={$school->id})");
    }
}
