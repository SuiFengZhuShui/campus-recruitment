<?php

/**
 * 演示账号统一密码的读取入口。
 *
 * 密码存放于 .env 的 SEED_PASSWORD，不硬编码在 seeder 代码里 ——
 * 避免把可用于登录的明文密码提交进公开仓库。
 * 默认值见 .env.example，生产环境部署前务必修改。
 */
class DemoPassword
{
    /**
     * @return string
     */
    public static function get()
    {
        $password = env('SEED_PASSWORD');

        if (empty($password)) {
            throw new RuntimeException(
                '未配置 SEED_PASSWORD —— 请在 .env 中添加 SEED_PASSWORD=你的演示密码 后再执行 seeder。'
            );
        }

        return $password;
    }
}
