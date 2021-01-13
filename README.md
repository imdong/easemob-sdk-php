# easemob-sdk-php

Easemob SDK for PHP

适用于 Laravel 的 环信 IM SDKi

## 安装方法

> composer require imdong/easemob-sdk 

Laravel 会自动发现，旧版本不支持自动发现的，添加以下内容到 `/config/app.php` 中。

```
'providers' => [
    ImDong\Easemob\Providers\EasemobServiceProvider::class,
],
'aliases' => [
    'Easemob'      => ImDong\Easemob\Facades\Easemob::class,
],
```

##
初步完成用户操作与消息发送

其余路由抽时间完善

## 开源

本扩展使用 [MIT](LICENSE) 开源。
