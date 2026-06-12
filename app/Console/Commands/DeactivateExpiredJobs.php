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
        $count = Job::where('status', 'active')
            ->where('created_at', '<', now()->subDays(30))
            ->update(['status' => 'inactive']);

        $this->info("已下架 {$count} 个过期岗位");
    }
}
