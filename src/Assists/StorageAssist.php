<?php

namespace ImDong\Easemob\Assists;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * 数据&储存相关辅助类
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 11:11
 */
class StorageAssist
{
    /**
     * 读取配置文件
     *
     * @param string      $key
     * @param string|null $default
     * @return string
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 11:12
     */
    public static function getConfig(string $key, string $default = null): string
    {
        return Config::get(sprintf('easemob.%s', $key), $default);
    }

    /**
     * 读取缓存信息
     *
     * @param string     $key
     * @param mixed|null $default
     * @return mixed
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 11:14
     */
    public static function getCache(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * setCache
     *
     * @param string                 $key
     * @param mixed                  $value
     * @param null|int|\DateInterval $ttl
     * @return bool
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 11:16
     */
    public static function setCache(string $key, $value, $ttl = null): bool
    {
        return Cache::set($key, $value, $ttl);
    }
}
