<?php 
class PLogger {
    //log级别 1:error 2:info 3:debug 4:print out
    private $_level;
    private $_fp;
    private $_path;
    private $_filename;
    private $options;

    const ERROR=1;
    const WARN=2;
    const INFO=3;
    const DEBUG=4;
    const PRINT_OUT=5;

    private function __construct($options=[]){
        $options=array_merge([
            'level'=>self::ERROR,
            'path'=>'/tmp/',
            'file_prefix'=>'',
            'in_line'=>true,///每条日志仅打一行
            'time_split'=>true,
            ],$options);
        $this->_file_prefix=$options['file_prefix'];
        $this->_level=$options['level'];
        $this->_path=$options['path'];
        $this->_in_line=$options['in_line'];
        $this->options=$options;
        //by chenchao16
        //如果_file_prefix不存在用_path填充
        if(empty($this->_file_prefix) && !empty($this->_path)){
            $file_prefix = basename($this->_path);
            $file_prefix = str_replace(array("_","log",), "", $file_prefix);
            $this->_file_prefix = $file_prefix;
        } 

    }
    private static $loggers=[];
    public static function get($name='root',$options=[]){
        if(!isset(self::$loggers[$name])){
            self::$loggers[$name]=new self($options);
        }
        return self::$loggers[$name];
    }

    /**
     * 设置log级别
     *
     * @param num $level
     */
    public function setLevel($level = self::ERROR) {
        $this->_level = $level;
    }
    
    public function open($path = false) {
        $this->_filename = $this->getFileName();
        $this->_path = $path ? $path : $this->_path;
        if ($this->_path && !file_exists($this->_path)) {
            @mkdir($this->_path, 0777, true);
        }
        /*
        try{
            $oldMask=umask(0000);
            $this->_fp = fopen($this->_path.$this->_filename, "a");
            umask($oldMask);
        }catch(Exception $e){
            $this->error("can't open ".$this->_path.$this->_filename." .".$e);
        }
         */
    }
    
    public function close() {
        /*
        if (! empty($this->_fp))
            fclose($this->_fp);
         */
    }
    
    private function put($str) {
        $newname = $this->getFileName();
        if ($newname != $this->_filename) {
            $this->close();
            $this->open();
        }
        
        $now = date('[Y-m-d H:i:s:');
        $t = gettimeofday();
        if($this->_in_line){
            $str=str_replace(["\r","\n"],'',$str);
        }
        /*
        if ($this->_fp){
            fwrite($this->_fp, $now.$t["usec"]."] ".LOG_ID." ".$str."\n");
        }*/
        file_put_contents($this->_path.$this->_filename, $now.$t["usec"]."] ".LOG_ID." ".$str."\n",FILE_APPEND|LOCK_EX);
        if ($this->_level == self::PRINT_OUT) {
            echo "<div style='color:red'>".$now.$t["usec"]."] ".$str."</div>\n";
        }
    }
    
    /**
     * uilog格式化输出
     * @param string $str
     */
    private function uiPut($str) {
        $newname = $this->getFileNameUi();
        if ($newname != $this->_filename) {
            $this->_filename = $newname;
        }
        
        $now = date('[Y-m-d H:i:s:');
        $t = gettimeofday();
        if($this->_in_line){
            $str=str_replace(["\r","\n"],'',$str);
        }
        /*
         if ($this->_fp){
         fwrite($this->_fp, $now.$t["usec"]."] ".LOG_ID." ".$str."\n");
         }*/
        
        /*
        // 判断是否小时切分，小时切分的直接放入，非切分需要根据小时清空
        $filename = $this->_path.$this->_filename;
        if (!$this->options['time_split']) {
            if(file_exists($filename) && date('YmdH') == date('YmdH', filemtime($filename))) {
                file_put_contents($this->_path.$this->_filename, $now.$t["usec"]."] ".LOG_ID." ".$str."\n",FILE_APPEND|LOCK_EX);
            }
            else {
                file_put_contents($this->_path.$this->_filename, $now.$t["usec"]."] ".LOG_ID." ".$str."\n",LOCK_EX);
            }
        }
        else {
            file_put_contents($this->_path.$this->_filename, $now.$t["usec"]."] ".LOG_ID." ".$str."\n",FILE_APPEND|LOCK_EX);
        }*/
        file_put_contents($this->_path.$this->_filename, $now.$t["usec"]."] ".LOG_ID." ".$str."\n",FILE_APPEND|LOCK_EX);
        if ($this->_level == self::PRINT_OUT) {
            echo "<div style='color:red'>".$now.$t["usec"]."] ".$str."</div>\n";
        }
    }
    
    /**
     * trace格式化输出
     * @param string $str
     */
    private function tracePut($str) {
        $newname = $this->getFileNameUi();
        if ($newname != $this->_filename) {
            $this->_filename = $newname;
        }
    
        $now = date('[Y-m-d H:i:s:');
        $t = gettimeofday();
        if($this->_in_line){
            $str=str_replace(["\r","\n"],'',$str);
        }
        /*
         if ($this->_fp){
         fwrite($this->_fp, $now.$t["usec"]."] ".LOG_ID." ".$str."\n");
         }*/
        $filename = $this->_path.$this->_filename;
        if (!$this->options['time_split']) {
            if(file_exists($filename) && date('YmdH') == date('YmdH', filemtime($filename))) {
                file_put_contents($this->_path.$this->_filename, "[TRACE] " .$now.$t["usec"]."] "."[du-ui] [".LOG_ID."] ".$str."\n",FILE_APPEND|LOCK_EX);
            }
            else {
                file_put_contents($this->_path.$this->_filename, "[TRACE] " .$now.$t["usec"]."] "."[du-ui] [".LOG_ID."] ".$str."\n",LOCK_EX);
            }
        }
        else {
            file_put_contents($this->_path.$this->_filename, "[TRACE] " .$now.$t["usec"]."] "."[du-ui] [".LOG_ID."] ".$str."\n",FILE_APPEND|LOCK_EX);
        }
        if ($this->_level == self::PRINT_OUT) {
            echo "<div style='color:red'>".$now.$t["usec"]."] ".$str."</div>\n";
        }
    }
    
    /**
     * 输出error日志
     * @param string $str
     * @param string $logType ui/trace
     */
    public function error($str, $logType = "") {
        if ($this->_level >= self::ERROR) {
            switch($logType) {
                case 'trace':// uitraceLog 需要 json 保持某些字段信息必须为 string
                    $str = $this->parseTraceLog($str);
                    $this->tracePut("[ERROR] $str");
                    break;
                case 'ui':// PVLOG为统计标志    PVLOST为请求失败标志
                    $this->uiPut("[ERROR] PVLOG[1] PVLOST[1] $str".$this->backtrace());
                    break;
                default:
                    $this->put("[ERROR] $str".$this->backtrace());
                    break;
            }
        }
    }
    
    /**
     * 输入info日志
     * @param string $str
     * @param string $logType ui/trace
     */
    public function info($str, $logType = "") {
        if ($this->_level >= self::INFO) {
            switch($logType) {
                case 'trace':// uitraceLog 需要 json 保持某些字段信息必须为 string
                    $str = $this->parseTraceLog($str);
                    $this->tracePut("[INFO] $str");
                    break;
                case 'ui':// PVLOG为统计标志    PVLOST为请求失败标志
                    $this->uiPut("[INFO] PVLOG[1] PVLOST[0] $str");
                    break;
                default:
                    $this->put("[INFO] $str");
                    break;
            }
        }
    }
    
    /**
     * 输出warn日志
     * @param string $str
     * @param string $logType ui/trace
     */
    public function warn($str, $logType = "") {
        if ($this->_level >= self::WARN) {
            switch($logType) {
                case 'trace':// uitraceLog 需要 json 保持某些字段信息必须为 string
                    $str = $this->parseTraceLog($str);
                    $this->tracePut("[WARN] $str");
                    break;
                case 'ui':
                    $this->uiPut("[WARN] PVLOG[0] PVLOST[2] $str".$this->caller());
                    break;
                default:
                    $this->put("[WARN] $str".$this->caller());
                    break;
            }
        }
    }
    
    public function debug($str) {
        if ($this->_level >= self::DEBUG) {
            $this->put("[DEBUG] $str".$this->caller());
        }
    }
    
    private function getFileName() {
        if($this->_file_prefix && !$this->options['time_split']){
            return $this->_file_prefix.".log";
        }
        return $this->_file_prefix.".log.".date('YmdH');
    }
    
    /**
     * ui 和 trace 的文件名称为 ui.log.time/trace.log.time/ui.log/trace.log
     * @return string
     */
    private function getFileNameUi() {
        if($this->_file_prefix && !$this->options['time_split']){
            return $this->_file_prefix.".log";
        }
        return $this->_file_prefix.".log.".date('YmdH');
    }
    
    private function caller() {
        $bt = debug_backtrace();
        array_shift($bt);
        array_shift($bt);
        $data = '';
        $point = array_shift($bt);
        $func = isset($point['function']) ? $point['function'] : '';
        $file = isset($point['file']) ? substr($point['file'], strlen($basePath)) : '';
        $line = isset($point['line']) ? $point['line'] : '';
        $args = isset($point['args']) ? $point['args'] : '';
        $class = isset($point['class']) ? $point['class'] : '';
        if ($class) {
            $data .= "# ${class}->${func} at [$file:$line]";
        } else {
            $data .= "# $func at [$file:$line]";
        }
        
        return $data;
    }
    
    private function backtrace($basePath = "") {
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
                $data .= "#$i ${class}->${func} at [$file:$line]\t";
            } else {
                $data .= "#$i $func at [$file:$line]\t";
            }
        }
        
        return $data;
    }
    
    /**
     * 提取postdata里面的信息user_id，放置在json的第一层，保证string
     * @param string $str
     * @return boolean|string
     */
    private function parseTraceLog($str) {
        if(empty($str) || !is_string($str)) {
            return false;
        }
        $ret = json_decode($str, true);
        if(!empty($ret) && is_array($ret)) {
            if(empty($ret['user_id']) && isset($ret['ori_post_data']['user_id'])) {
                $ret['user_id'] = $ret['ori_post_data']['user_id'];
            }
        }
        $ret['user_id'] = strval($ret['user_id']);
        $ret = json_encode($ret, JSON_UNESCAPED_UNICODE);
        return $ret;
    }
}
