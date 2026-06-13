# 生产部署检查清单

## 环境配置

- [x] `APP_DEBUG=false` — 已设置（.env）
- [x] `APP_KEY` 已生成
- [ ] HTTPS 证书 — 部署时配置
- [ ] 数据库密码 — 修改 root/root 为强密码
- [ ] `.env` 不提交到版本控制

## 安全头

- [x] `SecurityHeaders` 中间件已注册到 web 组
- [x] CSP、X-Frame-Options、X-Content-Type-Options、Referrer-Policy、Permissions-Policy
- [x] HSTS（仅在 HTTPS 请求时发送）
- [ ] 生产环境取消 .htaccess 中 HTTPS 强制跳转注释

## 定时任务

- [x] `jobs:deactivate-expired` 每天执行
- [ ] 配置 crontab: `* * * * * cd /path && php artisan schedule:run >> storage/logs/scheduler.log 2>&1`

## .htaccess

- [ ] 取消 HTTPS 强制跳转注释（`RewriteCond %{HTTPS} off`）
- [x] 禁止访问 .env、.json、.lock、.md 等敏感文件
- [x] 禁止目录浏览（Options -Indexes）

## 安全扫描结果

- [x] 无 SQL 注入：无 DB::raw/whereRaw/selectRaw 拼接用户输入
- [x] 无硬编码密钥：所有密码通过 Hash::make() 处理
- [x] CSRF 保护：所有 Blade 表单使用 @csrf，AJAX 使用 X-CSRF-TOKEN
- [x] 输入验证：所有控制器使用 $request->validate()
- [x] 文件上传安全：白名单 MIME + 大小限制 + 随机文件名
- [x] 密码策略：bcrypt，最小 6 位

## 测试

- [x] 67 tests, 134 assertions, 全部通过
- [ ] CI/CD pipeline 配置（建议）
- [ ] 覆盖率报告生成（phpunit --coverage-html）
