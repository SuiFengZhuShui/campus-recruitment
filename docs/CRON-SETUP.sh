#!/bin/bash
# 校园招聘平台 — 定时任务配置
# 将以下行添加到 crontab（crontab -e）：
#
# * * * * * cd /path/to/yyy && php artisan schedule:run >> storage/logs/scheduler.log 2>&1
#
# 说明：
# - Laravel scheduler 每分钟执行一次，检查是否有到期任务
# - jobs:deactivate-expired 每天执行一次（Kernel.php 中配置 daily()）
# - 自动将创建超过 30 天的 active 岗位改为 inactive
#
# Windows 任务计划程序：
# 创建基本任务 → 触发器：每天 → 操作：启动程序
# 程序：C:\phpstudy_pro\Extensions\php\php7.3.4nts\php.exe
# 参数：C:\phpstudy_pro\WWW\yyy\artisan schedule:run
# 起始于：C:\phpstudy_pro\WWW\yyy

echo "Cron 配置说明已保存。"
echo "Linux/macOS: crontab -e 添加上面的 cron 行"
echo "Windows: 使用任务计划程序配置"
