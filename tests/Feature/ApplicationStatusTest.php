<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApplicationStatusTest extends TestCase
{
    private $studentUser, $student, $enterpriseUser, $enterprise, $job;

    protected function setUp(): void
    {
        parent::setUp();

        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $this->studentUser = User::create(['role' => 'student', 'username' => 'stu1', 'name' => '张三', 'phone' => '13800000400', 'email' => 'stu1@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->student = Student::create(['user_id' => $this->studentUser->id, 'student_no' => '20240001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $college->id]);

        $this->enterpriseUser = User::create(['role' => 'enterprise', 'username' => 'ent1', 'name' => '企业', 'phone' => '13800000401', 'email' => 'ent1@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->enterprise = Enterprise::create(['user_id' => $this->enterpriseUser->id, 'name' => '测试公司', 'credit_code' => '91110000400', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000400', 'email' => 'ent1@test.com', 'status' => 'approved']);

        $this->job = Job::create(['enterprise_id' => $this->enterprise->id, 'title' => 'PHP Dev', 'count' => 3, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);
    }

    // === Status defaults ===

    public function test_new_application_has_pending_status()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $this->assertEquals('pending', $app->fresh()->status);
    }

    public function test_status_label_returns_chinese()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);
        $app = $app->fresh(); // Reload to get DB default status

        $this->assertEquals('待审核', $app->statusLabel());

        $app->fill(['status' => 'accepted'])->save();
        $this->assertEquals('已录用', $app->fresh()->statusLabel());
    }

    // === Enterprise: update status ===

    public function test_enterprise_can_update_to_reviewed()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $response = $this->actingAs($this->enterpriseUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'reviewed',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);

        $this->assertEquals('reviewed', $app->fresh()->status);
    }

    public function test_enterprise_can_update_to_interviewed()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $this->actingAs($this->enterpriseUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'interviewed',
        ])->assertStatus(200);

        $this->assertEquals('interviewed', $app->fresh()->status);
    }

    public function test_enterprise_can_update_to_accepted()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $this->actingAs($this->enterpriseUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'accepted',
        ])->assertStatus(200);

        $this->assertEquals('accepted', $app->fresh()->status);
    }

    public function test_enterprise_can_update_to_rejected()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $this->actingAs($this->enterpriseUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'rejected',
            'remark' => '专业不匹配',
        ])->assertStatus(200);

        $this->assertEquals('rejected', $app->fresh()->status);
        $this->assertEquals('专业不匹配', $app->fresh()->remark);
    }

    // === Edge cases ===

    public function test_cannot_set_invalid_status()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $response = $this->actingAs($this->enterpriseUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'pending', // not allowed for update (only reviewed/interviewed/accepted/rejected)
        ]);

        $response->assertStatus(422);
    }

    public function test_student_cannot_update_status()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $response = $this->actingAs($this->studentUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'reviewed',
        ]);

        $response->assertStatus(401);
    }

    public function test_wrong_enterprise_cannot_update_status()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        // Create another enterprise
        $otherUser = User::create(['role' => 'enterprise', 'username' => 'other', 'name' => 'Other', 'phone' => '13800000402', 'email' => 'other@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $otherEnt = Enterprise::create(['user_id' => $otherUser->id, 'name' => 'Other Corp', 'credit_code' => '91110000401', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000401', 'email' => 'other@test.com', 'status' => 'approved']);

        $response = $this->actingAs($otherUser)->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'reviewed',
        ]);

        // Should fail because the job doesn't belong to other enterprise
        $response->assertStatus(404);
    }

    public function test_unauthenticated_cannot_update_status()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $response = $this->putJson("/api/my/jobs/{$this->job->id}/applications/{$app->id}/status", [
            'status' => 'reviewed',
        ]);

        $response->assertStatus(401);
    }

    public function test_student_sees_status_in_my_applications()
    {
        $app = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id, 'status' => 'reviewed']);

        $response = $this->actingAs($this->studentUser)->getJson('/api/my/applications');

        $response->assertStatus(200);
        $items = $response->json('data.list');
        $this->assertEquals('reviewed', $items[0]['status']);
    }
}
