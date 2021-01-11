<?php
/**
 * Easemob 异常报错
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 16:01
 */

namespace ImDong\Easemob\Exceptions;

/**
 * Class EasemobException
 *
 * @package ImDong\Easemob\Exceptions
 *
 * @author  ImDong (www@qs5.org)
 * @created 2021-01-08 16:01
 */
class EasemobException extends \Exception
{
    /**
     * 资源描述信息
     *
     * @var array result
     */
    public $result = null;

    /**
     * 设置资源信息
     *
     * @param array $result
     * @return $this
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 16:10
     */
    public function setResult(array $result): self
    {
        $this->result = $result;

        return $this;
    }

    /**
     * 获取资源信息
     *
     * @return array|null
     * @author  ImDong (www@qs5.org)
     * @created 2021-01-08 16:08
     */
    public function getResult()
    {
        return $this->result;
    }
}
