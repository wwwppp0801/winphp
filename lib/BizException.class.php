<?php
class BizException extends Exception
{
    public function __construct($message, $errorCode = 0)
    {
        parent::__construct($message, $errorCode);
    }
}
