<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\Interview;
use App\Models\Job;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InterviewTest extends TestCase
{
    private $stuUser, $student, $entUser, $enterprise, $job, $application;

    protected function setUp(): void
    {
        parent::setUp();

        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $this->stuUser = User::create(['role' => 'student', 'username' => 'stu_iv', 'name' => '学生', 'phone' => '13800000700', 'email' => 'stuiv@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->student = Student::create(['user_id' => $this->stuUser->id, 'student_no' => '20240001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $college->id]);

        $this->entUser = User::create(['role' => 'enterprise', 'username' => 'ent_iv', 'name' => '企业', 'phone' => '13800000701', 'email' => 'entiv@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->enterprise = Enterprise::create(['user_id' => $this->entUser->id, 'name' => '面试公司', 'credit_code' => '91110000700', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000700', 'email' => 'entiv@test.com', 'status' => 'approved']);
        $this->job = Job::create(['enterprise_id' => $this->enterprise->id, 'title' => '面位岗位', 'count' => 1, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);
        $this->application = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);
    }

    // === Enterprise: create interview ===

    public function test_enterprise_can_create_interview()
    {
        $response = $this->actingAs($this->entUser)->postJson("/api/my/jobs/{$this->job->id}/applications/{$this->application->id}/interview", [
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => '会议室A',
            'type' => 'on-site',
            'contact' => 'HR-张',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200, 'message' => '面试邀请已发送']);

        $this->assertDatabaseHas('interviews', ['application_id' => $this->application->id]);
        // Application status should auto-update to interviewed
        $this->assertEquals('interviewed', $this->application->fresh()->status);
    }

    public function test_create_interview_auto_sets_application_status()
    {
        $this->assertEquals('pending', $this->application->fresh()->status);

        $this->actingAs($this->entUser)->postJson("/api/my/jobs/{$this->job->id}/applications/{$this->application->id}/interview", [
            'scheduled_at' => '2026-07-03 14:00:00',
            'location' => '腾讯会议: 123-456-789',
            'type' => 'online',
        ]);

        $this->assertEquals('interviewed', $this->application->fresh()->status);
    }

    // === Student: respond ===

    public function test_student_can_accept_interview()
    {
        $interview = Interview::create([
            'application_id' => $this->application->id,
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => '会议室A',
            'type' => 'on-site',
        ]);

        $response = $this->actingAs($this->stuUser)->putJson("/api/my/interviews/{$interview->id}/respond", [
            'action' => 'accept',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('accepted', $interview->fresh()->status);
    }

    public function test_student_can_decline_interview()
    {
        $interview = Interview::create([
            'application_id' => $this->application->id,
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => '会议室A',
            'type' => 'on-site',
        ]);

        $this->actingAs($this->stuUser)->putJson("/api/my/interviews/{$interview->id}/respond", [
            'action' => 'decline',
        ])->assertStatus(200);

        $this->assertEquals('declined', $interview->fresh()->status);
    }

    // === Student: my interviews ===

    public function test_student_can_view_own_interviews()
    {
        Interview::create([
            'application_id' => $this->application->id,
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => '会议室A',
            'type' => 'on-site',
        ]);

        $response = $this->actingAs($this->stuUser)->getJson('/api/my/interviews');

        $response->assertStatus(200)
            ->assertJson(['code' => 200]);
        $this->assertEquals(1, $response->json('data.meta.total'));
    }

    // === Edge cases ===

    public function test_enterprise_cannot_create_interview_for_other_enterprise_job()
    {
        $otherUser = User::create(['role' => 'enterprise', 'username' => 'other_iv', 'name' => 'Other', 'phone' => '13800000702', 'email' => 'otheriv@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $otherEnt = Enterprise::create(['user_id' => $otherUser->id, 'name' => 'Other Corp', 'credit_code' => '91110000701', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000701', 'email' => 'otheriv@test.com', 'status' => 'approved']);

        $response = $this->actingAs($otherUser)->postJson("/api/my/jobs/{$this->job->id}/applications/{$this->application->id}/interview", [
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => 'Room',
            'type' => 'on-site',
        ]);

        $response->assertStatus(404);
    }

    public function test_student_cannot_respond_to_other_student_interview()
    {
        $interview = Interview::create([
            'application_id' => $this->application->id,
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => '会议室A',
            'type' => 'on-site',
        ]);

        $otherUser = User::create(['role' => 'student', 'username' => 'other_stu', 'name' => 'Other', 'phone' => '13800000703', 'email' => 'otherstu@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $school = School::first();
        $college = College::first();
        Student::create(['user_id' => $otherUser->id, 'student_no' => '20240002', 'class_name' => '计科2班', 'grade' => '2024', 'college_id' => $college->id]);

        $response = $this->actingAs($otherUser)->putJson("/api/my/interviews/{$interview->id}/respond", [
            'action' => 'accept',
        ]);

        // Returns 404 because whereHas filters out interviews not belonging to this student
        $response->assertStatus(404);
    }

    public function test_status_label_returns_chinese()
    {
        $interview = Interview::create([
            'application_id' => $this->application->id,
            'scheduled_at' => '2026-07-01 10:00:00',
            'location' => '会议室',
            'type' => 'on-site',
            'status' => 'invited',
        ]);

        $this->assertEquals('待确认', $interview->statusLabel());

        $interview->fill(['status' => 'accepted'])->save();
        $this->assertEquals('已接受', $interview->fresh()->statusLabel());
    }
}
