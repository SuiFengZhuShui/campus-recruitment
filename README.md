# 校园招聘平台

连接学校、学院、企业与学生的校园招聘系统：企业发布岗位、学生投递简历、学校与学院审核管理，全流程线上完成。

## 功能特性

- **四角色体系** — 学校管理员 / 学院管理员 / 企业 / 学生，权限分级
- **企业招聘** — 企业注册入驻、发布招聘信息、查看投递学生简历
- **学生求职** — 浏览招聘信息、投递简历、查看投递进度
- **审核流程** — 企业入驻审核、招聘信息审核（学校 / 学院两级）
- **后台管理** — 用户管理、招聘信息管理、主题定制（CSS 主题系统）
- **定时任务** — Laravel Scheduler 处理到期招聘信息（crontab 见 `docs/CRON-SETUP.sh`）

## 技术栈

| 层 | 技术 | 版本 |
|----|------|------|
| 后端框架 | Laravel | 5.8.* |
| 前端 | Blade 模板 + Vue.js | — |
| 数据库 | MySQL | 5.7+ |
| PHP | — | ≥ 7.3 |

## 快速开始

### 环境要求

- PHP ≥ 7.3（含 mbstring、openssl、pdo_mysql 扩展）
- MySQL 5.7+
- Composer
- Node.js + npm（前端资源编译）

### 安装

```bash
composer install
cp .env.example .env        # 配置数据库连接
php artisan key:generate
php artisan migrate --seed  # 建表并填充测试数据
npm install && npm run dev  # 编译前端资源
php artisan serve
```

> 测试账号：学校管理员 `admin`，各学院管理员账号见 seeder 输出（密码 `CHANGE_ME`，生产环境务必修改）

### 定时任务

```bash
# 每分钟执行一次调度器（参考 docs/CRON-SETUP.sh）
* * * * * cd <project-root> && php artisan schedule:run >> storage/logs/scheduler.log 2>&1
```

## 项目结构

```
├── app/              # 模型 / 控制器 / 中间件
├── database/         # 迁移与种子数据（四角色 seed）
├── routes/           # web 路由
├── resources/        # Blade 视图与前端资源
├── tests/            # PHPUnit 测试
├── docs/             # 设计文档与部署检查清单
└── CLAUDE.md         # 项目约定与导航图
```

## 测试

```bash
./vendor/bin/phpunit
```

## 文档

- `校园招聘平台-使用手册.docx` — 用户操作手册
- `docs/DEPLOYMENT-CHECKLIST.md` — 部署前检查清单
- `DEVLOG.md` — 开发日志

## License

Copyright © 2026 随风逐水。保留所有权利。
