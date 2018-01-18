<?php
class SystemException extends Exception
{
    public function __construct($message, $errorCode = 500)
    {
        parent::__construct($message, $errorCode);
    }

    public function getStatus() {
        $statusMap = [
            '403' => 'HTTP/1.1 403 Forbidden',
            '404' => 'HTTP/1.1 404 Not Found',
            '500' => 'HTTP/1.1 500 Internal Server Error',
        ];
        $code = strval($this->code);
        if (isset($statusMap[$code])) {
            return $statusMap[$code];
        }
        // 异常默认500
        return $statusMap[500];
    }
}
