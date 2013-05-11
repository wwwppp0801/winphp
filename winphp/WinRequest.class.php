<?php 
WinRequest::init();
class WinRequest
{
    private static $request;
    private static $attributes = array();
    private static $model = array();
    
    public static function init()
    {
        self::$request = $_GET + $_POST;
    }
    
    public static function getModel()
    {
        return self::$model;
    }
    
    public static function setModel($model)
    {
        if (is_array($model))
        {
            self::$model = $model;
        }
        else
        {
            throw new SystemException("model must be an php key-value array");
        }
    }

    public static function clearModel(){
        self::$model=array();
    }
    
    public static function mergeModel($model)
    {
        if (is_array($model))
        {
            self::$model += $model;
        }
        else if (!$model)
        {
            return;
        }
        else
        {
            throw new SystemException("model must be an php key-value array");
        }
    }
    /**
     * 删除Model中的特定数据(由key指定)
     * @param string/array $key
     * @return nothing
     */
    public static function delModel($keys)
    {
        if (is_array($keys))
        {
            $delKey = $keys;
        }
		else
		{
        	$delKeys = array($keys);
		}
        foreach ($delKeys as $key)
        {     
            unset(self::$model[$key]);   
        }
    }
    
    public static function getCookie($key)
    {
        return $_COOKIE[$key];
    }
    
    public static function setCookie($key, $value)
    {
        $_COOKIE[$key] = $value;
        setcookie($key, $value);
    }
    
    public static function getParameter($key)
    {
        return self::$request[$key];
    }
    
    public static function setParameter($key, $value)
    {
        self::$request[$key] = $value;
    }
    
    public static function setAttribute($key, $value)
    {
        self::$attributes[$key] = $value;
    }
    
    public static function getAttribute($key)
    {
        return self::$attributes[$key];
    }
}
