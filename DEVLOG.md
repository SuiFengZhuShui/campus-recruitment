# DEVLOG — 校园招聘平台

## 2026-07-02
### 完成
- 生成《校园招聘平台-使用手册》docx（截图+操作步骤结构）
### 待办
- [ ] 上线前完整测试

## 2026-07-01
### 完成
- .env.example 标准化，配置项补全
- package.json 前端依赖锁定（axios, playwright, concurrently）
### 待办
- [ ] API 文档完善

## 2026-06-24
### 完成
- 全部 27 条项目偏好写入记忆（返回按钮、表单提示、分页、下拉自动提交等 UI 规范）
- .gitignore / .gitattributes 完善
### 待办
- [ ] 移动端适配

## 2026-06-21
### 完成
- 后台配置模块完成（config/ 目录）
- 路由结构调整（web.php + api.php + console.php）
### 待办
- [ ] 权限中间件完善

## 2026-06-17
### 完成
- campus_recruitment.sql 数据库结构定型
- composer 依赖锁定（doctrine/dbal, laravel/framework 5.8.*）
### 待办
- [ ] Seeder 覆盖所有 Model $fillable 字段

## 2026-06-15
### 完成
- 测试流程文档（测试流程.txt）编写
- 端到端测试用例设计（Playwright）
### 待办
- [ ] PHPUnit 测试覆盖率提升

## 2026-06-13
### 完成
- docs/ 目录建立（DEPLOYMENT-CHECKLIST.md, CRON-SETUP.sh）
- phpunit.xml 测试配置
### 待办
- [ ] 部署到生产环境

## 2026-06-12
### 完成
- Laravel 5.8 项目初始化（composer create-project）
- README.md 编写
- artisan 命令行工具配置
### 待办
- [ ] 数据库迁移脚本整理

## 2026-09-13
### 今日完成
- README 重构：版式对齐 + 按代码校正技术栈（原文写「Blade 模板 + Vue.js」，实际未使用 Vue —— 是 Blade + 原生 JavaScript fetch 调站内 `/api/*` JSON 接口）
- 删除不存在的构建步骤（`package.json` 的 `scripts` 为空，样式是 `public/css` 下的静态文件，无 npm 构建）
- 环境要求补 `gd` 扩展（登录验证码用 GD 生成图片）
- seeder 演示密码外置：新增 `DemoPassword` 从 `.env` 读 `SEED_PASSWORD`，不再硬编码明文密码；未配置时抛异常
- `SchoolSeeder::run()` 原 71 行、循环体嵌套 5 层 → 拆为若干私有方法；`DatabaseSeeder` 两处内联 `User::create` 数组抽成方法（纯代码搬移，写入字段未变）
- 新增 `SECURITY.md`：17 条依赖告警的清单、为何无法通过升级依赖消除、已实施的安全措施
- 开启 Dependabot 扫描；关闭 Vue 3 升级 PR 并用 `@dependabot ignore this major version` 登记（uni-app 是 Vue 2 语法，Vue 3 有破坏性变更）

### 待办事项
- [ ] 部署到生产环境
- [ ] 数据库迁移脚本整理
- [ ] 两个 Dependabot PR 待合：nanoid 3.3.12→3.3.19、postcss 8.5.15→8.5.28

### 遇到的问题
- Laravel 5.8 没有 `php artisan schedule:work`（该命令 Laravel 8 才引入）→ README 改为 `schedule:run`
- 企业资质文档存于 `local` 盘、由控制器读取后输出，无需 `storage:link` → README 已更正
- 管理员密码原先硬编码在 `SchoolSeeder.php`（公开仓库里的可用凭据，原值见 git 历史）→ 已外置
