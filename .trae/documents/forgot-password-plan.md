# 登录页“忘记密码”功能实施计划

## 1. 摘要

在登录页面增加“忘记密码”入口，允许用户通过注册邮箱接收重置链接并设置新密码。基于 Laravel 内置的 `Password` broker 实现，使用自定义中文邮件通知。

**当前状态**：核心代码已实现并存在于仓库中（路由、控制器、视图、通知、模型钩子、登录页入口）。本计划以**环境校验 + 端到端验证 + 必要微调**为主。

## 2. 现状分析

通过探查已确认以下文件已就绪：

| 模块 | 文件路径 | 状态 | 说明 |
|---|---|---|---|
| 路由 | `routes/web.php` | 已添加 | `/forgot-password`、`/reset-password/{token}` 及其 POST 端点，带 `throttle:5,1` |
| 控制器 | `app/Http/Controllers/AuthController.php` | 已添加 | `showForgotPasswordForm`、`sendResetLinkEmail`、`showResetForm`、`reset` |
| 通知 | `app/Notifications/ResetPasswordNotification.php` | 已创建 | 继承 `ResetPassword`，重写为中文主题/正文 |
| 模型 | `app/Models/User.php` | 已修改 | `sendPasswordResetNotification` 使用自定义通知类 |
| 视图 | `resources/views/auth/forgot-password.blade.php` | 已创建 | 输入邮箱发送重置链接 |
| 视图 | `resources/views/auth/reset-password.blade.php` | 已创建 | 输入新密码/确认密码 |
| 登录页 | `resources/views/auth/login.blade.php` | 已修改 | 已增加“忘记密码？”链接 |
| 迁移 | `database/migrations/2014_10_12_100000_create_password_resets_table.php` | 已存在且已执行 | `password_resets` 表已生成 |
| 配置 | `config/auth.php` | 已配置 | `passwords.users.table` 指向 `password_resets`，token 60 分钟失效 |
| 语言包 | `lang/zh_CN/passwords.php` | 已存在 | 包含 `sent`、`user`、`token` 等中文提示 |
| 环境 | `.env` | 已调整 | `APP_URL=http://127.0.0.1:8000`，`MAIL_MAILER=log`（本地测试） |

## 3. 待完成工作

本次计划只涉及**验证与收尾**，不包含大规模重构。

### 3.1 清理本地测试遗留（如存在）

**文件**：`app/Models/User.php`

- 检查并移除可能为调试临时添加的密码或测试代码（若存在）。
- 确认 `sendPasswordResetNotification` 仅调用自定义通知类，无多余逻辑。

### 3.2 重置链接 URL 校验

**文件**：`app/Notifications/ResetPasswordNotification.php`

- 确认 `url(route('password.reset', [...], false))` 在 `APP_URL` 正确时能生成可点击的完整链接。
- 若本地测试域名/端口与 `APP_URL` 不一致，按实际运行地址调整 `.env` 中的 `APP_URL`。

### 3.3 端到端功能验证

1. 启动本地服务 `php artisan serve`。
2. 访问 `/login`，确认“忘记密码？”链接可见且可点击。
3. 在 `/forgot-password` 输入一个已注册邮箱，提交后应看到成功提示“密码重置链接已发送到您的邮箱”。
4. 检查 `storage/logs/laravel.log`，确认收到中文重置邮件，并提取 `/reset-password/{token}?email=xxx` 链接。
5. 访问该链接，输入新密码并确认，提交后应自动登录并跳转到首页。
6. 退出后使用新密码重新登录，确认登录成功。
7. 测试邮箱不存在、token 过期、两次密码不一致等边界情况，确认错误提示为中文。

### 3.4 可选体验优化（仅在验证时发现明显问题时执行）

- 若成功提示不够醒目，可在 `forgot-password.blade.php` 中保留现有绿色提示框样式。
- 若重置页密码可见性切换失效，检查 `reset-password.blade.php` 的 `@push('scripts')` 是否被 `layouts.app` 正确渲染（`app.blade.php` 已包含 `@stack('scripts')`，应无问题）。

## 4. 假设与决策

1. **邮件驱动**：本地测试使用 `MAIL_MAILER=log`，邮件内容写入日志，不依赖真实 SMTP；生产环境应改为 `smtp` 或对应邮件服务。
2. **限流**：发送重置链接和提交重置请求均使用 `throttle:5,1`，防止滥用。
3. **token 有效期**：沿用 `config/auth.php` 中的 60 分钟，邮件文案已明确提示“60 分钟后失效”。
4. **不引入邮件模板文件**：使用 `MailMessage` 链式 API 生成邮件，保持简洁。
5. **不修改表结构**：沿用现有 `password_resets` 表，与 `config/auth.php` 保持一致。

## 5. 验证步骤

- [ ] 登录页 `/login` 出现“忘记密码？”链接。
- [ ] 点击后进入 `/forgot-password` 表单。
- [ ] 输入有效邮箱提交后，页面返回中文成功提示。
- [ ] `storage/logs/laravel.log` 中出现带完整链接的中文重置邮件。
- [ ] 点击链接进入 `/reset-password/{token}`，能成功设置新密码并自动登录。
- [ ] 使用新密码可正常登录。
- [ ] 输入不存在邮箱、错误 token、不一致密码时，提示为中文且页面不崩溃。
