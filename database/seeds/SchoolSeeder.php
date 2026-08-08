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
        $school = School::firstOrCreate([
            'name' => '校园招聘平台',
        ]);

        // 8 个二级学院（教学/思政类学院不参与招聘）
        $colleges = [
            '机电工程学院',
            '汽车工程学院',
            '电子信息工程学院',
            '环境与食品工程学院',
            '财经与物流管理学院',
            '贸易与旅游管理学院',
            '艺术学院',
            '国际教育学院',
        ];

        $collegeIds = [];
        foreach ($colleges as $name) {
            $college = College::firstOrCreate([
                'school_id' => $school->id,
                'name' => $name,
            ]);
            $collegeIds[] = $college->id;
        }

        // School admin
        User::firstOrCreate(
            ['phone' => '13700000000'],
            [
                'role' => 'school',
                'username' => 'admin',
                'college_id' => null,
                'name' => '学校管理员',
                'email' => 'admin@school.com',
                'password' => Hash::make('123456'),
                'status' => 'active',
            ]
        );

        // 每个学院一个管理员账号
        $collegeUsers = [
            ['username' => 'jidian',    'name' => '机电工程学院管理员'],
            ['username' => 'qiche',     'name' => '汽车工程学院管理员'],
            ['username' => 'dianxin',   'name' => '电子信息工程学院管理员'],
            ['username' => 'huanjing',  'name' => '环境与食品工程学院管理员'],
            ['username' => 'caijing',   'name' => '财经与物流管理学院管理员'],
            ['username' => 'maoyi',     'name' => '贸易与旅游管理学院管理员'],
            ['username' => 'yishu',     'name' => '艺术学院管理员'],
            ['username' => 'guojiao',   'name' => '国际教育学院管理员'],
        ];

        foreach ($collegeUsers as $i => $cu) {
            User::firstOrCreate(
                ['phone' => '1370000001' . $i],
                [
                    'role' => 'college',
                    'username' => $cu['username'],
                    'college_id' => $collegeIds[$i],
                    'name' => $cu['name'],
                    'email' => $cu['username'] . '@school.com',
                    'password' => Hash::make('123456'),
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('School admin: admin / 123456');
        $this->command->info('College accounts (all password: 123456):');
        foreach ($collegeUsers as $cu) {
            $this->command->info("  {$cu['username']} — {$cu['name']}");
        }
    }
}
