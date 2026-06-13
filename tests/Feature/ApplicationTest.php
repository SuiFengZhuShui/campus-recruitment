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

class ApplicationTest extends TestCase
{
    private $student;
    private $studentUser;
    private $enterprise;
    private $enterpriseUser;
    private $job;

    protected function setUp(): void
    {
        parent::setUp();

        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        // Create student
        $this->studentUser = User::create(['role' => 'student', 'username' => 'stu1', 'name' => '张三', 'phone' => '13800000300', 'email' => 'stu1@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->student = Student::create(['user_id' => $this->studentUser->id, 'student_no' => '20240001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $college->id]);

        // Create enterprise
        $this->enterpriseUser = User::create(['role' => 'enterprise', 'username' => 'ent1', 'name' => '企业', 'phone' => '13800000301', 'email' => 'ent1@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->enterprise = Enterprise::create(['user_id' => $this->enterpriseUser->id, 'name' => '测试公司', 'credit_code' => '91110000300', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000300', 'email' => 'ent1@test.com', 'status' => 'approved']);

        // Create job
        $this->job = Job::create(['enterprise_id' => $this->enterprise->id, 'title' => 'PHP Dev', 'count' => 3, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);
    }

    // === Student: apply ===

    public function test_student_can_apply()
    {
        $response = $this->actingAs($this->studentUser)->postJson('/api/jobs/' . $this->job->id . '/apply');

        $response->assertStatus(200)
            ->assertJson(['code' => 200, 'message' => '投递成功']);

        $this->assertDatabaseHas('applications', ['job_id' => $this->job->id, 'student_id' => $this->student->id]);
    }

    public function test_cannot_apply_twice()
    {
        // First application
        Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        // Duplicate attempt
        $response = $this->actingAs($this->studentUser)->postJson('/api/jobs/' . $this->job->id . '/apply');

        $response->assertStatus(422)
            ->assertJson(['message' => '已投递过该岗位']);
    }

    public function test_cannot_apply_if_not_student()
    {
        $response = $this->actingAs($this->enterpriseUser)->postJson('/api/jobs/' . $this->job->id . '/apply');

        $response->assertStatus(401);
    }

    public function test_cannot_apply_inactive_job()
    {
        $this->job->update(['status' => 'inactive']);

        $response = $this->actingAs($this->studentUser)->postJson('/api/jobs/' . $this->job->id . '/apply');

        $response->assertStatus(404);
    }

    // === Student: my applications ===

    public function test_my_applications()
    {
        Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $response = $this->actingAs($this->studentUser)->getJson('/api/my/applications');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.meta.total', 1);
    }

    public function test_my_applications_requires_student()
    {
        $response = $this->actingAs($this->enterpriseUser)->getJson('/api/my/applications');

        $response->assertStatus(401);
    }

    // === Enterprise: job applications ===

    public function test_enterprise_can_view_job_applications()
    {
        Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);

        $response = $this->actingAs($this->enterpriseUser)->getJson('/api/my/jobs/' . $this->job->id . '/applications');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.meta.total', 1);
    }

    // === Student: profile ===

    public function test_student_can_view_profile()
    {
        $response = $this->actingAs($this->studentUser)->getJson('/api/my/profile');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.student_no', '20240001');
    }

    public function test_student_can_update_profile()
    {
        $response = $this->actingAs($this->studentUser)->putJson('/api/my/profile', [
            'name' => '张三丰',
            'class_name' => '计科2班',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => '更新成功']);

        $this->assertDatabaseHas('users', ['id' => $this->studentUser->id, 'name' => '张三丰']);
        $this->assertDatabaseHas('students', ['id' => $this->student->id, 'class_name' => '计科2班']);
    }
}
