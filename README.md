<p align="center">
  <img src="public/images/school-logo.svg" width="120" height="120" style="border-radius: 20px;" alt="校园招聘平台">
</p>

<h1 align="center">校园招聘平台</h1>

<p align="center">连接学校、学院、企业与学生的校园招聘系统 · 全流程线上完成</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-%E2%89%A57.3-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP >= 7.3">
  <img src="https://img.shields.io/badge/Laravel-5.8-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel 5.8">
  <img src="https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL 5.7+">
  <img src="https://img.shields.io/badge/Blade-JSON%20API-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Blade + JSON API">
  <img src="https://img.shields.io/badge/License-All%20Rights%20Reserved-lightgrey?style=flat-square" alt="License">
</p>

## 项目简介

校园招聘平台把学校就业办、各学院、招聘企业和学生放在同一个系统里。企业线上入驻并上传资质，学院或学校审核通过后即可发布岗位；学生浏览岗位、投递简历、跟进面试与 Offer；管理员在后台统一管理学院、用户与招聘信息。全流程留痕，不再靠 QQ 群和 Excel 表传递招聘信息。

前台是 Blade 页面 + 原生 JavaScript 调用站内 `/api/*` JSON 接口（会话认证，同源 AJAX），无需 Node 构建步骤。

## 功能简介

- **四角色权限体系** — 学校管理员 / 学院管理员 / 企业 / 学生，由 `CheckRole` 中间件按路由隔离，学院只能看到本院数据。
- **企业入驻与资质审核** — 企业注册后上传营业执照等资质文档，支持逐份审核，也支持一键全部通过，审核通过前只能看到等待页。
- **招聘信息发布与审核** — 企业发布岗位，经学院 / 学校两级审核后对学生可见；创建超过 30 天的岗位由定时任务自动下架。
- **学生求职全流程** — 浏览岗位与宣讲企业、维护在线简历、投递岗位、查看投递进度，并**对面试邀约与 Offer 作出响应**（接受 / 拒绝）。
- **简历库** — 企业可按岗位查看投递学生简历，筛选候选人。
- **学号规则配置** — 管理员自定义学号校验规则，学生注册时自动校验学院与年级归属。
- **图形验证码** — 登录与注册接口使用 GD 库生成的图片验证码，验证码存于 session，防脚本批量提交。
- **组织架构管理** — 学校、学院两级维护，学院管理员账号由学校统一创建与停用。
- **主题定制** — 内置 6 套 CSS 主题（经典红 / 深空 / 暖校园 / 清新绿 / 纯白 / 深紫），后台一键切换整站配色，无需改代码。
- **定时任务** — Laravel Scheduler 每日执行 `jobs:deactivate-expired`，把创建超过 30 天的在招岗位置为下架，配置参考 `docs/CRON-SETUP.sh`。

## 技术栈

| 层 | 技术 | 版本 |
|----|------|------|
| 后端框架 | Laravel | 5.8.* |
| 前台 / 后台 | Blade 模板 + 原生 JavaScript（`fetch` 调用站内 JSON 接口） | — |
| 接口层 | `routes/api.php` — 会话认证（`middleware('web')` + `auth`） | — |
| 样式 | 手写 CSS（`public/css/`，含 tokens / components / 6 套主题） | — |
| 数据库 | MySQL | 5.7+ |
| PHP | — | ≥ 7.3 |

> 前端资源**直接以静态文件放在 `public/css/` 下**，由 Blade 用 `url()` 引用，**不需要 npm 构建**。`resources/js` 是 Laravel 骨架残留（`app.js` 仅 `import './bootstrap'`）。

## 快速体验

> 没有线上演示环境，本地约 5 分钟可跑起来。

### 环境要求

- PHP ≥ 7.3，需开启 `mbstring`、`openssl`、`pdo_mysql`、**`gd`** 扩展（gd 用于生成登录验证码）
- MySQL 5.7+
- Composer

### 安装

```bash
composer install
cp .env.example .env        # 配置数据库连接
php artisan key:generate
php artisan migrate --seed  # 建表并填充测试数据
php artisan serve
```

### 测试账号

| 角色 | 账号 | 密码 |
|------|------|------|
| 学校管理员 | `admin`（手机号 `13700000000`） | `CHANGE_ME` |
| 学院管理员 | `jidian` / `qiche` / `dianxin` / `huanjing` / `caijing` / `maoyi` / `yishu` / `guojiao`（共 8 个） | `CHANGE_ME` |
| 企业 | `enterprise1` 起 | `123456` |
| 学生 | `student1` 起 | `123456` |

> **生产环境务必立即修改以上所有密码。** 学校与学院管理员密码定义在 `database/seeds/SchoolSeeder.php`，企业与学生演示账号定义在 `database/seeds/DatabaseSeeder.php`。

### 定时任务

Laravel 5.8 **没有** `php artisan schedule:work` 命令。本地调试可手动触发：

```bash
php artisan schedule:run          # 跑一次调度器（会自行判断当天该不该执行）
php artisan jobs:deactivate-expired   # 或直接执行目标命令
```

生产环境配 crontab（参考 `docs/CRON-SETUP.sh`，内含 Windows 任务计划程序写法）：

```bash
# 每分钟触发一次调度器
* * * * * cd <project-root> && php artisan schedule:run >> storage/logs/scheduler.log 2>&1
```

## 安装教程

部署要点：**Web 根目录指向项目根目录下的 `public/`**。`.env`、`app/`、`storage/`、`vendor/` 全部在 public 之外，根目录暴露会导致数据库密码泄露。

### 伪静态配置（Nginx）

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/campus-recruitment/public;   # 必须指向 public
    index index.php;

    # 拒绝隐藏文件（保留 ACME 验证目录）
    location ~ /\.(?!well-known).* { deny all; }

    # 拒绝敏感后缀
    location ~* \.(env|log|sql|sqlite|db|bak|old|save|swp|tmp|ini|lock)$ { deny all; }
    location ~* (composer\.(json|lock)|package(-lock)?\.json)$ { deny all; }
    location ~ ^/(vendor|node_modules)/ { deny all; }

    # 企业资质文档存在 local 盘（storage/app/），由控制器读取后输出，
    # 不暴露 public/storage，因此无需 storage:link

    location / { try_files $uri $uri/ /index.php?$query_string; }

    location ~ \.php$ {
        # 按实际 PHP 版本调整 socket 路径
        fastcgi_pass unix:/var/run/php/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 伪静态配置（IIS）

在 `public/` 目录放 `web.config`：

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
  <system.webServer>
    <rewrite>
      <rules>
        <rule name="Laravel" stopProcessing="true">
          <match url="^(.*)$" />
          <conditions logicalGrouping="MatchAll">
            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
          </conditions>
          <action type="Rewrite" url="index.php/{R:1}" />
        </rule>
      </rules>
    </rewrite>
  </system.webServer>
</configuration>
```

### Apache

`public/.htaccess` 已内置，确认 `mod_rewrite` 已开启即可。

### 部署后必做

```bash
php artisan config:cache      # 缓存配置（改 .env 后需 config:clear 再重新 cache）
php artisan route:cache
php artisan view:cache
php artisan migrate --force   # 生产环境迁移需加 --force
```

前端资源是 `public/css/` 下的静态文件，**无需编译步骤**，直接部署即可。

## 项目结构

```
├── app/
│   ├── Models/       # School / College / User / Enterprise / EnterpriseDoc
│   │                 # Job / Application / Interview / Offer / Student / StudentIdRule
│   ├── Http/
│   │   ├── Controllers/Admin/   # 后台控制器（Enterprise/Job/College/User/Theme/StudentIdRule…）
│   │   ├── Controllers/Api/     # 前台 JSON 接口（Auth/Captcha/Job/Application/Interview/Offer…）
│   │   └── Middleware/          # CheckRole 角色校验 / SecurityHeaders 安全响应头
│   └── Console/Commands/        # DeactivateExpiredJobs（jobs:deactivate-expired）
├── database/         # 迁移与种子数据（四角色 seed）
├── public/
│   ├── css/          # 手写样式（tokens / components / layout / themes 6 套主题）
│   └── images/       # 站点图标与 logo
├── routes/           # web.php（前台 + 后台）+ api.php（前台 JSON 接口）
├── resources/views/  # Blade 视图（admin 后台 / frontend 前台 / layouts）
├── tests/            # PHPUnit 测试（devDependencies 另含 Playwright 供 E2E）
├── docs/             # 设计文档、部署检查清单、CRON-SETUP.sh
├── DEVLOG.md         # 开发日志
└── CLAUDE.md         # 项目约定与导航图
```

## 测试

```bash
./vendor/bin/phpunit
```

## 文档

- `校园招聘平台-使用手册.docx` — 用户操作手册
- `docs/DEPLOYMENT-CHECKLIST.md` — 部署前检查清单
- `docs/CRON-SETUP.sh` — 定时任务配置脚本（含 Windows 任务计划程序写法）
- `DEVLOG.md` — 开发日志

## 声明

> 本项目为个人学习与实践作品，著作权归作者所有。
>
> 源码仅供学习、研究与技术交流使用。未经授权，不得用于任何商业用途，包括但不限于搭建线上平台对外经营、提供付费服务、二次销售或整体/部分纳入商业产品。
>
> 使用者应自行遵守所在国家或地区的法律法规。因使用本项目产生的一切后果，由使用者自行承担。
>
> 使用本项目即表示您已充分理解并同意本声明的全部内容。

---

Copyright © 2026 随风逐水。保留所有权利。
