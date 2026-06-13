<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JobTest extends TestCase
{
    private function createApprovedEnterprise(): Enterprise
    {
        $user = User::create(['role' => 'enterprise', 'username' => 'ent_owner', 'name' => 'Biz', 'phone' => '13800000100', 'email' => 'ent@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        return Enterprise::create(['user_id' => $user->id, 'name' => 'Test Corp', 'credit_code' => '91110000100', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000100', 'email' => 'ent@test.com', 'status' => 'approved']);
    }

    private function createActiveJob(Enterprise $enterprise, array $overrides = []): Job
    {
        return Job::create(array_merge([
            'enterprise_id' => $enterprise->id,
            'title' => 'PHP Developer',
            'count' => 3,
            'city' => '北京',
            'salary_min' => 10000,
            'salary_max' => 20000,
            'education' => '本科',
            'type' => 'full-time',
            'duty' => 'Write code',
            'requirement' => '3 years PHP',
            'status' => 'active',
        ], $overrides));
    }

    // === Public job listing ===

    public function test_public_jobs_index()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'Dev Job']);

        $response = $this->getJson('/api/jobs');

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.list.0.title', 'Dev Job')
            ->assertJsonPath('data.meta.total', 1);
    }

    public function test_jobs_only_show_active()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'Active Job']);
        Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Inactive Job', 'count' => 1, 'city' => '上海', 'salary_min' => 5000, 'salary_max' => 10000, 'education' => '大专', 'type' => 'internship', 'duty' => 'Test', 'requirement' => 'None', 'status' => 'inactive']);

        $response = $this->getJson('/api/jobs');

        $response->assertJsonPath('data.meta.total', 1);
        $this->assertCount(1, $response->json('data.list'));
    }

    public function test_jobs_filter_by_city()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'BJ Job', 'city' => '北京']);
        $this->createActiveJob($enterprise, ['title' => 'SH Job', 'city' => '上海']);

        $response = $this->getJson('/api/jobs?city=上海');

        $response->assertJsonPath('data.meta.total', 1);
        $this->assertEquals('SH Job', $response->json('data.list.0.title'));
    }

    public function test_jobs_filter_by_keyword()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'Java Dev', 'skills' => 'Java,Spring']);
        $this->createActiveJob($enterprise, ['title' => 'PHP Dev', 'skills' => 'PHP,Laravel']);

        $response = $this->getJson('/api/jobs?keyword=PHP');

        $response->assertJsonPath('data.meta.total', 1);
        $this->assertEquals('PHP Dev', $response->json('data.list.0.title'));
    }

    public function test_jobs_filter_by_type()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'Full-time Job', 'type' => 'full-time']);
        $this->createActiveJob($enterprise, ['title' => 'Intern Job', 'type' => 'internship']);

        $response = $this->getJson('/api/jobs?type=internship');

        $response->assertJsonPath('data.meta.total', 1);
        $this->assertEquals('Intern Job', $response->json('data.list.0.title'));
    }

    public function test_jobs_filter_by_education()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'Master Job', 'education' => '硕士']);
        $this->createActiveJob($enterprise, ['title' => 'Bachelor Job', 'education' => '本科']);

        $response = $this->getJson('/api/jobs?education=硕士');

        $response->assertJsonPath('data.meta.total', 1);
    }

    // === Job detail ===

    public function test_show_active_job()
    {
        $enterprise = $this->createApprovedEnterprise();
        $job = $this->createActiveJob($enterprise);

        $response = $this->getJson('/api/jobs/' . $job->id);

        $response->assertStatus(200)
            ->assertJson(['code' => 200])
            ->assertJsonPath('data.title', 'PHP Developer');
    }

    public function test_show_inactive_job_returns_404()
    {
        $enterprise = $this->createApprovedEnterprise();
        $job = Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Hidden', 'count' => 1, 'city' => '北京', 'salary_min' => 5000, 'salary_max' => 10000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Test', 'requirement' => 'None', 'status' => 'inactive']);

        $response = $this->getJson('/api/jobs/' . $job->id);

        $response->assertStatus(404);
    }

    // === Cities ===

    public function test_cities_list()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['city' => '北京']);
        $this->createActiveJob($enterprise, ['city' => '上海']);
        $this->createActiveJob($enterprise, ['city' => '北京']); // Duplicate

        $response = $this->getJson('/api/jobs/cities');

        $response->assertStatus(200);
        $cities = $response->json('data');
        $this->assertCount(2, $cities);
        $this->assertContains('北京', $cities);
        $this->assertContains('上海', $cities);
    }

    // === Enterprise: my jobs ===

    public function test_my_jobs_requires_auth()
    {
        $response = $this->getJson('/api/my/jobs');
        $response->assertStatus(401);
    }

    public function test_my_jobs_enterprise()
    {
        $enterprise = $this->createApprovedEnterprise();
        $this->createActiveJob($enterprise, ['title' => 'My Job']);

        $response = $this->actingAs($enterprise->user)->getJson('/api/my/jobs');

        $response->assertStatus(200)
            ->assertJsonPath('data.list.0.title', 'My Job');
    }

    public function test_my_jobs_requires_approved_enterprise()
    {
        // Create a pending enterprise
        $user = User::create(['role' => 'enterprise', 'username' => 'pending_ent', 'name' => 'P Biz', 'phone' => '13800000200', 'email' => 'p@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        Enterprise::create(['user_id' => $user->id, 'name' => 'Pending Corp', 'credit_code' => '91110000200', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000200', 'email' => 'p@test.com', 'status' => 'pending']);

        $response = $this->actingAs($user)->getJson('/api/my/jobs');

        $response->assertStatus(403);
    }

    // === Enterprise: create job ===

    public function test_store_job()
    {
        $enterprise = $this->createApprovedEnterprise();

        $response = $this->actingAs($enterprise->user)->postJson('/api/my/jobs', [
            'title' => 'New Job',
            'count' => 5,
            'city' => '深圳',
            'salary_min' => 15000,
            'salary_max' => 30000,
            'education' => '本科',
            'type' => 'full-time',
            'duty' => 'Develop features',
            'requirement' => '5 years experience',
            'skills' => 'PHP,MySQL',
        ]);

        $response->assertStatus(200)
            ->assertJson(['code' => 200, 'message' => '发布成功']);

        $this->assertDatabaseHas('jobs', ['title' => 'New Job', 'enterprise_id' => $enterprise->id]);
    }

    public function test_store_job_validation_fails()
    {
        $enterprise = $this->createApprovedEnterprise();

        $response = $this->actingAs($enterprise->user)->postJson('/api/my/jobs', [
            'title' => '',
        ]);

        $response->assertStatus(422);
    }

    // === Enterprise: update job ===

    public function test_update_job()
    {
        $enterprise = $this->createApprovedEnterprise();
        $job = $this->createActiveJob($enterprise);

        $response = $this->actingAs($enterprise->user)->putJson('/api/my/jobs/' . $job->id, [
            'title' => 'Updated Title',
            'count' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => '更新成功']);

        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'title' => 'Updated Title', 'count' => 10]);
    }

    // === Enterprise: delete job ===

    public function test_delete_job()
    {
        $enterprise = $this->createApprovedEnterprise();
        $job = $this->createActiveJob($enterprise);

        $response = $this->actingAs($enterprise->user)->deleteJson('/api/my/jobs/' . $job->id);

        $response->assertStatus(200)
            ->assertJson(['message' => '已删除']);

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    // === Enterprise: toggle job ===

    public function test_toggle_job_from_active_to_inactive()
    {
        $enterprise = $this->createApprovedEnterprise();
        $job = $this->createActiveJob($enterprise);

        $response = $this->actingAs($enterprise->user)->postJson('/api/my/jobs/' . $job->id . '/toggle');

        $response->assertStatus(200)
            ->assertJson(['message' => '已下架']);

        $this->assertDatabaseHas('jobs', ['id' => $job->id, 'status' => 'inactive']);
    }

    public function test_toggle_job_from_inactive_to_active()
    {
        $enterprise = $this->createApprovedEnterprise();
        $job = Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Toggle Test', 'count' => 1, 'city' => '北京', 'salary_min' => 5000, 'salary_max' => 10000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Test', 'requirement' => 'None', 'status' => 'inactive']);

        $response = $this->actingAs($enterprise->user)->postJson('/api/my/jobs/' . $job->id . '/toggle');

        $response->assertStatus(200)
            ->assertJson(['message' => '已上架']);
    }
}
