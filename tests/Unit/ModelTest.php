<?php

namespace Tests\Unit;

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\EnterpriseDoc;
use App\Models\Job;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentIdRule;
use App\Models\User;
use Tests\TestCase;

class ModelTest extends TestCase
{
    // === User Model ===

    public function test_user_role_checks()
    {
        $school = User::create(['role' => 'school', 'name' => 'Admin', 'phone' => '13800000001', 'password' => bcrypt('pass'), 'status' => 'active']);
        $college = User::create(['role' => 'college', 'name' => 'College', 'phone' => '13800000002', 'password' => bcrypt('pass'), 'status' => 'active']);
        $enterprise = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000003', 'password' => bcrypt('pass'), 'status' => 'active']);
        $student = User::create(['role' => 'student', 'name' => 'Stu', 'phone' => '13800000004', 'password' => bcrypt('pass'), 'status' => 'active']);

        $this->assertTrue($school->isSchool());
        $this->assertFalse($school->isEnterprise());
        $this->assertFalse($school->isStudent());

        $this->assertTrue($college->isCollege());
        $this->assertTrue($enterprise->isEnterprise());
        $this->assertTrue($student->isStudent());
    }

    public function test_user_password_is_hashed()
    {
        $user = User::create(['role' => 'student', 'name' => 'Test', 'phone' => '13800000005', 'password' => bcrypt('secret'), 'status' => 'active']);

        $this->assertNotEquals('secret', $user->password);
        $this->assertTrue(password_verify('secret', $user->password));
    }

    public function test_user_hidden_fields()
    {
        $user = User::create(['role' => 'student', 'name' => 'Test', 'phone' => '13800000006', 'password' => bcrypt('secret'), 'status' => 'active']);
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    // === Enterprise Model ===

    public function test_enterprise_belongs_to_user()
    {
        $user = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000007', 'password' => bcrypt('pass'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $user->id, 'name' => 'Test Corp', 'credit_code' => '91110000', 'industry' => 'IT', 'contact_name' => 'John', 'contact_phone' => '13900000001', 'email' => 'a@b.com', 'status' => 'pending']);

        $this->assertEquals($user->id, $enterprise->user->id);
        $this->assertEquals('Test Corp', $enterprise->user->enterprise->name);
    }

    public function test_enterprise_status_checks()
    {
        $user1 = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000008', 'password' => bcrypt('pass'), 'status' => 'active']);
        $pending = Enterprise::create(['user_id' => $user1->id, 'name' => 'P Corp', 'credit_code' => '91110001', 'industry' => 'IT', 'contact_name' => 'A', 'contact_phone' => '13900000002', 'email' => 'a1@b.com', 'status' => 'pending']);

        $user2 = User::create(['role' => 'enterprise', 'name' => 'Biz2', 'phone' => '13800000009', 'password' => bcrypt('pass'), 'status' => 'active']);
        $approved = Enterprise::create(['user_id' => $user2->id, 'name' => 'A Corp', 'credit_code' => '91110002', 'industry' => 'IT', 'contact_name' => 'B', 'contact_phone' => '13900000003', 'email' => 'a2@b.com', 'status' => 'approved']);

        $this->assertFalse($pending->isApproved());
        $this->assertTrue($approved->isApproved());
    }

    public function test_enterprise_has_many_docs()
    {
        $user = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000010', 'password' => bcrypt('pass'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $user->id, 'name' => 'Corp', 'credit_code' => '91110003', 'industry' => 'IT', 'contact_name' => 'C', 'contact_phone' => '13900000004', 'email' => 'a3@b.com', 'status' => 'pending']);

        EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'license', 'file_path' => '/path/a', 'file_name' => 'a.pdf', 'status' => 'pending']);
        EnterpriseDoc::create(['enterprise_id' => $enterprise->id, 'type' => 'id_card', 'file_path' => '/path/b', 'file_name' => 'b.pdf', 'status' => 'pending']);

        $this->assertCount(2, $enterprise->docs);
    }

    public function test_enterprise_has_many_jobs()
    {
        $user = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000011', 'password' => bcrypt('pass'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $user->id, 'name' => 'Corp', 'credit_code' => '91110004', 'industry' => 'IT', 'contact_name' => 'D', 'contact_phone' => '13900000005', 'email' => 'a4@b.com', 'status' => 'approved']);

        Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Dev', 'count' => 3, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);
        Job::create(['enterprise_id' => $enterprise->id, 'title' => 'QA', 'count' => 1, 'city' => '上海', 'salary_min' => 8000, 'salary_max' => 12000, 'education' => '大专', 'type' => 'internship', 'duty' => 'Test', 'requirement' => 'Manual', 'status' => 'active']);

        $this->assertCount(2, $enterprise->jobs);
    }

    // === Student Model ===

    public function test_student_belongs_to_user()
    {
        $user = User::create(['role' => 'student', 'name' => 'Stu', 'phone' => '13800000012', 'password' => bcrypt('pass'), 'status' => 'active']);
        $school = School::create(['name' => '北京大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);
        $student = Student::create(['user_id' => $user->id, 'student_no' => '2024001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $college->id]);

        $this->assertEquals($user->id, $student->user->id);
        $this->assertEquals('计科1班', $student->user->student->class_name);
    }

    // === Job Model ===

    public function test_job_belongs_to_enterprise()
    {
        $user = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000013', 'password' => bcrypt('pass'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $user->id, 'name' => 'Corp', 'credit_code' => '91110005', 'industry' => 'IT', 'contact_name' => 'E', 'contact_phone' => '13900000006', 'email' => 'a5@b.com', 'status' => 'approved']);
        $job = Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Dev', 'count' => 3, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);

        $this->assertEquals($enterprise->id, $job->enterprise->id);
        $this->assertEquals('Corp', $job->enterprise->name);
    }

    // === Application Model ===

    public function test_application_belongs_to_job_and_student()
    {
        $school = School::create(['name' => '清华']);
        $college = College::create(['school_id' => $school->id, 'name' => '软件学院']);

        $studentUser = User::create(['role' => 'student', 'name' => 'Stu', 'phone' => '13800000014', 'password' => bcrypt('pass'), 'status' => 'active']);
        $student = Student::create(['user_id' => $studentUser->id, 'student_no' => '2024002', 'class_name' => '软工1班', 'grade' => '2024', 'college_id' => $college->id]);

        $entUser = User::create(['role' => 'enterprise', 'name' => 'Biz', 'phone' => '13800000015', 'password' => bcrypt('pass'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => 'Corp', 'credit_code' => '91110006', 'industry' => 'IT', 'contact_name' => 'F', 'contact_phone' => '13900000007', 'email' => 'a6@b.com', 'status' => 'approved']);
        $job = Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Dev', 'count' => 3, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);

        $application = Application::create(['job_id' => $job->id, 'student_id' => $student->id]);

        $this->assertEquals($job->id, $application->job->id);
        $this->assertEquals($student->id, $application->student->id);
    }

    // === School / College ===

    public function test_school_has_many_colleges()
    {
        $school = School::create(['name' => '浙江大学']);
        College::create(['school_id' => $school->id, 'name' => '计算机学院']);
        College::create(['school_id' => $school->id, 'name' => '数学学院']);

        $this->assertCount(2, $school->colleges);
    }

    public function test_college_belongs_to_school()
    {
        $school = School::create(['name' => '复旦大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '经济学院']);

        $this->assertEquals('复旦大学', $college->school->name);
    }

    // === StudentIdRule ===

    public function test_student_id_rule_belongs_to_school_and_college()
    {
        $school = School::create(['name' => '上海交大']);
        $college = College::create(['school_id' => $school->id, 'name' => '电子信息学院']);
        $rule = StudentIdRule::create(['school_id' => $school->id, 'prefix' => '2024', 'college_id' => $college->id]);

        $this->assertEquals('上海交大', $rule->school->name);
        $this->assertEquals('电子信息学院', $rule->college->name);
    }
}
