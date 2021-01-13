<?php
/**
 * 环信消息实现
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 11:39
 */

namespace ImDong\Easemob\App;


use ImDong\Easemob\Exceptions\EasemobException;

/**
 * Class EasemobUser
 *
 * @package ImDong\Easemob\App
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 11:41
 */
trait MessageTrait
{
    /**
     * 消息路由/前缀
     *
     * @var null $message_path
     */
    public $message_path = 'messages';

    /**
     * 消息类型对照表
     *
     * @var string[] message_type_map
     */
    private $message_type_map = [
        'txt'   => '文本消息',
        'img'   => '图片消息',
        'loc'   => '位置信息',
        'audio' => '语音消息',
        'video' => '视频消息',
        'file'  => '文件消息'
    ];

    /**
     * 发送文本消息
     *
     * @param string|string[] $target
     * @param string          $msg
     * @param string          $target_type
     * @param string|null     $from
     * @param array           $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-09 17:42
     */
    public function messageText($target, string $msg, string $target_type = Easemob::TARGET_TYPE_USERS, string $from = null, array $ext = [])
    {
        return $this->messageSend(
            $target_type,
            $target,
            'txt',
            $msg,
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送图片
     *
     * @param string|string[] $target
     * @param string          $file_uuid
     * @param string|null     $share_secret
     * @param string          $filename
     * @param int             $width
     * @param int             $height
     * @param string          $target_type
     * @param string|null     $from
     * @param array           $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 10:53
     */
    public function messageImage(
        $target,
        string $file_uuid, string $share_secret = null, string $filename = 'test-img.jpg', $width = 480, $height = 720,
        string $target_type = Easemob::TARGET_TYPE_USERS, string $from = null, array $ext = []
    ) {
        return $this->messageSend(
            $target_type,
            $target,
            'img',
            [
                'filename' => $filename,
                'secret'   => $share_secret,
                'url'      => sprintf('%s/%s/%s/%s', $this->api_domain, $this->org_name, $this->app_name, $file_uuid),
                'size'     => [
                    "width"  => $width,
                    "height" => $height
                ]
            ],
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送语音消息
     *
     * @param             $target
     * @param string      $file_uuid
     * @param string|null $share_secret
     * @param string      $filename
     * @param int         $length
     * @param string      $target_type
     * @param string|null $from
     * @param array       $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 11:22
     */
    public function messageAudio(
        $target,
        string $file_uuid, string $share_secret = null, string $filename = 'test-audio.amr', $length = 10,
        string $target_type = 'users', string $from = null, array $ext = []
    ) {
        return $this->messageSend(
            $target_type,
            $target,
            'audio',
            [
                'filename' => $filename,
                'secret'   => $share_secret,
                'url'      => sprintf('%s/%s/%s/%s', $this->api_domain, $this->org_name, $this->app_name, $file_uuid),
                'length'   => $length
            ],
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送视频消息
     *
     * @param             $target
     * @param string      $thumb_uuid
     * @param string|null $thumb_secret
     * @param string      $file_uuid
     * @param string|null $share_secret
     * @param string      $filename
     * @param int         $length
     * @param int         $file_length
     * @param string      $target_type
     * @param string|null $from
     * @param array       $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 11:26
     */
    public function messageVideo(
        $target,
        string $thumb_uuid, string $file_uuid,
        string $thumb_secret = null, string $share_secret = null,
        string $filename = 'test-video.mp4',
        $length = 10, $file_length = 58103,
        string $target_type = Easemob::TARGET_TYPE_USERS, string $from = null, array $ext = []
    ) {
        return $this->messageSend(
            $target_type,
            $target,
            'video',
            [
                'filename'     => $filename,
                'secret'       => $share_secret,
                'url'          => sprintf('%s/%s/%s/%s', $this->api_domain, $this->org_name, $this->app_name, $file_uuid),
                'thumb'        => sprintf('%s/%s/%s/%s', $this->api_domain, $this->org_name, $this->app_name, $thumb_uuid),
                'thumb_secret' => $thumb_secret,
                'length'       => $length,
                'file_length'  => $file_length
            ],
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送位置消息
     *
     * @param             $target
     * @param string      $lat
     * @param string      $lng
     * @param string      $addr
     * @param string      $target_type
     * @param string|null $from
     * @param array       $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 11:32
     */
    public function messageLocation(
        $target, string $lat, string $lng, string $addr,
        string $target_type = Easemob::TARGET_TYPE_USERS, string $from = null, array $ext = []
    ): array {
        return $this->messageSend(
            $target_type,
            $target,
            'loc',
            ['lat' => $lat, 'lng' => $lng, 'addr' => $addr],
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送透传消息
     *
     * @param             $target
     * @param string      $action
     * @param string      $target_type
     * @param string|null $from
     * @param array       $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 11:35
     */
    public function messageCommand(
        $target, string $action, string $target_type = Easemob::TARGET_TYPE_USERS, string $from = null, array $ext = []
    ) {
        return $this->messageSend(
            $target_type,
            $target,
            'cmd',
            ['action' => $action],
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送自定义消息
     *
     * @param             $target
     * @param string      $custom_event
     * @param array|null  $custom_Ext
     * @param string      $target_type
     * @param string|null $from
     * @param array       $ext
     * @return array|string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 11:37
     */
    public function messageCustom(
        $target, string $custom_event, array $custom_Ext = null, string $target_type = Easemob::TARGET_TYPE_USERS, string $from = null, array $ext = []
    ) {
        $msg = [
            'customEvent' => $custom_event
        ];
        if (!empty($custom_Ext)) {
            $msg['customExts'] = $custom_Ext;
        }

        return $this->messageSend(
            $target_type,
            $target,
            'custom',
            $msg,
            ['form' => $from, 'ext' => $ext]
        );
    }

    /**
     * 发送消息 (最终实现)
     *
     * @param string          $target_type
     * @param string|string[] $target
     * @param string          $message_type 即 type 消息类型；txt:文本消息，img：图片消息，loc：位置消息，audio：语音消息，video：视频消息，file：文件消息
     * @param string|array    $msg
     * @param array           $option
     *
     * @return array|string
     *
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-09 17:47
     */
    public function messageSend(string $target_type, $target, string $message_type, $msg, array $option = [])
    {
        // 目标类型
        if (!isset($this->target_type_map[$target_type])) {
            throw new EasemobException('The parameter {target_type} is not a valid type');
        }

        // 收件人处理为数组
        $targets = $target;
        if (is_string($target)) {
            $targets = [$target];
        }
        if (!is_array($targets)) {
            throw new EasemobException('The {target} must be a string[]');
        }

        // 消息类型
        if (!isset($this->message_type_map)) {
            throw new EasemobException('The parameter {message_type} is not a valid type');
        }

        // 生成 消息体
        if (is_string($msg)) {
            $msg = [
                'msg' => $msg
            ];
        }
        $msg['type'] = $message_type;

        // 请求 Body
        $body = [
            'target_type' => $target_type,
            'target'      => $target,
            'msg'         => $msg
        ];

        if (!empty($option['form'])) {
            $body['form'] = $option['form'];
        }

        // 发送消息
        $result = $this->send('POST', $this->message_path, $body);

        // 发送到单人的返回单个
        $return = $result['data'];
        if (is_string($target)) {
            $return = array_shift($return);
        }

        return $return;
    }

}
