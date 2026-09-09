# 诊所 SaaS 服务版

面向自费中医馆和社区诊所的门诊经营系统。当前仓库保留认证、后台权限、CMS、操作日志、文件上传以及患者、医生基础资料；医院挂号排班、体检和私有化授权模块已移除。

## 环境要求
- PHP >= 8.3（需 `ext-openssl`、`ext-redis`、`ext-zip`）
- Composer >= 2
- Node.js >= 22
- npm >= 10（本仓库使用 `package-lock.json`，CI 以 `npm ci` 安装）
- MySQL >= 8（生产推荐；本地 `.env.example` 默认可用 SQLite）
- Redis（Horizon 必需；生产队列建议 `QUEUE_CONNECTION=redis`）
- Laravel 13
- Filament 5（管理后台 `/admin`）
- Supervisor >= 4（生产常驻 Horizon）

## 快速初始化

完成依赖和环境变量配置后，可执行 `php artisan cms:install` 初始化 CMS，也可以按以下步骤手动初始化项目。

### 1) 配置 Laravel 目录权限

Laravel 运行时需要 Web/PHP 进程能够写入 `bootstrap/cache` 和 `storage`。在 Linux 服务器上，先将目录所属组改为 PHP-FPM/Web 服务使用的用户组（Debian/Ubuntu 通常为 `www-data`，CentOS/RHEL 可能为 `nginx` 或 `apache`）：

```bash
sudo chown -R "$(whoami)":www-data bootstrap/cache storage
sudo find bootstrap/cache storage -type d -exec chmod 775 {} \;
sudo find bootstrap/cache storage -type f -exec chmod 664 {} \;
```

本地开发时，如果 Composer、PHP 和 Web 服务均以当前用户运行，通常只需执行：

```bash
chmod -R u+rwX bootstrap/cache storage
```

> 不要直接对整个项目执行 `chmod -R 777`。生产环境应让部署用户拥有文件、PHP-FPM/Web 服务用户组拥有组写权限；如果服务器使用 ACL、SELinux 或容器挂载卷，还需同步配置对应的写权限。

若目录不存在（例如部署流程未保留 Git 中的空目录），可先创建 Laravel 常用运行时目录：

```bash
mkdir -p bootstrap/cache \
  storage/app/public \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/views \
  storage/logs
```

权限配置完成后，可验证当前用户是否能够写入：

```bash
test -w bootstrap/cache && test -w storage && echo "Laravel 目录可写"
```

### 2) 配置环境变量
```bash
cp .env.example .env
```

按需编辑 `.env`。本地默认可使用 SQLite；对接短信、微信小程序、OSS 等时补齐对应配置项。

### 3) 安装后端依赖
```bash
composer install
```

### 4) 生成应用密钥
```bash
php artisan key:generate
```

### 5) 执行数据库迁移
```bash
php artisan migrate
```

### 6) 填充基础数据
```bash
php artisan db:seed
```

会写入后台权限与 `super-admin` 角色（见 `AdminAuthorizationSeeder`）。

### 7) 安装并构建前端资源
```bash
npm install
npm run build
```

开发时可改用：

```bash
npm run dev
```

也可一键执行 Composer 的 `setup` 脚本（安装依赖、生成密钥、迁移、构建前端）：

```bash
composer setup
```

### 8) 创建超级管理员
```bash
php artisan admin:create
```

或非交互：

```bash
php artisan admin:create --name="管理员" --email="admin@example.com" --password="your-password"
```

创建后可通过 `/admin` 登录。该命令会先幂等执行权限 Seeder，再创建/更新账号并赋予 `super-admin` 角色。

> 不要使用 `php artisan make:filament-user`。该命令面向默认 User 模型，本项目后台账号在 `admin_users` 表，由 `admin:create` 管理。

### 9) 公共存储软链（可选）
```bash
php artisan storage:link
```

## API 认证说明
API 使用 Laravel Sanctum（`auth:sanctum`），无需 Passport 客户端。更多接口说明见 `docs/Api/`。

## 队列（Horizon 模式）
生产环境建议：
- `QUEUE_CONNECTION=redis`
- 使用 Redis 作为队列与 Horizon 元数据存储

### 1) 安装 Horizon（仅首次）
```bash
php artisan horizon:install
php artisan migrate
```

### 2) Supervisor 配置

项目内示例配置：`.supervisor/caiyun-laravel-horizon.conf`（进程名 `caiyun-laravel-horizon`）。

配置更新后刷新：

```bash
supervisorctl reread
supervisorctl update
supervisorctl restart caiyun-laravel-horizon:
supervisorctl status
```

### 3) 状态检查
```bash
php artisan horizon:status
```

> 已使用 Horizon 时，不建议再并行运行 `queue:work` 的常驻进程，避免消费链路混用。

## 定时任务配置
```bash
crontab -e
```

```bash
* * * * * cd /path/to/caiyun-laravel && php artisan schedule:run >> /dev/null 2>&1
```

生产若通过 GitHub Actions 发布，请将路径改为部署后的 `current` 目录（参见 `docs/部署.md`）。

## 后台权限维护
权限与超级管理员角色由 Seeder 维护，无需 Filament Shield：

```bash
# 仅同步权限与 super-admin 角色
php artisan db:seed --class=AdminAuthorizationSeeder

# 创建/更新超级管理员（内部也会执行上述 Seeder）
php artisan admin:create
```

新增后台资源后，在 `AdminAuthorizationSeeder` 中补充对应 permission，并更新相关 Policy。

## 生产部署
GitHub Actions 自动部署与服务器准备说明见 [`docs/部署.md`](docs/部署.md)。
