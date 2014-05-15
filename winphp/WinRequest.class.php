<?php 
WinRequest::init();
class WinRequest
{
    private static $request;
    private static $view;
    private static $attributes = array();
    private static $model = array();
    
    public static function init()
    {
        self::$request = $_GET + $_POST;
    }
    
    public static function getModel($key=null)
    {
        if(!is_null($key)){
            if(isset(self::$model[$key])){
                return self::$model[$key];
            }
            return null;
        }
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
    public static function getFlash($key)
    {
        $result=$_SESSION[$key];
        unset($_SESSION[$key]);
        return $result;
    }
    public static function setFlash($key,$value)
    {
        $_SESSION[$key]=$value;
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
    
    public static function getParameter($key,$default=null)
    {
        return isset(self::$request[$key])?self::$request[$key]:$default;
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
    
    public static function setView($view)
    {
        self::$view = $view;
    }
    
    public static function getView()
    {
        return self::$view;
    }
}
