<?php
/**
 * 环信用户相关功能实现
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 11:39
 */

namespace ImDong\Easemob\App;

use ImDong\Easemob\Assists\StorageAssist;

/**
 * Class EasemobUser
 *
 * @package ImDong\Easemob\App
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 11:41
 */
trait UserTrait
{
    /**
     * 用户管理时的 path 前缀
     *
     * @var null user_path
     */
    public $user_path = 'users';

    /**
     * 添加单个用户 (开放注册模式)
     *
     * @param string      $username 环信 ID ;也就是 IM 用户名的唯一登录账号，长度不可超过64个字符长度
     * @param string      $password 登录密码，长度不可超过64个字符长度
     * @param string|null $nickname 昵称（可选），在 iOS Apns 推送时会使用的昵称（仅在推送通知栏内显示的昵称），
     *                              并不是用户个人信息的昵称，环信是不保存用户昵称，头像等个人信息的，
     *                              需要自己服务器保存并与给自己用户注册的IM用户名绑定，长度不可超过100个字符
     *
     * @return array    接口返回的 entities[0] 字段信息
     *
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 11:41
     */
    public function userAddByRegistration(string $username, string $password, string $nickname = null): array
    {
        $result = $this->send('POST', $this->user_path, [
            [
                'username' => $username,
                'password' => $password,
                'nickname' => $nickname
            ]
        ], [
            'no_auth' => true
        ]);

        return array_shift($result['entities']);
    }

    /**
     * 添加单个用户 (授权注册模式)
     *
     * @param string      $username 环信 ID ;也就是 IM 用户名的唯一登录账号，长度不可超过64个字符长度
     * @param string      $password 登录密码，长度不可超过64个字符长度
     * @param string|null $nickname 昵称（可选），在 iOS Apns 推送时会使用的昵称（仅在推送通知栏内显示的昵称），
     *                              并不是用户个人信息的昵称，环信是不保存用户昵称，头像等个人信息的，
     *                              需要自己服务器保存并与给自己用户注册的IM用户名绑定，长度不可超过100个字符
     *
     * @return array    接口返回的 entities[0] 字段信息
     *
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 14:27
     */
    public function userAddByAuthorization(string $username, string $password, string $nickname = null): array
    {
        $result = $this->send('POST', $this->user_path, [
            [
                'username' => $username,
                'password' => $password,
                'nickname' => $nickname
            ]
        ]);

        return array_shift($result['entities']);
    }

    /**
     * 批量添加用户
     *
     * @param array $users 用户对象列表
     *                     格式必须是: [ ['username' => 'user', 'password' => '123456', 'nickname' => 'User'], ... ]
     *                     结构字段说明参考 self::userAddByAuthorization()
     *
     * @return array 接口返回的 entities 字段信息
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 16:51
     */
    public function userAddBatch(array $users): array
    {
        $result = $this->send('POST', $this->user_path, $users);

        return $result['entities'];
    }

    /**
     * 获取单个用户信息
     *
     * @param string $username
     * @return array
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:04
     */
    public function userGetInfo(string $username): array
    {
        $result = $this->send('GET', sprintf('%s/%s', $this->user_path, $username));

        return array_shift($result['entities']);
    }

    /**
     * 查看一个用户的在线状态。
     *
     * @param string $username
     * @return string
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 18:22
     */
    public function userGetStatus(string $username): string
    {
        $result = $this->send('GET', sprintf('%s/%s/status', $this->user_path, $username));

        return array_shift($result['data']);
    }

    /**
     * 批量获取用户信息
     *
     * @param int|null    $limit  如果需要指定获取数量，需加上参数 limit=N，N 为数量值
     * @param string|null $cursor 关于分页：如果 DB 中的数量大于 N，返回 JSON 会携带一个字段“cursor”,我们把它叫做”游标”，
     *                            该游标可理解为结果集的指针，值是变化的。往下取数据的时候带着游标，就可以获取到下一页的值。如果还有下一页，
     *                            返回值里依然还有这个字段，直到没有这个字段，说明已经到最后一页。 cursor 的意义在于数据（真）分页
     * @return array    返回除隐藏字段外的所有字段
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:08
     */
    public function userGetBatch(int $limit = null, string $cursor = null): array
    {
        // 请求参数
        $query = [];
        if (!is_null($limit)) {
            $query['limit'] = $limit;
            if (!is_null($cursor)) {
                $query['cursor'] = $cursor;
            }
        }

        return $this->send('GET', $this->user_path, [
            'query' => $query
        ]);
    }

    /**
     * 删除单个用户
     *
     * @param string $username
     * @return array
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:48
     */
    public function userDel(string $username): array
    {
        $result = $this->send('DELETE', sprintf('%s/%s', $this->user_path, $username));

        return array_shift($result['entities']);
    }

    /**
     * 批量删除用户
     *
     * 删除某个 APP 下指定数量的环信账号。可一次删除 N 个用户，数值可以修改。建议这个数值在100-500之间，不要过大。
     * 需要注意的是，这里只是批量的一次性删除掉 N个用户，具体删除哪些并没有指定，可以在返回值中查看到哪些用户被删除掉了。
     *
     * @param int|null $limit 如果需要指定获取数量，需加上参数 limit=N，N 为数量值
     *
     * @return array    返回除隐藏字段外的所有字段
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:08
     */
    public function userDelBatch(int $limit): array
    {
        // 请求参数
        $query['limit'] = $limit;

        return $this->send('DELETE', $this->user_path, [
            'query' => $query
        ]);
    }

    /**
     * 修改用户密码
     *
     * @param string $username
     * @param string $new_password
     * @return bool
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:52
     */
    public function userSetPassword(string $username, string $new_password): bool
    {
        $result = $this->send(
            'PUT',
            sprintf('%s/%s/password', $this->user_path, $username),
            [
                'newpassword' => $new_password
            ]
        );

        return true;
    }

    /**
     * 修改用户密码
     *
     * @param string $username
     * @param string $nickname
     * @return bool
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:52
     */
    public function userSetNickname(string $username, string $nickname): bool
    {
        $result = $this->send(
            'PUT',
            sprintf('%s/%s', $this->user_path, $username),
            [
                'nickname' => $nickname
            ]
        );

        return $result['entities'];
    }

    /**
     * 设置推送消息展示方式
     *
     * 设置推送消息至客户端的方式，修改后及时有效。服务端对应不同的设置，向用户发送不同展示方式的消息。
     *
     * @param string $username
     * @param int    $notification_display_style “0”仅通知，“1“通知以及消息详情
     * @return array
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 18:00
     */
    public function userSetNotificationDisplayStyle(string $username, int $notification_display_style): array
    {
        $result = $this->send(
            'PUT',
            sprintf('%s/%s', $this->user_path, $username),
            [
                'notification_display_style' => $notification_display_style
            ]
        );

        return $result['entities'];
    }

    /**
     * 设置推送消息展示方式
     *
     * 设置推送消息至客户端的方式，修改后及时有效。服务端对应不同的设置，向用户发送不同展示方式的消息。
     *
     * @param string $username
     * @param bool   $switch 是否免打扰
     * @return array
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 18:00
     */
    public function userSetNotificationNoDisturbing(string $username, bool $switch, int $start = null, int $end = null): array
    {
        $body = [
            'notification_no_disturbing' => $switch,
        ];

        if ($switch) {
            $body = $body + [
                    'notification_no_disturbing_start' => $start,
                    'notification_no_disturbing_end'   => $end
                ];
        }

        $result = $this->send('PUT', sprintf('%s/%s', $this->user_path, $username), $body);

        return $result['entities'];
    }

    /**
     * 获取用户 Token (密码登录获取)
     *
     * @param string $username
     * @param string $password
     * @return array
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 17:02
     */
    public function userGetTokenByPassword(string $username, string $password): array
    {
        // 获取 Token 并检查过期时间
        if ($this->cache_user_token >= 0 && $token = StorageAssist::getCache(sprintf('UserAccessToken:%s', $username))) {
            return $token;
        }

        // 登录并获取 Token
        $result = $this->send('POST', sprintf('%s/token', $this->user_path), [
            'grant_type' => 'password',
            'username'   => $username,
            'password'   => $password,
        ]);

        // 写入缓存
        if ($this->cache_user_token >= 0) {
            $expires = time() + ($this->cache_user_token == 0 ? $result['expires_in'] : $this->cache_user_token);
            StorageAssist::setCache(sprintf('UserAccessToken:%s', $username), $result['access_token'], $expires);
        }

        return $result['entities']['access_token'];
    }
}
