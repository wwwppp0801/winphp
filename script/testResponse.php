<?php
$postStr="<xml><ToUserName><![CDATA[gh_18dbd63feb51]]></ToUserName>
<FromUserName><![CDATA[o5lSejrIdK0j1VFsIEqgVW8YtiAg]]></FromUserName>
<CreateTime>1381026119</CreateTime>
<MsgType><![CDATA[event]]></MsgType>
<Event><![CDATA[subscribe]]></Event>
<EventKey><![CDATA[]]></EventKey>
</xml>";
/*
$postStr="<xml><ToUserName><![CDATA[gh_18dbd63feb51]]></ToUserName>
<FromUserName><![CDATA[o5lSejpTRBnz-ZNmc1aRf8KpIDog]]></FromUserName>
<CreateTime>1375552196</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[我要刮奖]]></Content>
<MsgId>5907951695760982043</MsgId>
</xml>";
 */
/*
$postStr="<xml><ToUserName><![CDATA[gh_18dbd63feb51]]></ToUserName>
<FromUserName><![CDATA[o5lSejpTRBnz-ZNmc1aRf8KpIDog]]></FromUserName>
<CreateTime>1375552196</CreateTime>
<MsgType><![CDATA[location]]></MsgType>
<Location_X><![CDATA[116.375254]]></Location_X>
<Location_Y><![CDATA[39.966337]]></Location_Y>
<Scale>20</Scale>
<Label><![CDATA[位置信息]]></Label>
<MsgId>5907951695760982043</MsgId>
</xml>";
 */
Soso_Logger::setLevel(3);
$GLOBALS["HTTP_RAW_POST_DATA"]=$postStr;

$c=new MessageController();
//ob_start();
$ret=$c->indexAction();
//$xml=ob_get_contents();
$xml=preg_replace('/^text\:/',"",$ret[0]);
var_dump($xml);
$msg=MessageModel::parseMessage($xml);
var_dump($msg);

/*
$msg=MessageModel::parseMessage($postStr);
Soso_Logger::debug($msg->toStr());
$msg->save();

$sendMsg=MessageModel::createMessage(array(
    'ToUserName'=>$msg->FromUserName,
    'FromUserName'=>$msg->ToUserName,
    'MsgType'=>'text',
    'Content'=>'default content',
));

Soso_Logger::debug($sendMsg->toStr());
Soso_Logger::debug(var_export(XMLUtils::to_array($sendMsg->toStr()),1));


$resource=new Resource();
$resource=$resource->addWhere("id","3")->select();

$msg=MessageModel::createMessageFromResource($resource);
echo $msg->toStr();


$msg=MessageModel::createMessage(array(
    'ToUserName'=>$msg->FromUserName,
    'FromUserName'=>$msg->ToUserName,
    'MsgType'=>'text',
    //'Content'=>$msg->Content,
    'Content'=>'天气',
));
$chain=new PreciseProcessChain();
$chain->process($msg,$sendMsg);
var_dump($sendMsg);
 */



