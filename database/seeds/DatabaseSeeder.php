<?php

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\Interview;
use App\Models\Job;
use App\Models\Offer;
use App\Models\School;
use App\Models\EnterpriseDoc;
use App\Models\Student;
use App\Models\StudentIdRule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Dev: safe re-run — truncate dependent tables first
        Offer::query()->delete();
        Interview::query()->delete();
        Application::query()->delete();
        Job::query()->delete();
        EnterpriseDoc::query()->delete();
        Enterprise::query()->delete();
        Student::query()->delete();
        StudentIdRule::query()->delete();
        User::where('role', '!=', 'school')->delete();
        College::query()->delete();
        School::query()->delete();

        $this->call(SchoolSeeder::class);

        $colleges = College::all();
        $school = School::first();

        // === Student ID Rules ===
        $ruleCount = 0;
        foreach ($colleges as $college) {
            $year = ['2022', '2023', '2024'][$ruleCount % 3];
            StudentIdRule::create([
                'school_id' => $school->id,
                'prefix' => $year,
                'college_id' => $college->id,
            ]);
            $ruleCount++;
        }

        // === Enterprises (24, 每学院3个) ===
        $entNames = [
            ['星辰科技有限公司', '远航数据集团', '云帆信息技术'],
            ['智造工场科技', '博联自动化', '精工精密仪器'],
            ['创想电商有限公司', '速达物流集团', '海通供应链'],
            ['蓝鲸设计工作室', '锐意品牌策划', '光合传媒文化'],
            ['华建集团', '绿城置业', '中铁建设'],
            ['天眼科技', '量子云计算', '数联信息'],
            ['寰宇外贸有限公司', '跨境通商', '丝路国际物流'],
            ['阳光教育科技', '优学派教育', '启迪未来教育'],
        ];
        $entIndustries = ['IT', '智能制造', '电商/物流', '设计/传媒', '建筑', 'IT', '外贸', '教育'];
        $allIndustries = ['IT', '智能制造', '电商', '物流', '设计', '传媒', '建筑', '外贸', '教育', '金融', '医疗', '新能源'];
        $scales = ['1-50人', '50-200人', '200-500人', '500-1000人', '1000人以上'];
        $statuses = ['approved', 'approved', 'approved', 'approved', 'pending', 'rejected'];
        $entId = 1;
        foreach ($colleges as $idx => $college) {
            foreach ([0, 1, 2] as $sub) {
                $name = $entNames[$idx][$sub];
                $industry = $sub === 0 ? $entIndustries[$idx] : $allIndustries[array_rand($allIndustries)];
                $status = $idx < 6 ? $statuses[($idx * 3 + $sub) % count($statuses)] : 'approved';

                $entUser = User::create([
                    'role' => 'enterprise',
                    'username' => 'enterprise' . $entId,
                    'name' => $name . 'HR',
                    'phone' => '139000000' . str_pad($entId, 2, '0', STR_PAD_LEFT),
                    'email' => 'enterprise' . $entId . '@test.com',
                    'password' => Hash::make('123456'),
                    'status' => $status === 'approved' ? 'active' : 'active',
                ]);

                Enterprise::create([
                    'user_id' => $entUser->id,
                    'name' => $name,
                    'credit_code' => '911100000000' . str_pad($entId, 4, '0', STR_PAD_LEFT),
                    'industry' => $industry,
                    'scale' => $scales[array_rand($scales)],
                    'intro' => $name . '专注于' . $industry . '领域，是行业内的优秀企业，与学院建立了长期稳定的校企合作关系，每年招收大量实习生和应届毕业生。',
                    'contact_name' => $name . 'HR',
                    'contact_phone' => '139000000' . str_pad($entId, 2, '0', STR_PAD_LEFT),
                    'email' => 'enterprise' . $entId . '@test.com',
                    'college_id' => $college->id,
                    'status' => $status,
                    'audit_remark' => $status === 'rejected' ? '资质材料不完整' : null,
                ]);
                $entId++;
            }
        }

        $approvedEnterprises = Enterprise::where('status', 'approved')->get();

        // === Enterprise Docs (approved enterprises get qualification docs) ===
        $docNum = 0;
        foreach ($approvedEnterprises as $enterprise) {
            $docTypes = [
                ['type' => 'license', 'file_name' => '营业执照.pdf'],
                ['type' => 'id_card', 'file_name' => '身份证.pdf'],
                ['type' => 'authorization', 'file_name' => '授权书.pdf'],
            ];
            foreach ($docTypes as $dt) {
                EnterpriseDoc::create([
                    'enterprise_id' => $enterprise->id,
                    'type' => $dt['type'],
                    'file_path' => 'enterprises/' . $enterprise->id . '/docs/seed_' . uniqid() . '_' . $dt['file_name'],
                    'file_name' => $dt['file_name'],
                    'status' => 'approved',
                ]);
                $docNum++;
            }
        }

        // === Students (32, 每学院4个) ===
        $stuNames = [
            ['张三', '李四', '陈晓', '林悦'],
            ['王五', '赵六', '刘洋', '黄婷'],
            ['陈七', '周八', '杨帆', '吴敏'],
            ['吴九', '郑十', '孙磊', '何静'],
            ['钱一', '孙二', '马超', '谢琳'],
            ['刘三', '黄四', '朱峰', '韩雪'],
            ['林五', '何六', '郑刚', '邓丽'],
            ['罗七', '梁八', '唐亮', '许芳'],
        ];
        $majors = ['软件技术', '计算机应用', '大数据技术', '人工智能', '电子信息', '现代通信', '网络工程', '物联网'];
        $years = ['2024', '2023', '2024', '2023', '2024', '2023', '2024', '2022'];
        $classNames = ['实验班', '卓越班', '1班', '2班'];

        $studentUsers = [];
        foreach ($colleges as $idx => $college) {
            for ($sub = 0; $sub < 4; $sub++) {
                $i = $idx * 4 + $sub + 1;
                $year = ['2024', '2023', '2024', '2023'][$sub];
                $stuUser = User::create([
                    'role' => 'student',
                    'username' => 'student' . $i,
                    'name' => $stuNames[$idx][$sub],
                    'phone' => '138000000' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'email' => 'student' . $i . '@test.com',
                    'password' => Hash::make('123456'),
                    'college_id' => $college->id,
                    'status' => 'active',
                ]);

                Student::create([
                    'user_id' => $stuUser->id,
                    'student_no' => $year . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'class_name' => $year . $majors[$idx] . $classNames[$sub],
                    'grade' => $year,
                    'college_id' => $college->id,
                ]);
            }
        }

        $students = Student::all();

        // === Jobs (每 approved 企业 3-4 个岗位) ===
        $titles = [
            '前端开发工程师', '后端开发工程师', '测试工程师', '产品经理', 'UI设计师',
            '数据分析师', '运维工程师', '算法工程师', 'Android开发工程师', 'iOS开发工程师',
            '嵌入式开发工程师', '网络安全工程师', '技术支持工程师', '项目经理', '产品运营专员',
            '新媒体运营', '市场推广专员', '销售工程师', '机械设计工程师', '电气工程师',
            '自动化工程师', '质量工程师', '采购专员', '外贸业务员', '物流管理专员',
            '平面设计师', '室内设计师', '视频剪辑师', '会计', '行政专员',
        ];
        $cities = ['南宁', '柳州', '桂林', '广州', '深圳', '杭州', '成都', '北京', '上海', '武汉'];
        $jobMajors = ['软件技术', '计算机应用', '大数据技术', '人工智能', '电子信息', '现代通信', '网络工程', '物联网', '不限'];
        $skillSets = [
            'Vue.js,React,TypeScript,CSS,HTML5',
            'Java,Spring Boot,MySQL,Redis,RabbitMQ',
            'Python,Selenium,Postman,Jira,自动化测试',
            'Axure,Figma,SQL,用户研究,数据分析',
            'Figma,Sketch,PS,AI,品牌设计',
            'Python,SQL,Tableau,PowerBI,数据建模',
            'Linux,Docker,K8s,Nginx,Shell',
            'Python,TensorFlow,PyTorch,CV/NLP',
            'Java,Kotlin,Android SDK,RxJava',
            'Swift,Objective-C,Xcode,CocoaPods',
            'C/C++,ARM,RTOS,嵌入式Linux',
            'Wireshark,Metasploit,安全审计,渗透测试',
            'Linux,Windows Server,网络配置,SQL',
            'Scrum,Jira,风险管理,跨团队协作',
            '用户运营,活动策划,数据分析,文案写作',
            '公众号运营,短视频策划,SEO优化',
            '市场调研,竞品分析,渠道拓展,活动执行',
            '大客户开发,商务谈判,CRM管理',
            'SolidWorks,ProE,机械制图,公差分析',
            'PLC,电气图纸,西门子,三菱,变频器',
            'PLC,SCADA,工业机器人,视觉系统',
            'ISO9001,SPC,FMEA,APQP,MSA',
            '供应链管理,ERP,SAP,供应商评估',
            '英语六级,国际贸易,信用证,报关单',
            'WMS,TMS,供应链规划,数据分析',
            'PS,AI,InDesign,海报设计,画册设计',
            '3DMax,CAD,SketchUp,V-Ray,效果图',
            'Premiere,After Effects,达芬奇,摄影',
            '用友,金蝶,税法,财务报表',
            '办公软件,会议室管理,档案管理,接待',
        ];

        $jobCount = 0;
        foreach ($approvedEnterprises as $enterprise) {
            $jobPerEnt = rand(3, 5);
            for ($k = 0; $k < $jobPerEnt; $k++) {
                $idx = $jobCount % count($titles);
                $city = $cities[array_rand($cities)];
                $min = [4000, 5000, 3500, 6000, 4000, 5000, 4500, 8000, 5000, 6000,
                        4500, 6000, 4000, 7000, 4000, 3500, 4000, 5000, 4500, 5000,
                        5000, 4000, 4000, 4500, 4000, 4000, 4000, 4500, 4000, 3500][$idx];
                $max = $min + [3000, 5000, 3000, 5000, 3000, 4000, 3000, 7000, 4000, 5000,
                                3000, 4000, 2000, 5000, 2000, 2000, 2000, 3000, 3000, 3000,
                                3000, 2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000, 2000][$idx];

                Job::create([
                    'enterprise_id' => $enterprise->id,
                    'title' => $titles[$idx],
                    'count' => rand(1, 5),
                    'city' => $city,
                    'salary_min' => $min,
                    'salary_max' => $max,
                    'education' => ['大专及以上', '本科及以上', '不限'][rand(0, 2)],
                    'type' => ['full-time', 'full-time', 'full-time', 'internship', 'internship'][rand(0, 4)],
                    'major' => $jobMajors[array_rand($jobMajors)],
                    'skills' => $skillSets[$idx],
                    'duty' => '1. 负责' . $titles[$idx] . '相关模块的日常开发与维护；' . "\n"
                            . '2. 参与需求评审与技术方案设计，输出高质量交付；' . "\n"
                            . '3. 与产品、测试团队紧密协作，保障项目进度与质量。',
                    'requirement' => '1. 计算机、软件或相关专业，基础扎实；' . "\n"
                                   . '2. 有实际项目经验或竞赛经历者优先；' . "\n"
                                   . '3. 具备良好的沟通能力和团队合作精神。',
                    'welfare' => '五险一金、带薪年假、节日福利、定期团建、年度体检',
                    'status' => $k < $jobPerEnt - 1 ? 'active' : (rand(0, 3) ? 'active' : 'inactive'),
                ]);
                $jobCount++;
            }
        }

        $activeJobs = Job::where('status', 'active')->get();

        // === Applications (80) ===
        $existingPairs = [];
        $appCount = 0;
        $statusPool = ['pending', 'pending', 'pending', 'pending', 'reviewed', 'reviewed', 'reviewed', 'interviewed', 'interviewed', 'accepted', 'rejected', 'rejected'];
        for ($attempt = 0; $attempt < 200 && $appCount < 80; $attempt++) {
            $student = $students->random();
            $job = $activeJobs->random();

            $pairKey = $job->id . '-' . $student->id;
            if (in_array($pairKey, $existingPairs)) {
                continue;
            }
            $existingPairs[] = $pairKey;

            Application::create([
                'job_id' => $job->id,
                'student_id' => $student->id,
                'status' => $statusPool[$appCount % 12],
                'remark' => $appCount % 4 === 0 ? '期望尽快安排面试' : '',
            ]);
            $appCount++;
        }

        // === Interviews (for interviewed/accsepted applications) ===
        $interviewedApps = Application::whereIn('status', ['interviewed', 'accepted'])->get();
        $intCount = 0;
        foreach ($interviewedApps as $app) {
            $interview = Interview::create([
                'application_id' => $app->id,
                'scheduled_at' => now()->addDays(rand(1, 14))->setTime(9, 0),
                'location' => ['行政楼302会议室', '线上-腾讯会议', '教学楼101', '就业指导中心'][$intCount % 4],
                'type' => ['on-site', 'online'][$intCount % 2],
                'contact' => '王经理 1390000000' . ($intCount + 1),
                'note' => '请携带简历' . ($intCount % 2 ? '和作品集' : ''),
                'status' => $app->status === 'accepted' ? 'accepted' : ['invited', 'accepted'][$intCount % 2],
            ]);
            $intCount++;
        }

        // === Offers (for accepted applications) ===
        $acceptedApps = Application::where('status', 'accepted')->get();
        $ofCount = 0;
        foreach ($acceptedApps as $app) {
            Offer::create([
                'application_id' => $app->id,
                'position' => $app->job->title,
                'salary' => ($app->job->salary_min / 1000) . 'K-' . ($app->job->salary_max / 1000) . 'K，14薪',
                'start_date' => now()->addDays(30 + $ofCount * 7)->format('Y-m-d'),
                'note' => '报到地址：' . $app->job->city . '市高新区科技园',
                'status' => ['sent', 'accepted'][$ofCount % 2],
            ]);
            $ofCount++;
        }

        $totalEnts = Enterprise::count();
        $totalStudents = Student::count();
        $totalJobs = Job::count();
        $totalApps = Application::count();
        $totalInterviews = Interview::count();
        $totalOffers = Offer::count();
        $this->command->info("Seed complete: 1 school, {$colleges->count()} colleges, {$totalEnts} enterprises, {$totalStudents} students, {$totalJobs} jobs, {$totalApps} applications, {$totalInterviews} interviews, {$totalOffers} offers");
    }
}
