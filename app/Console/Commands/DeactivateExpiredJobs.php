<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class DeactivateExpiredJobs extends Command
{
    protected $signature = 'jobs:deactivate-expired';
    protected $description = '自动下架超过30天的岗位';

    public function handle()
    {
        $jobs = Job::where('status', 'active')
            ->where('created_at', '<', now()->subDays(30))
            ->get();

        $count = 0;
        foreach ($jobs as $job) {
            $job->fill(['status' => 'inactive'])->save();
            $count++;
        }

        $this->info("已下架 {$count} 个过期岗位");
    }
}
