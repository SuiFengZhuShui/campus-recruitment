<?php

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(SchoolSeeder::class);

        // === Schools & Colleges ===
        $schools = [
            '清华大学' => ['计算机学院', '软件学院', '电子工程学院'],
            '北京大学' => ['信息科学技术学院', '数学科学学院'],
            '浙江大学' => ['计算机科学与技术学院', '信息与电子工程学院'],
        ];

        foreach ($schools as $schoolName => $collegeNames) {
            $school = School::create(['name' => $schoolName]);
            foreach ($collegeNames as $collegeName) {
                College::create(['school_id' => $school->id, 'name' => $collegeName]);
            }
        }

        $colleges = College::all();

        // === Enterprises (10) ===
        $industries = ['IT', '金融', '教育', '制造', '医疗', '房地产', '能源', '通信', '零售', '媒体'];
        for ($i = 1; $i <= 10; $i++) {
            $entUser = User::create([
                'role' => 'enterprise',
                'username' => 'enterprise' . $i,
                'name' => '企业' . $i . '联系人',
                'phone' => '139' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'enterprise' . $i . '@test.com',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]);

            $status = $i <= 8 ? 'approved' : ($i === 9 ? 'pending' : 'rejected');
            Enterprise::create([
                'user_id' => $entUser->id,
                'name' => $industries[$i - 1] . '科技公司' . $i,
                'credit_code' => '911100000000' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'industry' => $industries[$i - 1],
                'scale' => ['1-50人', '50-200人', '200-500人', '500人以上'][$i % 4],
                'intro' => '这是一家专注于' . $industries[$i - 1] . '领域的优秀企业。',
                'contact_name' => '企业' . $i . '联系人',
                'contact_phone' => '139' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'enterprise' . $i . '@test.com',
                'college_id' => $i <= 8 ? $colleges->random()->id : null,
                'status' => $status,
                'audit_remark' => $status === 'rejected' ? '资质材料不完整' : null,
            ]);
        }

        $approvedEnterprises = Enterprise::where('status', 'approved')->get();

        // === Students (50) ===
        $majors = ['软工', '计科', '大数据', '人工智能', '通信', '电子信息'];
        for ($i = 1; $i <= 50; $i++) {
            $year = ['2020', '2021', '2022', '2023', '2024'][$i % 5];
            $college = $colleges->random();

            $stuUser = User::create([
                'role' => 'student',
                'username' => 'student' . $i,
                'name' => '学生' . $i,
                'phone' => '138' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'student' . $i . '@test.com',
                'password' => Hash::make('password'),
                'college_id' => $college->id,
                'status' => 'active',
            ]);

            Student::create([
                'user_id' => $stuUser->id,
                'student_no' => $year . str_pad($i, 5, '0', STR_PAD_LEFT),
                'class_name' => $majors[$i % 6] . ($i % 3 + 1) . '班',
                'grade' => $year,
                'college_id' => $college->id,
            ]);
        }

        $students = Student::all();

        // === Jobs (30) ===
        $titles = ['前端开发工程师', '后端开发工程师', '测试工程师', '产品经理', 'UI设计师', '数据分析师', '运维工程师', '算法工程师', '产品运营', '安全工程师'];
        $cities = ['北京', '上海', '广州', '深圳', '杭州', '成都', '武汉', '南京'];

        for ($i = 1; $i <= 30; $i++) {
            $min = [3000, 5000, 8000, 10000, 15000][$i % 5];
            $enterprise = $approvedEnterprises->random();
            Job::create([
                'enterprise_id' => $enterprise->id,
                'title' => $titles[$i % 10] . '（' . ['初级', '中级', '高级'][$i % 3] . '）',
                'count' => ($i % 5) + 1,
                'city' => $cities[$i % 8],
                'salary_min' => $min,
                'salary_max' => $min * (1.5 + ($i % 3) * 0.5),
                'education' => ['大专', '本科', '硕士', '不限'][$i % 4],
                'type' => $i % 3 === 0 ? 'internship' : 'full-time',
                'duty' => '负责' . $titles[$i % 10] . '相关工作，参与团队协作和技术攻关。',
                'requirement' => ($i % 2 === 0 ? '计算机相关专业' : '理工科背景') . '，有相关项目经验优先。',
                'welfare' => $i % 5 !== 0 ? '五险一金、带薪年假、弹性工作' : null,
                'status' => $i <= 28 ? 'active' : 'inactive',
            ]);
        }

        $activeJobs = Job::where('status', 'active')->get();

        // === Applications (100) ===
        $existingPairs = [];
        for ($i = 0; $i < 100; $i++) {
            $student = $students->random();
            $job = $activeJobs->random();

            $pairKey = $job->id . '-' . $student->id;
            if (in_array($pairKey, $existingPairs)) {
                continue; // Skip duplicates
            }
            $existingPairs[] = $pairKey;

            $statuses = ['pending', 'pending', 'pending', 'reviewed', 'reviewed', 'interviewed', 'interviewed', 'accepted', 'rejected', 'rejected'];
            Application::create([
                'job_id' => $job->id,
                'student_id' => $student->id,
                'status' => $statuses[$i % 10],
                'remark' => $i % 3 === 0 ? '面试安排在' . ['下周一', '下周三', '下周五'][$i % 3] : null,
            ]);
        }

        $this->command->info('Seed complete: 3 schools, ' . $colleges->count() . ' colleges, 10 enterprises, 50 students, 30 jobs, ' . count($existingPairs) . ' applications');
    }
}
