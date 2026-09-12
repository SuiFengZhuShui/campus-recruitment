<?php

use App\Models\College;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 学校与学院基础数据 + 学校/学院管理员账号
 *
 * 演示账号密码取自 .env 的 SEED_PASSWORD（见 DemoPassword），不硬编码。
 */
class SchoolSeeder extends Seeder
{
    public function run()
    {
        $school = $this->seedSchool();
        $collegeIds = $this->seedColleges($school);

        $this->seedSchoolAdmin();
        $this->seedCollegeAdmins($collegeIds);
        $this->printSummary();
    }

    /**
     * 学校主体
     *
     * @return School
     */
    private function seedSchool()
    {
        return School::firstOrCreate(['name' => '校园招聘平台']);
    }

    /**
     * 8 个二级学院（教学/思政类学院不参与招聘）
     *
     * @return array 学院 ID 列表，顺序与 collegeNames() 一致
     */
    private function seedColleges(School $school)
    {
        $collegeIds = [];
        foreach ($this->collegeNames() as $name) {
            $key = ['school_id' => $school->id, 'name' => $name];
            $collegeIds[] = College::firstOrCreate($key)->id;
        }

        return $collegeIds;
    }

    /**
     * @return array
     */
    private function collegeNames()
    {
        return [
            '机电工程学院',
            '汽车工程学院',
            '电子信息工程学院',
            '环境与食品工程学院',
            '财经与物流管理学院',
            '贸易与旅游管理学院',
            '艺术学院',
            '国际教育学院',
        ];
    }

    /**
     * 学校管理员账号
     */
    private function seedSchoolAdmin()
    {
        $values = [
            'role' => 'school',
            'username' => 'admin',
            'college_id' => null,
            'name' => '学校管理员',
            'email' => 'admin@school.com',
            'password' => Hash::make(DemoPassword::get()),
            'status' => 'active',
        ];

        User::firstOrCreate(['phone' => '13700000000'], $values);
    }

    /**
     * 每个学院一个管理员账号
     */
    private function seedCollegeAdmins(array $collegeIds)
    {
        foreach ($this->collegeUsers() as $i => $cu) {
            $key = ['phone' => '1370000001' . $i];
            $values = $this->collegeAdminValues($cu, $collegeIds[$i]);
            User::firstOrCreate($key, $values);
        }
    }

    /**
     * 学院管理员账号的写入字段
     */
    private function collegeAdminValues(array $cu, $collegeId)
    {
        return [
            'role' => 'college',
            'username' => $cu['username'],
            'college_id' => $collegeId,
            'name' => $cu['name'],
            'email' => $cu['username'] . '@school.com',
            'password' => Hash::make(DemoPassword::get()),
            'status' => 'active',
        ];
    }

    /**
     * @return array
     */
    private function collegeUsers()
    {
        return [
            ['username' => 'jidian', 'name' => '机电工程学院管理员'],
            ['username' => 'qiche', 'name' => '汽车工程学院管理员'],
            ['username' => 'dianxin', 'name' => '电子信息工程学院管理员'],
            ['username' => 'huanjing', 'name' => '环境与食品工程学院管理员'],
            ['username' => 'caijing', 'name' => '财经与物流管理学院管理员'],
            ['username' => 'maoyi', 'name' => '贸易与旅游管理学院管理员'],
            ['username' => 'yishu', 'name' => '艺术学院管理员'],
            ['username' => 'guojiao', 'name' => '国际教育学院管理员'],
        ];
    }

    /**
     * 打印账号清单（不回显密码，密码见 .env 的 SEED_PASSWORD）
     */
    private function printSummary()
    {
        $this->command->info('学校管理员: admin（密码取自 .env 的 SEED_PASSWORD）');
        $this->command->info('学院管理员账号:');
        foreach ($this->collegeUsers() as $cu) {
            $this->command->info("  {$cu['username']} — {$cu['name']}");
        }
    }
}
