<?php
class Utils {

    public static function get_default_back_url(){
        if(isset($_SERVER['HTTP_REFERER'])){
            $ref_url=$_SERVER['HTTP_REFERER'];
        }else{
            $ref_url='/';
        }
        return $ref_url;
    }

    public static function toUTF8($str) {
        if(is_array($str)){
            return array_map('Utils::toUTF8',$str);
        }else{
            return mb_convert_encoding($str, 'UTF-8', 'GBK');
        }
    }

    public static function toGBK($str) {
        if(is_array($str)){
            return array_map('Utils::toGBK',$str);
        }else{
            return mb_convert_encoding($str, 'GBK', 'UTF-8');
        }
    }


    public static function getDistance($lng1,$lat1,$lng2,$lat2)//根据经纬度计算距离
    {
        //将角度转为狐度
        $radLat1=deg2rad($lat1);
        $radLat2=deg2rad($lat2);
        $radLng1=deg2rad($lng1);
        $radLng2=deg2rad($lng2);
        $a=$radLat1-$radLat2;//两纬度之差,纬度<90
        $b=$radLng1-$radLng2;//两经度之差纬度<180
        $s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137;
        return $s;
    }

    public static function curlGet($url, $timeout = 3, $headerAry = '') {
        if(is_array($timeout)){
            $headerAry=$timeout;
            $timeout=3;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		//output时忽略http响应头
        curl_setopt($ch, CURLOPT_HEADER, false);
		//设置http请求的头部信息 每行是数组中的一项
        //当url中用ip访问时，允许用host指定具体域名
        if ($headerAry != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerAry);
        }

        $res = curl_exec($ch);

        return $res;
    }


    public static function curlPost($url, $data, $timeout = 3, $headerAry = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($ch, CURLOPT_HEADER, false);
        if ($headerAry != '') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerAry);
        }
        $res = curl_exec($ch);
        //var_dump($url);
        return $res;
    }

    public static function array2Simple($pArray, $pField=null){
        $tRet = array();
        foreach ($pArray as $index=>$item) {
            if($pField==null){
                $tRet[]=current($item);
            }else{
                $tRet[] = $item[$pField];
            }
        }
        return $tRet;
    }
    public static function array2map($pArray, $pField=null){
        $tRet = array();
        foreach ($pArray as $index=>$item) {
            if($pField==null){
                $tRet[current($item)]=$item;
            }else{
                $tRet[$item[$pField]]=$item;
            }
        }
        return $tRet;
    }


    public static function memcacheGet($ip, $port, $key) {
        $memcache_obj = memcache_connect($ip, $port);
        if ($memcache_obj === false) {
            Logger::error("memcache_connect error. $ip:$port:$key\n");
            return false;
        }

        $res = memcache_get($memcache_obj, $key);
        if (false === $res) {
            Logger::error("memcache_get error. $ip:$port:$key\n");
            memcache_close($memcache_obj);
            return false;
        }
        memcache_close($memcache_obj);
        return $res;
    }

    public static function getClientIP() {
        if ($_SERVER["HTTP_X_FORWARDED_FOR"]) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif ($_SERVER["HTTP_CLIENT_IP"]) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif ($_SERVER["REMOTE_ADDR"]) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } elseif (getenv("HTTP_X_FORWARDED_FOR")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } elseif (getenv("HTTP_CLIENT_IP")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } elseif (getenv("REMOTE_ADDR")) {
            $ip = getenv("REMOTE_ADDR");
        } else {
            $ip = false;
        }
        $ip = explode(",", $ip, 2);
        $ip = trim($ip[0]);
        return $ip;
    }

    /**
	 * 获得今日零时的时间戳
	 */
	public static function getTodayTime() {
		$today = date ( 'Y-m-d' );
		list($y, $m, $d) = explode('-', $today);
		return mktime(0, 0, 0, $m, $d, $y);
	}

	/**
	 * Y-m-d H:i:s 形式的字串转为unix时间戳
	 * @param string $timeStr
	 */
	public static function string2time($timeStr){
		$timeStr = trim($timeStr);
		list($date, $time) = explode(' ', $timeStr);

		list($y, $m, $d) = explode('-', $date);
		list($h, $i, $s) = explode(':', $time);

		return mktime($h, $i, $s, $m, $d, $y);
	}

	/**
	 * @param string $pUrl 重定向目标地址
	 * @param integer $delay 延迟时间
	 * @return boolean
	 * @param delay
	 *
	 * 重定向URL
	 */
	public static function redirect($pUrl='/',$delay=0)	{
		if (headers_sent($file,$line)) {
			echo "<meta http-equiv=\"refresh\" content=\"{$delay};URL={$pUrl}\" />";
			exit;
		}
		if ($delay > 0) {
			header("Refresh:{$delay}; url={$pUrl}");
			exit;
		}

		header("HTTP/1.1 302 Found");
		header("Location:{$pUrl}");
	}
    
    public static function delTree($dir) { 
        $files = array_diff(scandir($dir), array('.','..')); 
        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file"); 
        } 
        return rmdir($dir); 
    } 

	public static function exportToCsv($csv_data, $filename = 'sample.csv') {
        $csv_data=Utils::toGBK($csv_data);
		$csv_terminated = "\n";
		$csv_separator = ",";
		$csv_enclosed = '"';
		$csv_escaped = "\\";

		$insert = '';

		$out = '';
		foreach ($csv_data as $row) {
			$insert = '';
			$fields_cnt = count($row);
			$tmp_str = '';
			foreach ($row as $v) {
				$tmp_str .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $v) . $csv_enclosed . $csv_separator;
			}


			$tmp_str = substr($tmp_str, 0, -1);
			$insert .= $tmp_str;

			$out .= $insert;
			$out .= $csv_terminated;
		}


		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($out));
		header("Content-type: text/x-csv");
		header("Content-Disposition:filename=$filename");

		echo $out;
		die();
	}
}
