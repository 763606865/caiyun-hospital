<?php

namespace App\Admin\Commands;

use App\Models\AdminRole;
use App\Models\AdminUser;
use App\Models\Organization;
use Database\Seeders\AdminAuthorizationSeeder;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create
        {--name= : 管理员姓名}
        {--email= : 管理员邮箱}
        {--password= : 管理员密码}
        {--organization= : 组织名称}
        {--organization-code= : 组织编码}';

    protected $description = '创建或更新一个超级管理员账号';

    public function handle(): int
    {
        $this->callSilent('db:seed', ['--class' => AdminAuthorizationSeeder::class]);

        $name = $this->option('name') ?: $this->ask('管理员姓名');
        $email = $this->option('email') ?: $this->ask('管理员邮箱');
        $password = $this->option('password') ?: $this->secret('管理员密码（至少 8 位）');

        if (! is_string($password) || mb_strlen($password) < 8) {
            $this->error('密码长度不能少于 8 位。');

            return self::FAILURE;
        }

        $admin = AdminUser::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'is_active' => true,
            ],
        );

        $admin->syncRoles([AdminRole::findByName('super-admin', 'admin')]);

        $organizationName = $this->option('organization') ?: config('app.name');
        $organizationCode = $this->option('organization-code') ?: 'default';
        $organization = Organization::query()->firstOrCreate(
            ['code' => $organizationCode],
            ['name' => $organizationName, 'status' => 'active'],
        );
        $organization->organizationMembers()->withTrashed()->updateOrCreate(
            ['admin_user_id' => $admin->id],
            ['display_name' => $admin->name, 'status' => 'active', 'joined_at' => now(), 'deleted_at' => null],
        );

        $this->info("超级管理员 {$admin->email} 已创建并加入组织 {$organization->name}，可通过 /admin 登录。");

        return self::SUCCESS;
    }
}
