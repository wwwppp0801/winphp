<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<meta property="qc:admins" content="1610360113544617002763757" />
<title>养乐多活动</title>
</head>
<body>
<script type="text/javascript">
var childWindow;
function toQzoneLogin()
{
    childWindow = window.open("redirect/qqlogin","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
} 

function closeChildWindow()
{
    childWindow.close();
}
</script>
<a href="redirect/logout">登出</a>
<a href="user">修改个人信息</a>
{%if $user%}
<p>nick:<b>{%$user.nickname|escape%}</b></p>
<p>figure:<img src="{%$user.figure_url%}"/></p>
{%/if%}
<div>
<a href="redirect/qqLogin"><img src="img/qq_login.png"></a>
</div>

<div>
<a href="/question/start">开始答题</a>
</div>
<!--a href="#" onclick='toQzoneLogin()'><img src="img/qq_login.png"></a
<br><br>
<a href="user/get_user_info.php"    target="_blank">获取用户信息</a>
<br><br>
<a href="share/add_share.html"      target="_blank">添加分享</a>
<br><br>
<a href="photo/list_album.php"      target="_blank">获取相册列表</a>
<br><br>
<a href="photo/add_album.html"      target="_blank">创建相册</a>
<br><br>
<a href="photo/upload_pic.html"     target="_blank">上传相片</a>
<br><br>
<a href="blog/add_blog.html"     target="_blank">发表日志</a>
<br><br>
<a href="topic/add_topic.html"     target="_blank">发表说说</a>
<br><br>
<a href="weibo/add_weibo.html"     target="_blank">发表微博</a-->
</body>
</html>
