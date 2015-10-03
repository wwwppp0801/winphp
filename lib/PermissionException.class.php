<?php
class PermissionException extends Exception{
    /**
     * 权限异常
     * @param unknown $message 异常信息
     * @param unknown $errorCode 错误码
     * @param unknown $permission 权限名
     */
    public function __construct($message, $errorCode, $permission)
    {
        parent::__construct($message, $errorCode);
    }
}