# CLAUDE.md

校园招聘平台 — Laravel 5.8 + Blade + Vue.js + MySQL 全栈项目。

## 项目路径

```
<project-root>
```

## 技术栈

| 层 | 技术 | 版本 |
|----|------|------|
| 后端框架 | Laravel | 5.8.* |
| PHP | phpStudy 内置 | ^7.3 |
| 前端 | Vue.js（脚本引入式）+ Blade | — |
| 数据库 | MySQL | — |
| 服务器 | Apache（phpStudy） | — |
| 测试 | PHPUnit | ^8.5 |
| 代码索引 | CodeGraph | .codegraph/ |

## 环境

- 本地地址：`http://127.0.0.1`
- phpStudy + Apache 运行
- `.env` 已配置，勿改数据库连接信息
- PHP 格式化（PHP-CS-Fixer / Pint）暂不可用——phpStudy PHP 7.3 版本过低

## 关键规则

### Laravel 版本约束

本项目使用 **Laravel 5.8**，不是最新版。以下规则强制：
- `$fillable` 而非 `$guarded`
- 写操作：`find() + fill() + save()`，禁止 Query Builder 写操作
- 禁 `history.back()` / `window.history.back()`，统一用 `goBack(固定路径)`
- 禁止用 affected rows 判断成功/失败
- `$timestamps = false` 时手动加 `$dates = ['created_at']`

### UI 规则

- 主题色：LZPU 蓝色，所有 UI 元素必须匹配，不允许独立配色
- 返回按钮：所有二级页面必须有返回按钮，用 `goBack()` 智能判断来源
- 表单提示：放 `placeholder` 内，不放输入框下方
- 搜索框清空按钮：× 按钮在输入框右端，有内容才显示
- 分页按钮：用 `button` 标签不用 `a` 标签
- 筛选下拉框：选择即搜索，不需要额外点按钮
- 同类型组件跨页面必须样式一致

### 文档

- 用户说"文本文档""说明" → `.txt`
- 项目结构文档（CLAUDE.md、README.md）→ `.md`
- 生成文档前先列目录结构给用户确认
- 使用手册默认用"页面截图+操作步骤"结构
- "不用太详细"=换表达方式，不是删内容

### Seeder

- Seeder 必须覆盖 Model 所有 `$fillable` 字段

### Office 生成

- Word：Node.js docx，反引号避引号，扁平化嵌套，CDN 图片限制
- 覆盖前先备份：`cp target target.bak.YYYYMMDD`
- docx 生成后检查白缩略图

## 项目记忆

项目记忆位于 `~/.claude/projects/c--Users-Administrator-Desktop-yyy/memory/`，包含 27 条项目偏好。关键条目：
- 返回按钮模式、goBack skip 列表同步
- CSS 作用域检查、跨页面样式一致性
- 下拉框自动提交、分页按钮模式
- 文档生成确认流程

## 文档格式

- 本项目说明文档、行为准则使用 `.txt` 格式
- `doc-file-warning` Hook 对 `.txt` 警告忽略

## 不可变性覆盖

- ECC "不可变性"规则不适用于 Eloquent ORM
- `$model->fill($data)->save()` 是 Laravel 标准惯用法，允许原地修改

## 模型配置

- 本项目使用 DeepSeek V4-Pro（1M token 上下文）
- ECC performance.md 中 Haiku/Sonnet/Opus 建议不适用
- 日常开发：deepseek-v4-pro，简单任务：deepseek-v4-flash

## CodeGraph 与 grep 分工

| 场景 | 工具 |
|------|------|
| 函数定义、调用关系、影响范围 | CodeGraph（AST，无需 grep 验证） |
| 字符串内容、注释、日志 | grep |
| 修改后验证 | grep + 语法检查 |

## 命名规范

| 层面 | 规范 | 示例 |
|------|------|------|
| PHP 类 | PascalCase | `JobController` |
| PHP 方法/变量 | camelCase | `getJobById()` |
| JS 变量/函数 | camelCase | `fetchJobs` |
| JS 常量 | UPPER_SNAKE_CASE | `API_BASE_URL` |
| CSS 类 | kebab-case | `.job-card` |
| 数据库表/字段 | snake_case | `job_listings` |
