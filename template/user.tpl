{%extends "question_base.tpl"%}
{%block name="body"%}
	<div class="tips">
    	<div class="tips1">
            <form class="form" action="/user/update" method="get">
                <input name="realname" value="{%$user.realname|escape%}" type="text" class="input1" /><br />
                <input name="phone" value="{%$user.phone|escape%}" type="text" class="input1" /><br />
                <select name="address" class="select1">
{%foreach ['北京','天津','上海'] as $city%}
                  <option {%if $user.address==$city%}checked="checked"{%/if%} value="{%$city|escape%}">{%$city|escape%}</option>
{%/foreach%}
                </select><br />
                <input name="qq" value="{%$user.qq|escape%}" type="text" class="input1" /><br />
                <input name="redirect" value="{%$redirect|escape%}" type="hidden" />
                <input type="submit" value="" class="button1" />
            </form>
        </div>
    </div>
{%/block%}

<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<title>养乐多活动</title>
</head>
<body>
<form action="/user/update">
<div>姓名：<input name="realname" value="{%$user.realname%}" maxLength="6"/></div>
<div>电话：<input name="phone" value="{%$user.phone%}" maxLength="20"/></div>
<div>地址：<input name="address" value="{%$user.address%}" maxLength="100"/></div>
<div><input type=submit value="提交"></div>
</form>
</body>
</html>
