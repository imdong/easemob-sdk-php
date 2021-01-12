<?php
/**
 * 环信 文件上传下载
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
     * @var string $message_path
     */
    public $chatfile_path = 'chatfiles';

    /**
     * 上传文件
     *
     * @param string|resource $file
     * @param bool            $restrict_access
     * @return array
     *
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-09 17:47
     */
    public function fileUpload($file, bool $restrict_access = true): array
    {
        $resource = $file;
        if (is_string($file)) {
            $resource = fopen($file, 'r');
        }

        if (!is_resource($resource)) {
            throw new EasemobException('{$file} Is not a valid file');
        }

        $result = $this->send('POST', $this->chatfile_path, [], [
            'multipart' => [
                [
                    'name'     => 'file',
                    'contents' => $resource
                ]
            ],
            'headers'   => [
                'restrict-access' => $restrict_access
            ]
        ]);

        return array_shift($result['entities']);
    }

    /**
     * fileGet
     *
     * @param string|array $uuid
     * @param string|null  $share_secret
     * @return string
     * @throws EasemobException
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-12 09:53
     */
    public function fileGet($uuid, string $share_secret = null): string
    {
        if (is_array($uuid)) {
            $share_secret = $share_secret ?? $uuid['share-secret'] ?? null;
            $uuid         = $uuid['uuid'] ?? null;
        }

        if (empty($uuid)) {
            throw new EasemobException('{uuid} cannot be empty');
        }

        $headers = [
            'Accept' => 'application/octet-stream'
        ];
        if (!empty($share_secret)) {
            $headers['share-secret'] = $share_secret;
        }

        // 直接保存到临时文件
        if (!$tmp_file = tempnam(sys_get_temp_dir(), 'easemob_')) {
            throw new EasemobException('Cannot create temporary file');
        }

        $this->send('GET',
            sprintf('%s/%s', $this->chatfile_path, $uuid),
            [
                'headers' => $headers,
                'save_to' => $tmp_file
            ]
        );

        return $tmp_file;
    }

}
