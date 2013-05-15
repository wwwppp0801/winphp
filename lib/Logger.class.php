<?php 
class Soso_Logger {
    //log级别 1:error 2:info 3:debug 4:print out
    private static $_level = 1;
    private static $_fp;
    private static $_path = "/tmp/";
    private static $_filename;
    /**
     * 设置log级别
     *
     * @param num $level
     */
    public static function setLevel($level = 1) {
        self::$_level = $level;
    }
    
    public static function open($path = false) {
        self::$_filename = self::getFileName();
        self::$_path = $path ? $path : self::$_path;
        self::$_path = $path ? $path : self::$_path;
        try{
            self::$_fp = fopen(self::$_path.self::$_filename, "a");
        }catch(Exception $e){
            self::error("can't open ".self::$_path.self::$_filename." .".$e);
        }
    }
    
    public static function close() {
        if (! empty(self::$_fp))
            fclose(self::$_fp);
    }
    
    private static function put($str) {
        $newname = self::getFileName();
        if ($newname != self::$_filename) {
            self::close();
            self::open();
        }
        
        $now = date('[Y-m-d H:i:s:');
        $t = gettimeofday();
        if (self::$_fp)
            fwrite(self::$_fp, $now.$t["usec"]."] ".$str);
        if (self::$_level == 4) {
            echo "<div style='color:red'>".$now.$t["usec"]."] ".$str."</div>\n";
        }
    }
    
    public static function error($str) {
        if (self::$_level >= 1) {
            self::put("[ERROR] $str".self::backtrace());
        }
    }
    
    public static function info($str) {
        if (self::$_level >= 2) {
            self::put("[INFO] $str");
        }
    }
    
    public static function debug($str) {
        if (self::$_level >= 3) {
            self::put("[DEBUG] $str".self::caller());
        }
    }
    
    private static function getFileName() {
        return date('YmdH').".log";
    }
    
    private static function caller() {
        $bt = debug_backtrace();
        array_shift($bt);
        $data = '';
        $point = array_shift($bt);
        $func = isset($point['function']) ? $point['function'] : '';
        $file = isset($point['file']) ? substr($point['file'], strlen($basePath)) : '';
        $line = isset($point['line']) ? $point['line'] : '';
        $args = isset($point['args']) ? $point['args'] : '';
        $class = isset($point['class']) ? $point['class'] : '';
        if ($class) {
            $data .= "# ${class}->${func} at [$file:$line]\n";
        } else {
            $data .= "# $func at [$file:$line]\n";
        }
        
        return $data;
    }
    
    private static function backtrace($basePath = "") {
        $bt = debug_backtrace();
        array_shift($bt);
        $data = '';
        foreach ($bt as $i=>$point) {
            $func = isset($point['function']) ? $point['function'] : '';
            $file = isset($point['file']) ? substr($point['file'], strlen($basePath)) : '';
            $line = isset($point['line']) ? $point['line'] : '';
            $args = isset($point['args']) ? $point['args'] : '';
            $class = isset($point['class']) ? $point['class'] : '';
            if ($class) {
                $data .= "#$i ${class}->${func} at [$file:$line]\n";
            } else {
                $data .= "#$i $func at [$file:$line]\n";
            }
        }
        
        return $data;
    }
}
