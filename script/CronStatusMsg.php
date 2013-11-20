<?php
/**
 * 每天下半夜跑前一天的统计数据
 * 20 2 * * * (cd /home/wp/wx_platform; /usr/bin/php webroot/route.php CronStatusMsg.php 2>&1)
 */
class CronStatusMsg{
	public function run(){
		$msgTbl = new DBTable("message");
		$today = Utils::getTodayTime();
		$yesterday = $today - 86400;

		$msgTbl->addWhere('CreateTime', $yesterday, ">=");
		$msgTbl->addWhere('CreateTime', $today, "<");
		$num = $msgTbl->count();

		$msgStatusTbl = new DBTable("status_msg");
		$data = array(
			"num" => $num,
			'time' => date("Y-m-d 00:00:00", $yesterday),
		);

		$msgStatusTbl->insert($data);
	}
}

$obj = new CronStatusMsg();
$obj->run();