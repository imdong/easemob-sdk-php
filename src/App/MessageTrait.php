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
     * @param string      $target_type
     * @param             $target
     * @param string      $msg
     * @param string|null $from
     * @param array       $ext
     * @return array
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-09 17:42
     */
    public function messageText(string $target_type, $target, string $msg, string $from = null, array $ext = []): array
    {
        return $this->messageSend($target_type, $target, 'txt', $msg, [
            'form' => $from,
            'ext'  => $ext
        ]);
    }

    /**
     * 发送消息 (最终实现)
     *
     * @param string          $target_type
     * @param string|string[] $target
     * @param string          $message_type 即 type 消息类型；txt:文本消息，img：图片消息，loc：位置消息，audio：语音消息，video：视频消息，file：文件消息
     * @param string          $msg
     * @param array           $option
     *
     * @return array
     *
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-09 17:47
     */
    public function messageSend(string $target_type, $target, string $message_type, string $msg, array $option = []): array
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

        // 拼装消息体
        $body = [
            'target_type' => $target_type,
            'target' => $target,
            'type' => $message_type,
            'msg' =>$msg
        ];

        // 发送消息
        $result = $this->send('POST', $this->message_path, $body);

        return $result['data'];
    }

}
