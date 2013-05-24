{%strip%}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
{%block name="title"%}
{%/block%}
<link href="/css/index.css" rel="stylesheet" type="text/css" />
</head>

<body>

	<div class="main">
		<div class="banner"><a href="/question/start" title="点击开始"></a></div>
		<div class="nav">
            <a href="/index"{%if $executeInfo.methodName=='indexAction'%} class="now"{%/if%}>活动流程</a>
            <a href="/index/prize"{%if $executeInfo.methodName=='prizeAction'%} class="now"{%/if%}>奖项设置</a>
            <a href="/index/hire"{%if $executeInfo.methodName=='hireAction'%} class="now"{%/if%}>招募说明</a>
            <a href="/index/apply"{%if $executeInfo.methodName=='applyAction'%} class="now"{%/if%}>填写申请</a>
            <a href="/index/daren"{%if $executeInfo.methodName=='darenAction'%} class="now"{%/if%}>达人榜</a>
        </div>
        
{%block name="content"%}
{%/block%}
	</div>
    <div class="copy">本活动由养乐多联合搜搜问问共同推出，活动版权归属养乐多（中国）投资有限公司<br />养乐多（中国）投资有限公司具有对本活动的最终解释权，说明权和修改权<br />免责声明<br />京ICP备10007101</div>

</body>
</html>

{%/strip%}
