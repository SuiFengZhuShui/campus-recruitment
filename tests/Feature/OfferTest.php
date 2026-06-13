<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\College;
use App\Models\Enterprise;
use App\Models\Job;
use App\Models\Offer;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OfferTest extends TestCase
{
    private $stuUser, $student, $entUser, $enterprise, $job, $application;

    protected function setUp(): void
    {
        parent::setUp();

        $school = School::create(['name' => '测试大学']);
        $college = College::create(['school_id' => $school->id, 'name' => '计算机学院']);

        $this->stuUser = User::create(['role' => 'student', 'username' => 'stu_of', 'name' => '学生', 'phone' => '13800000800', 'email' => 'stuof@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->student = Student::create(['user_id' => $this->stuUser->id, 'student_no' => '20240001', 'class_name' => '计科1班', 'grade' => '2024', 'college_id' => $college->id]);

        $this->entUser = User::create(['role' => 'enterprise', 'username' => 'ent_of', 'name' => '企业', 'phone' => '13800000801', 'email' => 'entof@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $this->enterprise = Enterprise::create(['user_id' => $this->entUser->id, 'name' => '录用公司', 'credit_code' => '91110000800', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000800', 'email' => 'entof@test.com', 'status' => 'approved']);
        $this->job = Job::create(['enterprise_id' => $this->enterprise->id, 'title' => '录用岗位', 'count' => 1, 'city' => '北京', 'salary_min' => 10000, 'salary_max' => 20000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Code', 'requirement' => 'PHP', 'status' => 'active']);
        $this->application = Application::create(['job_id' => $this->job->id, 'student_id' => $this->student->id]);
    }

    // === Enterprise: send offer ===

    public function test_enterprise_can_send_offer()
    {
        $response = $this->actingAs($this->entUser)->postJson("/api/my/jobs/{$this->job->id}/applications/{$this->application->id}/offer", [
            'position' => '高级PHP工程师',
            'salary' => '25k-35k × 16薪',
            'start_date' => '2026-08-01',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200, 'message' => '录用通知已发送']);

        $this->assertDatabaseHas('offers', ['position' => '高级PHP工程师']);
        $this->assertEquals('accepted', $this->application->fresh()->status);
    }

    public function test_send_offer_requires_fields()
    {
        $response = $this->actingAs($this->entUser)->postJson("/api/my/jobs/{$this->job->id}/applications/{$this->application->id}/offer", [
            'position' => '',
        ]);

        $response->assertStatus(422);
    }

    // === Student: respond ===

    public function test_student_can_accept_offer()
    {
        $offer = Offer::create([
            'application_id' => $this->application->id,
            'position' => 'PHP工程师',
            'salary' => '20k-30k',
            'start_date' => '2026-08-01',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->stuUser)->putJson("/api/my/offers/{$offer->id}/respond", [
            'action' => 'accept',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('accepted', $offer->fresh()->status);
    }

    public function test_student_can_decline_offer()
    {
        $offer = Offer::create([
            'application_id' => $this->application->id,
            'position' => 'PHP工程师',
            'salary' => '20k-30k',
            'start_date' => '2026-08-01',
            'status' => 'sent',
        ]);

        $this->actingAs($this->stuUser)->putJson("/api/my/offers/{$offer->id}/respond", [
            'action' => 'decline',
        ])->assertStatus(200);

        $this->assertEquals('declined', $offer->fresh()->status);
    }

    // === Student: my offers ===

    public function test_student_can_view_own_offers()
    {
        Offer::create([
            'application_id' => $this->application->id,
            'position' => 'PHP工程师',
            'salary' => '20k-30k',
            'start_date' => '2026-08-01',
            'status' => 'sent',
        ]);

        $response = $this->actingAs($this->stuUser)->getJson('/api/my/offers');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('data.meta.total'));
    }

    // === Notifications ===

    public function test_offer_notification_is_sent_to_student()
    {
        Notification::fake();

        $this->actingAs($this->entUser)->postJson("/api/my/jobs/{$this->job->id}/applications/{$this->application->id}/offer", [
            'position' => 'PHP工程师',
            'salary' => '20k',
            'start_date' => '2026-08-01',
        ]);

        Notification::assertSentTo($this->stuUser, \App\Notifications\OfferSent::class);
    }

    // === Status label ===

    public function test_status_label_returns_chinese()
    {
        $offer = Offer::create([
            'application_id' => $this->application->id,
            'position' => 'PHP工程师',
            'salary' => '20k',
            'start_date' => '2026-08-01',
            'status' => 'sent',
        ]);

        $this->assertEquals('已发送', $offer->statusLabel());

        $offer->fill(['status' => 'accepted'])->save();
        $this->assertEquals('已接受', $offer->fresh()->statusLabel());
    }
}
