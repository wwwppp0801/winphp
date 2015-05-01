<?php
class SystemException extends Exception
{
    public function __construct($message, $errorCode = 0)
    {
        parent::__construct($message, $errorCode);
    }
}
