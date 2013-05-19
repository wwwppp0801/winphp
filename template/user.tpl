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
