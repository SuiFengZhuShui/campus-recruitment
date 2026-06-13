<?php

namespace Tests\Feature;

use App\Models\Enterprise;
use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CommandTest extends TestCase
{
    public function test_deactivate_expired_jobs()
    {
        $user = User::create(['role' => 'enterprise', 'username' => 'ent_cmd', 'name' => 'Biz', 'phone' => '13800000800', 'email' => 'cmd@test.com', 'password' => Hash::make('password'), 'status' => 'active']);
        $enterprise = Enterprise::create(['user_id' => $user->id, 'name' => 'Cmd Corp', 'credit_code' => '91110000800', 'industry' => 'IT', 'contact_name' => 'CEO', 'contact_phone' => '13900000800', 'email' => 'cmd@test.com', 'status' => 'approved']);

        // Create an old job (32 days ago) — use DB update to bypass Eloquent timestamps
        $oldJob = Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Old Job', 'count' => 1, 'city' => '北京', 'salary_min' => 5000, 'salary_max' => 10000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Test', 'requirement' => 'None', 'status' => 'active']);
        \DB::table('jobs')->where('id', $oldJob->id)->update(['created_at' => Carbon::now()->subDays(32)]);

        // Create a recent job
        $recentJob = Job::create(['enterprise_id' => $enterprise->id, 'title' => 'Recent Job', 'count' => 1, 'city' => '北京', 'salary_min' => 5000, 'salary_max' => 10000, 'education' => '本科', 'type' => 'full-time', 'duty' => 'Test', 'requirement' => 'None', 'status' => 'active']);

        $this->artisan('jobs:deactivate-expired')
            ->assertExitCode(0);

        // Old active job should be deactivated
        $this->assertEquals('inactive', Job::find($oldJob->id)->status);
        // Recent job should stay active
        $this->assertEquals('active', Job::find($recentJob->id)->status);
    }
}
