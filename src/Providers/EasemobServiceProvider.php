<?php

namespace ImDong\Easemob\Providers;

use Illuminate\Support\ServiceProvider;

use ImDong\Easemob\App\Easemob;

class EasemobServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * 引导程序
     *
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $path = realpath(__DIR__ . '/../../configs/laravel_easemob.php');

        $this->publishes([
            $path => config_path('easemob.php'),
        ]);
    }

    /**
     * 默认包位置
     *
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $path = realpath(__DIR__ . '/../../configs/laravel_easemob.php');

        // 将给定配置文件合现配置文件接合
        $this->mergeConfigFrom($path, 'easemob');

        // 容器绑定
        $this->app->bind('Easemob', function () {
            return new Easemob();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

}
