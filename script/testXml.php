<?php

$xml=XMLUtils::from_array(array("name"=>array("first"=>"<\"peng","last"=>"wang"),"age"=>23),"xml");
var_dump($xml);
$xml=XMLUtils::to_array($xml);
var_dump($xml);



$xml="<xml><ToUserName><![CDATA[gh_18dbd63feb51]]></ToUserName>
<FromUserName><![CDATA[o5lSejpTRBnz-ZNmc1aRf8KpIDog]]></FromUserName>
<CreateTime>1375552196</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[qq]]></Content>
<MsgId>5907951695760982043</MsgId>
<EventKey><![CDATA[]]></EventKey>
</xml>";

$xml=XMLUtils::to_array($xml);
var_dump($xml);
