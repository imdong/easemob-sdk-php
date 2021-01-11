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
trait ChatFileTrait
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
     * @param string          $type
     * @param string          $msg
     * @param array           $option
     *
     * @return array
     *
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-09 17:47
     */
    public function messageSend(string $target_type, $target, string $type, string $msg, array $option = []): array
    {
        // 处理收件人
        $targets = $target;
        if (is_string($target)) {
            $targets = [$target];
        }
        if (!is_array($targets)) {
            throw new EasemobException('The {target} must be a string[]');
        }




        $result = $this->send('POST', $this->message_path, [
            [

            ]
        ]);

        return $result['data'];
    }

}
