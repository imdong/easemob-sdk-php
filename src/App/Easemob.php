<?php

namespace ImDong\Easemob\App;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use ImDong\Easemob\Assists\StorageAssist;
use ImDong\Easemob\Exceptions\EasemobException;

/**
 * 环信 IM SDK 核心类
 *
 * @package ImDong\Easemob\App
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-07 18:05
 */
class Easemob
{
    use UserTrait, MessageTrait;

    /**
     * 用户
     */
    public const TARGET_TYPE_USER = 'user';

    /**
     * 群
     */
    public const TARGET_TYPE_CHAT_GROUPS = 'chatgroups';

    /**
     * 聊天室
     */
    public const TARGET_TYPE_CHAT_ROOMS = 'chatrooms';

    /**
     * 目标类型 对照表
     *
     * @var string[] target_type_map
     */
    public static $target_type_map = [
        self::TARGET_TYPE_USER        => '用户',
        self::TARGET_TYPE_CHAT_GROUPS => '群',
        self::TARGET_TYPE_CHAT_ROOMS  => '聊天室'
    ];

    /**
     * 接口地址域名
     *
     * @var string api_domain
     */
    public $api_domain = 'https://a1.easemob.com';

    /**
     * 唯一租户标识 (企业的唯一标识)
     *
     * @var string org_name
     */
    public $org_name = null;

    /**
     * APP 唯一标识
     *
     * @var string app_name
     */
    public $app_name = null;

    /**
     * Client ID
     *
     * @var string client_id
     */
    public $client_id = null;

    /**
     * Client Secret (通讯密钥)
     *
     * @var string client_secret
     */
    public $client_secret = null;

    /**
     * 用于缓存 Client Auth Token 的 Key
     *
     * @var string cache_client_token
     */
    public $cache_client_token = 'AuthToken';

    /**
     * 是否缓存用户 Token
     *
     * @var int cache_user_token
     */
    public $cache_user_token = 0;

    /**
     * 使用 Client Secret 换发的 Token 信息
     *
     * @var string client_credentials
     */
    private $client_credentials = null;

    /**
     * 用于网络请求的 Http 对象
     *
     * @var Client http
     */
    private $http;

    /**
     * 返回结果集中不需要的 Keys
     *
     * @var array hidden_keys
     */
    private $hidden_keys = [
        'path', 'uri', 'timestamp', 'organization', 'application', 'action', 'duration', 'applicationName'
    ];

    /**
     * Easemob constructor.
     *
     * @param array|null $options 配置选项
     */
    public function __construct(array $options = [])
    {
        // 设置配置
        $this->setOption($options, null, true);
    }

    /**
     * 设置配置
     *
     * @param      $key
     * @param null $value
     * @param bool $load_config 从配置中加载默认值
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 11:32
     */
    public function setOption($key, $value = null, bool $load_config = false)
    {
        // 可配置属性
        $option_keys = ['api_domain', 'org_name', 'app_name', 'client_id', 'client_secret', 'cache_user_token'];
        $update_url  = ['api_domain', 'org_name', 'app_name'];

        // 修改属性
        if (is_string($key) && in_array($key, $option_keys) && isset($value)) {
            if (in_array($key, $update_url)) {
                $this->http = null;
            }
            $this->{$key} = $value;
        } else if (is_array($key) && $load_config == false) {
            foreach ($key as $name => $value) {
                if (is_array($update_url) && in_array($key, $update_url)) {
                    $this->http = null;
                }
                $this->{$name} = $value;
            }
        } else if ($load_config == true) {
            $this->http = null;
            foreach ($option_keys as $key) {
                $this->{$key} = $options[$key] ?? StorageAssist::getConfig($key);
            }
        }
    }

    /**
     * 初始化 HTTP 请求对象
     *
     * @return Client
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 13:53
     */
    public function http(): Client
    {
        if (!$this->http) {
            $this->http = new Client([
                'base_uri'    => sprintf('%s/%s/%s/', $this->api_domain, $this->org_name, $this->app_name),
                'http_errors' => false,
                'headers'     => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);
        }

        return $this->http;
    }

    /**
     * 鉴权换取 Token
     *
     * @return string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 14:11
     */
    public function getAuthToken(): string
    {
        // 获取 Token 并检查过期时间
        if (
            ($this->client_credentials || $this->client_credentials = StorageAssist::getCache($this->cache_client_token))
            && time() - $this->client_credentials['expires'] < 60
        ) {
            return $this->client_credentials['access_token'];
        }

        // 使用 Client Secret 交换 Client Token
        $result = $this->send('POST', 'token', [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret
        ], ['no_auth' => true]);

        // 生成过期时间
        $result['expires'] = time() + $result['expires_in'];

        // 保存并写入到缓存
        $this->client_credentials = $result;
        StorageAssist::setCache($this->cache_client_token, $result, $result['expires']);

        return $this->client_credentials['access_token'];
    }

    /**
     * 发出一个 HTTP 请求
     *
     * @param string     $method 请求方式
     * @param string     $uri
     * @param array|null $body   $method = POST / PUT 时必须存在，其他请求时为 $options
     * @param array      $options
     * @return array
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 13:39
     */
    public function send(string $method, string $uri, array $body = null, array $options = []): array
    {
        // 整理一下参数补充顺序逻辑
        $method = strtoupper($method);
        if (!in_array($method, ['POST', 'PUT'])) {
            if (!isset($options) && isset($body)) {
                $options = $body;
                $body    = null;
            } else if (isset($options) && !empty($body)) {
                $options['query'] = $body;
                $body             = null;
            }
        }

        // 设置 Headers
        $headers = $options['headers'] ?? [];

        // 是否需要鉴权
        if (!($options['no_auth'] ?? false)) {
            $headers['Authorization'] = sprintf('Bearer %s', $this->getAuthToken());
        }

        // 构建请求对象
        $request = new Request($method, $uri, $headers, is_null($body) ? null : json_encode($body));

        // 发起网络请求
        try {
            $response = $this->http()->send($request, $options);
            $result   = json_decode($response->getBody()->getContents(), true);

            // 处理异常请求
            if ($response->getStatusCode() != 200) {
                throw (new EasemobException(
                    sprintf('%s: %s', $result['error'], $result['error_description']),
                    $response->getStatusCode()
                ))->setResult($result);
            }

            // 过滤掉一些不需要的参数
            if (!($options['not_hidden'] ?? false)) {
                $result = array_diff_key($result, array_flip($this->hidden_keys));
            }

        } catch (GuzzleException $e) {
            throw new EasemobException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }

        return $result;
    }


}
