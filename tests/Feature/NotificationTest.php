<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ApplicationSubmitted;
use App\Notifications\EnterpriseApproved;
use App\Notifications\EnterpriseRejected;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    // === Enterprise Approved Notification ===

    public function test_enterprise_approved_notification_is_sent()
    {
        Notification::fake();

        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $entUser = User::create(['role' => 'enterprise', 'username' => 'notify_ent', 'name' => '通知企业', 'phone' => '13800000600', 'email' => 'ent@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '通知公司', 'credit_code' => '91110000600', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000600', 'email' => 'ent@test.com', 'status' => 'pending']);

        $admin = User::create(['role' => 'school', 'username' => 'admin_n', 'name' => '管理员', 'phone' => '13800000601', 'password' => Hash::make('password'), 'status' => 'active']);

        $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/approve', [
            'college_id' => $college->id,
        ]);

        Notification::assertSentTo($entUser, EnterpriseApproved::class);
    }

    public function test_enterprise_rejected_notification_is_sent()
    {
        Notification::fake();

        $entUser = User::create(['role' => 'enterprise', 'username' => 'reject_ent', 'name' => '驳回企业', 'phone' => '13800000602', 'email' => 'reject@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '驳回公司', 'credit_code' => '91110000601', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000601', 'email' => 'reject@test.com', 'status' => 'pending']);

        $admin = User::create(['role' => 'school', 'username' => 'admin_r', 'name' => '管理员', 'phone' => '13800000603', 'password' => Hash::make('password'), 'status' => 'active']);

        $this->actingAs($admin)->post('/admin/enterprises/' . $enterprise->id . '/reject', [
            'audit_remark' => '资料不全',
        ]);

        Notification::assertSentTo($entUser, EnterpriseRejected::class);
    }

    // === Application Submitted Notification ===

    public function test_application_submitted_notification_is_sent()
    {
        Notification::fake();

        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $entUser = User::create(['role' => 'enterprise', 'username' => 'app_ent', 'name' => '企业', 'phone' => '13800000610', 'email' => 'appent@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '投递公司', 'credit_code' => '91110000610', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000610', 'email' => 'appent@test.com', 'status' => 'approved']);
        $job = Job::create(['enterprise_id' => $enterprise->id, 'title' => '测试岗位', 'count' => 3, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);

        $stuUser = User::create(['role' => 'student', 'username' => 'app_stu', 'name' => '学生', 'phone' => '13800000611', 'email' => 'stu@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $student = Student::create(['user_id' => $stuUser->id, 'student_no' => '20240001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $college->id, 'resume_path' => 'resumes/test_resume.pdf']);

        $this->actingAs($stuUser)->postJson('/api/jobs/' . $job->id . '/apply');

        Notification::assertSentTo($entUser, ApplicationSubmitted::class);
    }

    // === Notification does not fail when mail is log ===

    public function test_notification_does_not_break_with_mail_log_driver()
    {
        Notification::fake();

        $entUser = User::create(['role' => 'enterprise', 'username' => 'mail_ent', 'name' => '邮件企业', 'phone' => '13800000620', 'email' => 'mailent@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $entUser->id, 'name' => '邮件公司', 'credit_code' => '91110000620', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000620', 'email' => 'mailent@test.com', 'status' => 'approved']);

        // Should not throw with Notification::fake()
        $entUser->notify(new EnterpriseApproved($enterprise));

        Notification::assertSentTo($entUser, EnterpriseApproved::class);
    }

    // === Factory Tests ===

    public function test_user_factory_creates_valid_user()
    {
        $user = factory(User::class)->create();
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('active', $user->status);
    }

    public function test_enterprise_factory_creates_valid_enterprise()
    {
        $user = factory(User::class)->create(['role' => 'enterprise']);
        $enterprise = factory(Enterprise::class)->create(['user_id' => $user->id]);
        $this->assertInstanceOf(Enterprise::class, $enterprise);
    }

    public function test_student_factory_creates_valid_student()
    {
        $school = School::create(['name' => '测试']);
        $college = College::create(['school_id' => $school->id, 'name' => '学院']);
        $user = factory(User::class)->create(['role' => 'student']);
        $student = factory(Student::class)->create(['user_id' => $user->id, 'college_id' => $college->id]);
        $this->assertInstanceOf(Student::class, $student);
    }
}
