# 高校校园招聘平台 — 完整设计文档

> 2026-06-11 · 第一期

---

## 1. 系统概述

高校校园招聘平台。学校（超管）→ 学院（管理员），下辖企业和学生。企业上传资质经学校审核通过后发布招聘信息，学生浏览岗位投递简历，企业可查看/下载学生简历。

**两期开发：**
- 第一期：核心链路（注册→审核→发布→学生浏览投递→简历管理）
- 第二期：消息系统（学院↔学生 + 企业↔学生）+ 高级统计

---

## 2. 技术栈

| 层 | 技术 |
|----|------|
| 后端 | Laravel 12 + PHP 8.3+ |
| 前端 | Vue 3（Composition API）+ Vite + Tailwind CSS |
| 数据库 | MySQL 8.0 / InnoDB / utf8mb4 |
| 认证 | Laravel Sanctum（SPA） |
| 验证码 | PHP GD 库图形验证码（数字+字母随机） |
| 文件存储 | Laravel Storage（本地 disk） |

---

## 3. 角色定义

| 角色 | 权限范围 | 界面 |
|------|----------|------|
| 🏫 学校 | 全权 — 学院CRUD、企业审核（唯一审核方）、企业/学生管理、学号规则、平台设置 | 管理后台 |
| 🏛️ 学院 | 本院数据统计 + 与学生沟通（二期） | 管理后台 |
| 🏢 企业 | 自己的岗位+简历+资料（审核通过才有完整功能） | 企业后台 |
| 🎓 学生 | 简历+浏览岗位+投递 | 学生中心 |

---

## 4. 系统架构

```
┌──────────────────────────────────────────────────┐
│                  统一登录入口                       │
│     (企业/学生/学院/学校 同一页面，角色选择)          │
└────────┬───────────┬───────────┬──────────────────┘
         ↓           ↓           ↓
  ┌──────────┐ ┌──────────┐ ┌───────────────────────┐
  │ 学校后台  │ │ 学院后台  │ │   企业后台 / 学生中心    │
  │/admin/   │ │/admin/   │ │/enterprise / /student │
  │ school   │ │ college  │ │                       │
  └──────────┘ └──────────┘ └───────────────────────┘

前台门户（/+ /jobs）：未登录也可浏览岗位和企业列表
```

---

## 5. 页面路由全景

### 5.1 公共页面（无需登录）
| 路由 | 页面 |
|------|------|
| `/` | 平台首页（介绍 + 数据统计 + 最新岗位） |
| `/jobs` | 岗位搜索列表页（筛选 + 分页） |
| `/jobs/{id}` | 岗位详情页 |
| `/enterprises` | 已审核企业列表 |
| `/enterprises/{id}` | 企业详情页 |
| `/login` | 统一登录页 |
| `/register/student` | 学生注册 |
| `/register/enterprise` | 企业注册 |

### 5.2 学生端 `/student`
| 路由 | 页面 |
|------|------|
| `/student/dashboard` | 个人中心首页 |
| `/student/resume` | 简历上传/替换/预览 |
| `/student/applications` | 我的投递记录 |
| `/student/profile` | 个人信息 + 修改密码 |

### 5.3 企业后台 `/enterprise`
| 路由 | 页面 | 权限 |
|------|------|------|
| `/enterprise/dashboard` | 企业后台首页 | 已审核 |
| `/enterprise/audit-status` | 审核进度 | 全部 |
| `/enterprise/docs/upload` | 上传/补充资质 | 全部 |
| `/enterprise/jobs` | 岗位列表 | 已审核 |
| `/enterprise/jobs/create` | 发布岗位 | 已审核 |
| `/enterprise/jobs/{id}/edit` | 编辑岗位 | 已审核 |
| `/enterprise/jobs/{id}/applications` | 投递学生列表 | 已审核 |
| `/enterprise/resumes` | 浏览学生简历 | 已审核 |
| `/enterprise/profile` | 企业信息 + 修改密码 | 全部 |

### 5.4 学院后台 `/admin/college`
| 路由 | 页面 |
|------|------|
| `/admin/college/dashboard` | 本院数据统计 |

### 5.5 学校后台 `/admin/school`
| 路由 | 页面 |
|------|------|
| `/admin/school/dashboard` | 全平台数据统计 |
| `/admin/school/colleges` | 学院管理（列表 + 新增 + 编辑 + 删除） |
| `/admin/school/enterprises` | 企业列表（全部） |
| `/admin/school/enterprises/{id}/audit` | 企业审核详情 |
| `/admin/school/students` | 学生管理（筛选 + 搜索 + 编辑 + 删除） |
| `/admin/school/admins` | 学院管理员账号管理 |
| `/admin/school/student-id-rules` | 学号匹配规则 |
| `/admin/school/profile` | 修改密码 |

---

## 6. 数据库设计

所有表统一使用 `id` 自增主键 + `created_at` / `updated_at` 时间戳。引擎 InnoDB，字符集 utf8mb4_unicode_ci。

### 6.1 schools
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| name | varchar(100) | 学校名称 |
| created_at / updated_at | timestamp | |

### 6.2 colleges
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| school_id | bigint unsigned | FK → schools.id |
| name | varchar(100) | 学院名称 |
| created_at / updated_at | timestamp | |

### 6.3 users（统一认证表）
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| role | enum('school', 'college', 'enterprise', 'student') | 角色 |
| college_id | bigint unsigned nullable | FK → colleges.id（学院/学生时有值） |
| name | varchar(50) | 显示名称 |
| phone | varchar(20) | 手机号 |
| password | varchar(255) | bcrypt 哈希 |
| status | enum('active', 'disabled') | 默认 active |
| created_at / updated_at | timestamp | |

### 6.4 enterprises
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK → users.id |
| name | varchar(200) | 企业全称 |
| credit_code | varchar(50) | 统一社会信用代码 |
| industry | varchar(50) | 所属行业 |
| scale | varchar(30) nullable | 企业规模 |
| intro | text nullable | 企业简介 |
| contact_name | varchar(30) | 联系人 |
| contact_phone | varchar(20) | 联系人手机 |
| email | varchar(100) | 企业邮箱 |
| college_id | bigint unsigned nullable | FK → colleges.id（学校审核通过时指定） |
| status | enum('pending', 'approved', 'rejected') | 审核状态，默认 pending |
| audit_remark | text nullable | 审核备注（驳回时必填） |
| created_at / updated_at | timestamp | |

### 6.5 enterprise_docs
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| enterprise_id | bigint unsigned | FK → enterprises.id |
| type | enum('license', 'id_card', 'authorization') | 执照/身份证/授权书 |
| file_path | varchar(500) | 存储路径 |
| file_name | varchar(200) | 原始文件名 |
| status | enum('pending', 'approved', 'rejected') | 该项审核结果 |
| reject_reason | varchar(500) nullable | 该项不通过原因 |
| created_at / updated_at | timestamp | |

### 6.6 students
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| user_id | bigint unsigned | FK → users.id |
| student_no | varchar(30) | 学号 |
| class_name | varchar(100) | 班级 |
| grade | varchar(10) | 年级（学号前4位提取） |
| college_id | bigint unsigned | FK → colleges.id（学号自动匹配） |
| resume_path | varchar(500) nullable | 简历文件路径 |
| created_at / updated_at | timestamp | |

### 6.7 jobs
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| enterprise_id | bigint unsigned | FK → enterprises.id |
| title | varchar(200) | 岗位名称 |
| count | int unsigned | 招聘人数 |
| city | varchar(50) | 工作城市 |
| salary_min | int unsigned | 薪资下限 |
| salary_max | int unsigned | 薪资上限 |
| education | varchar(30) | 学历要求 |
| major | varchar(200) nullable | 专业要求 |
| skills | varchar(500) nullable | 技能标签（逗号分隔） |
| type | enum('full-time', 'internship') | 全职/实习 |
| duty | text | 岗位职责 |
| requirement | text | 任职要求 |
| welfare | text nullable | 福利待遇 |
| status | enum('active', 'inactive') | 上架/下架，默认 active |
| created_at / updated_at | timestamp | |

### 6.8 applications
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| job_id | bigint unsigned | FK → jobs.id |
| student_id | bigint unsigned | FK → students.id |
| created_at / updated_at | timestamp | |

### 6.9 student_id_rules
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| school_id | bigint unsigned | FK → schools.id |
| prefix | varchar(20) | 学号前缀 |
| college_id | bigint unsigned | FK → colleges.id |
| created_at / updated_at | timestamp | |

### 6.10 messages（二期）
| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint unsigned | PK |
| from_id | bigint unsigned | 发送者 FK → users.id |
| to_id | bigint unsigned | 接收者 FK → users.id |
| content | text | 消息内容 |
| read_at | timestamp nullable | 阅读时间 |
| created_at / updated_at | timestamp | |

---

## 7. API 设计

统一返回格式：`{"code": 200, "message": "success", "data": {}}`

分页格式：`{"code": 200, "message": "success", "data": {"list": [], "meta": {...}}}`

### 7.1 Auth
```
POST   /api/auth/captcha                         生成图形验证码
POST   /api/auth/register/student                学生注册
POST   /api/auth/register/enterprise             企业注册
POST   /api/auth/login                           统一登录
POST   /api/auth/logout                          退出
GET    /api/auth/me                              当前用户信息
```

### 7.2 公开接口
```
GET    /api/jobs                                 岗位列表（筛选/搜索/分页）
GET    /api/jobs/{id}                            岗位详情
GET    /api/enterprises                          已审核企业列表
GET    /api/enterprises/{id}                     企业详情
```

### 7.3 学生端（auth:sanctum + role:student）
```
POST   /api/student/resume                       上传简历
DELETE /api/student/resume                       删除简历
GET    /api/student/applications                 我的投递
POST   /api/student/jobs/{id}/apply              投递岗位
PUT    /api/student/profile                      更新个人信息
PUT    /api/student/password                     修改密码
```

### 7.4 企业端（auth:sanctum + role:enterprise）
```
GET    /api/enterprise/audit-status              审核状态
POST   /api/enterprise/docs                      上传资质文件
DELETE /api/enterprise/docs/{id}                 删除/替换资质文件

// 以下需 enterprise.status = approved
GET    /api/enterprise/jobs                      岗位列表
POST   /api/enterprise/jobs                      发布岗位
PUT    /api/enterprise/jobs/{id}                 编辑岗位
PATCH  /api/enterprise/jobs/{id}/status          上架/下架
GET    /api/enterprise/jobs/{id}/applications    投递学生列表
GET    /api/enterprise/resumes                   浏览学生简历
GET    /api/enterprise/resumes/{id}/download     下载简历
PUT    /api/enterprise/profile                   更新企业信息
PUT    /api/enterprise/password                  修改密码
```

### 7.5 学院端（auth:sanctum + role:college）
```
GET    /api/admin/college/dashboard              本院数据统计
```

### 7.6 学校端（auth:sanctum + role:school）
```
GET    /api/admin/school/dashboard               全平台统计
GET    /api/admin/school/colleges                学院列表
POST   /api/admin/school/colleges                新增学院
PUT    /api/admin/school/colleges/{id}           编辑学院
DELETE /api/admin/school/colleges/{id}           删除学院
GET    /api/admin/school/enterprises             企业列表
GET    /api/admin/school/enterprises/{id}        企业详情
POST   /api/admin/school/enterprises/{id}/audit  审核企业（通过/驳回）
GET    /api/admin/school/students                学生列表（筛选+搜索）
PUT    /api/admin/school/students/{id}           编辑学生
DELETE /api/admin/school/students/{id}           删除学生
GET    /api/admin/school/admins                  学院管理员列表
POST   /api/admin/school/admins                  新增学院管理员
PUT    /api/admin/school/admins/{id}             编辑学院管理员
DELETE /api/admin/school/admins/{id}             删除学院管理员
GET    /api/admin/school/student-id-rules        规则列表
POST   /api/admin/school/student-id-rules        新增规则
PUT    /api/admin/school/student-id-rules/{id}   编辑规则
DELETE /api/admin/school/student-id-rules/{id}   删除规则
PUT    /api/admin/school/password                修改密码
```

---

## 8. 核心业务流程

### 8.1 企业注册→审核→发布
```
企业注册（填写信息+验证码）
  → 自动登录，status=pending
  → 上传三件套资质（营业执照/身份证/授权书）
  → 提交审核
  → 学校后台查看企业详情 + 预览资质文件
  → 逐项审核（通过/不通过+原因）→ 选择归属学院
  → 三项全部通过 → status=approved → 完整功能开放
  → 任一不通过 → status=rejected → 退回修改
  → 审核通过后可发布岗位
```

### 8.2 学生注册→投递
```
学生注册（学号自动匹配学院+年级 + 验证码）
  → 自动登录
  → 上传简历文件（pdf/doc/docx）
  → 前台浏览岗位列表（筛选：城市/学历/类型 + 搜索 + 分页）
  → 查看岗位详情
  → 点击投递（需登录）
  → 个人中心查看投递记录
```

### 8.3 企业浏览简历
```
企业后台 → 浏览学生简历列表
  → 查看学生基本信息（姓名/学号/班级/年级/学院）
  → 预览简历（PDF 直接显示）
  → 下载简历（doc/docx 直接下载）
```

---

## 9. 注册字段

### 9.1 企业注册
| 字段 | 必填 | 说明 |
|------|------|------|
| 企业名称 | ✅ | |
| 统一社会信用代码 | ✅ | |
| 所属行业 | ✅ | 下拉选择 |
| 企业规模 | 选填 | 下拉 |
| 企业简介 | 选填 | 文本 |
| 联系人姓名 | ✅ | |
| 联系人手机号 | ✅ | |
| 企业邮箱 | ✅ | |
| 登录密码 | ✅ | bcrypt |
| 图形验证码 | ✅ | |

### 9.2 学生注册
| 字段 | 必填 | 说明 |
|------|------|------|
| 姓名 | ✅ | |
| 学号 | ✅ | 自动匹配学院 + 提取年级（前4位） |
| 班级 | ✅ | |
| 手机号 | ✅ | |
| 登录密码 | ✅ | bcrypt |
| 图形验证码 | ✅ | |

---

## 10. 企业资质审核项

| 项目 | 格式 | 大小限制 | 说明 |
|------|------|----------|------|
| 营业执照 | jpg/png/pdf | ≤10MB | 原件彩色扫描/拍照 |
| 经办人身份证（正反面） | jpg/png | ≤10MB | |
| 招聘授权委托书 | jpg/png/pdf | ≤10MB | 盖企业公章 |

审核逻辑：三项全部通过 → 整体通过。任一项不通过 → 整体驳回（逐项标注原因）。

---

## 11. 学号匹配规则

- 学校后台预设 `学号前缀 → 学院` 映射
- 学生注册输入学号，遍历规则按前缀匹配 → 自动归属学院
- 年级从学号前4位提取（如 `20246030348` → `2024` 级）
- 未匹配到任何规则 → 提示联系管理员

---

## 12. 学校后台学生管理筛选

- 按学院筛选（下拉）
- 按年级筛选（下拉，从学号规则自动生成年级列表）
- 按姓名搜索（模糊匹配）
- 支持编辑和删除

---

## 13. 文件上传规范

| 类型 | 路径 | 格式 | 大小 |
|------|------|------|------|
| 企业资质 | `enterprises/{id}/docs/{type}_{ts}.{ext}` | jpg/png/pdf | ≤10MB |
| 学生简历 | `students/{id}/resume_{ts}.{ext}` | pdf/doc/docx | ≤20MB |

- 后端白名单验证 MIME + 扩展名
- 随机文件名防路径遍历
- 企业资质可逐个替换
- 学生简历只保留最新一份

---

## 14. 数据统计

学院后台仪表盘：本院企业数 / 学生数 / 岗位数 / 投递数（纯数字卡片）。

学校后台仪表盘：全平台企业数 / 学生数 / 岗位数 / 投递数 / 学院数（纯数字卡片）。

---

## 15. 中间件策略

| 中间件 | 作用 |
|--------|------|
| `auth:sanctum` | 所有需登录路由 |
| `role:school\|college\|enterprise\|student` | 角色隔离 |
| `enterprise.status:approved` | 企业核心功能仅审核通过可用 |

---

## 16. 实施阶段

### 阶段 0：项目初始化
- `laravel new` 创建项目
- 安装 Sanctum + Vite + Tailwind
- 配置数据库 `.env`
- 创建数据库迁移（10 张表）
- 运行迁移
- 创建学校超管 Seeder
- 创建基础目录结构（Controller / Model / Vue 组件目录）

### 阶段 1：认证系统
- 图形验证码生成（PHP GD）
- 统一登录（四角色）
- 学生注册（学号匹配 + 年级提取）
- 企业注册
- 密码修改

### 阶段 2：学校后台
- 学院 CRUD
- 学号匹配规则 CRUD
- 学生管理（筛选 + 搜索 + 编辑 + 删除）
- 学院管理员账号管理

### 阶段 3：企业审核 + 企业端
- 资质文件上传
- 学校审核详情页 + 审核操作
- 企业审核状态查看
- 岗位 CRUD + 上下架
- 浏览/下载学生简历
- 查看投递学生列表

### 阶段 4：学生端 + 前台门户
- 简历上传/替换/预览
- 前台岗位列表 + 搜索筛选
- 岗位详情 + 投递
- 我的投递记录
- 企业列表/详情页
- 前台首页

### 阶段 5：学院后台
- 本院数据统计面板

---

## 17. 风险

| 风险 | 可能性 | 缓解 |
|------|--------|------|
| 文件上传安全 | 中 | 白名单MIME+扩展名双重校验，随机文件名 |
| 学号匹配错误 | 低 | 学校后台可配置规则，未匹配提示联系管理员 |
| 资质审核纠纷 | 低 | 逐项标注原因，企业可重新提交 |

---

## 18. 一期完整文件清单

```
app/
├── Models/
│   ├── School.php
│   ├── College.php
│   ├── User.php
│   ├── Enterprise.php
│   ├── EnterpriseDoc.php
│   ├── Student.php
│   ├── Job.php
│   ├── Application.php
│   └── StudentIdRule.php
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   └── CaptchaController.php
│   │   ├── Api/
│   │   │   ├── JobController.php
│   │   │   ├── EnterpriseController.php
│   │   │   ├── Student/
│   │   │   │   ├── ResumeController.php
│   │   │   │   ├── ApplicationController.php
│   │   │   │   └── ProfileController.php
│   │   │   ├── Enterprise/
│   │   │   │   ├── AuditController.php
│   │   │   │   ├── DocController.php
│   │   │   │   ├── JobController.php
│   │   │   │   ├── ResumeController.php
│   │   │   │   └── ProfileController.php
│   │   │   └── Admin/
│   │   │       ├── School/
│   │   │       │   ├── DashboardController.php
│   │   │       │   ├── CollegeController.php
│   │   │       │   ├── EnterpriseController.php
│   │   │       │   ├── StudentController.php
│   │   │       │   ├── AdminController.php
│   │   │       │   └── StudentIdRuleController.php
│   │   │       └── College/
│   │   │           └── DashboardController.php
│   │   └── Web/
│   │       └── SpaController.php  // 所有页面返回 Vue SPA
│   └── Middleware/
│       ├── RoleMiddleware.php
│       └── EnterpriseStatusMiddleware.php
├── Services/
│   ├── CaptchaService.php
│   └── StudentIdMatcherService.php
└── database/
    ├── migrations/（10 个迁移文件）
    └── seeders/
        ├── DatabaseSeeder.php
        └── SchoolSeeder.php

resources/
├── js/
│   ├── frontend/
│   │   ├── app.js
│   │   ├── App.vue
│   │   ├── router/
│   │   ├── layouts/
│   │   ├── pages/
│   │   │   ├── Home.vue
│   │   │   ├── JobList.vue
│   │   │   ├── JobDetail.vue
│   │   │   ├── EnterpriseList.vue
│   │   │   ├── EnterpriseDetail.vue
│   │   │   ├── Login.vue
│   │   │   ├── RegisterStudent.vue
│   │   │   ├── RegisterEnterprise.vue
│   │   │   └── student/
│   │   │       ├── Dashboard.vue
│   │   │       ├── Resume.vue
│   │   │       ├── Applications.vue
│   │   │       └── Profile.vue
│   │   └── composables/
│   └── admin/
│       ├── app.js
│       ├── App.vue
│       ├── router/
│       ├── layouts/
│       └── pages/
│           ├── school/
│           │   ├── Dashboard.vue
│           │   ├── CollegeList.vue
│           │   ├── CollegeForm.vue
│           │   ├── EnterpriseList.vue
│           │   ├── EnterpriseAudit.vue
│           │   ├── StudentList.vue
│           │   ├── AdminList.vue
│           │   └── StudentIdRules.vue
│           ├── college/
│           │   └── Dashboard.vue
│           └── enterprise/
│               ├── Dashboard.vue
│               ├── AuditStatus.vue
│               ├── DocUpload.vue
│               ├── JobList.vue
│               ├── JobForm.vue
│               ├── ApplicationList.vue
│               └── ResumeBrowse.vue
├── views/
│   └── spa.blade.php  // Vue SPA 入口（前后台两个入口）
└── routes/
    ├── web.php
    └── api.php
```
