# 商业授权接入

## 设计

项目采用 RSA/SHA-256 非对称签名许可证。授权服务器保存私钥并签发许可证，外包项目只部署公钥，不得将私钥放入项目仓库或交付给客户。

许可证为 `<base64url-json>.<base64url-signature>`，包含以下字段：

```json
{
  "license_id": "LIC-2026-0001",
  "customer": "客户名称",
  "plan": "pro",
  "issued_at": "2026-08-04T00:00:00+08:00",
  "not_before": "2026-08-04T00:00:00+08:00",
  "expires_at": "2027-08-04T23:59:59+08:00",
  "features": ["payment", "mobile", "ai"],
  "domains": ["example.com", "*.example.com"]
}
```

`features` 可使用 `*` 授予所有已定义功能。功能标识定义在 `config/license.php`。

## 生产配置

```dotenv
LICENSE_ENABLED=true
LICENSE_TOKEN=eyJ...
LICENSE_PUBLIC_KEY_PATH=storage/app/license/public.pem
LICENSE_VERIFY_DOMAIN=true
LICENSE_LEEWAY=300
LICENSE_REMOTE_ENABLED=true
LICENSE_REMOTE_ENDPOINT=https://license.example.com/api/v1/leases
LICENSE_PROJECT=cms
LICENSE_REMOTE_CHECK_INTERVAL=60
LICENSE_OFFLINE_GRACE_HOURS=72
```

公钥文件应以只读方式部署。如无法部署文件，可使用 `LICENSE_PUBLIC_KEY` 传入 Base64 编码的 PEM。修改环境变量后需要重建配置缓存。

```bash
php artisan config:cache
php artisan license:status --domain=example.com
```

## 中间件

- `licensed`：校验签名、生效时间、到期时间和域名。已全局接入 Web 和 API 中间件组。
- `feature:{name}`：在基础授权之上检查功能权益。

```php
Route::middleware('feature:payment')->group(function (): void {
    Route::post('/orders/{order}/pay', PayController::class);
});
```

开发和测试环境可保持 `LICENSE_ENABLED=false`。生产项目必须开启；`/up` 健康检查不受授权状态影响。

## 远程校验协议

开启 `LICENSE_REMOTE_ENABLED` 后，客户端首次启动必须连接授权服务器。验证成功后每 60 分钟重新检查，并保存最长 72 小时的服务端签名租约。断网或服务器 `5xx` 时可在租约期内继续运行；租约过期后拒绝请求。服务器返回 `4xx` 撤销时立即失效，不使用离线缓存。

客户端请求：

```http
POST /api/v1/leases
Content-Type: application/json

{
  "license": "<signed-license>",
  "license_id": "LIC-2026-0001",
  "instance_id": "a stable UUID",
  "domain": "example.com",
  "project": "cms",
  "app_version": null
}
```

授权服务器需校验许可证、状态、客户、实例数和域名，然后使用与本地许可证相同的 RSA 私钥签发短期租约：

```json
{
  "lease": "<base64url-json>.<base64url-signature>"
}
```

租约 Payload：

```json
{
  "license_id": "LIC-2026-0001",
  "instance_id": "a stable UUID",
  "domain": "example.com",
  "issued_at": "2026-08-04T12:00:00+08:00",
  "expires_at": "2026-08-07T12:00:00+08:00",
  "features": ["payment", "mobile", "ai"]
}
```

`expires_at - issued_at` 不得超过 `LICENSE_OFFLINE_GRACE_HOURS`。远程租约的功能与本地许可证取交集，因此授权服务器可以即时收回单个功能。

撤销建议返回：

```json
{
  "reason": "revoked",
  "message": "授权已撤销"
}
```

HTTP 状态码为 `403`。安装实例 ID 保存在 `storage/app/license/instance_id`，签名租约缓存保存在 `storage/app/license/remote_receipt.json`，部署时应保留这两个文件。
