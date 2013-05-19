<!doctype html>
<html>
<head>
<meta charset="utf-8" />
<meta property="qc:admins" content="1610360113544617002763757" />
<title>养乐多活动</title>
</head>
<body>
<h1>step {%$step+1%}</h1>
倒计时：<div id="time">{%$time%}</div>
<div style="color:red">
    {%if $wrong_question%}
        {%$wrong_question.title%}，回答错误，请重新答题
    {%/if%}
    {%if $wrong_time%}
        超过规定答题时间，请重新答题
    {%/if%}
    {%if $wrong_captcha%}
        验证码错误，请重新输入
    {%/if%}
</div>
<form action="/question/answer">
{%foreach $questions as $i=>$question%}
<div>
    <h3>{%$question.title|escape%}{%$question.answer%}</h3>
    {%foreach $question.choices as $j=>$choice%}
        {%if trim($choice)%}
            <label><input {%if isset($answers[$i]) && $answers[$i]==$j+1%}checked="checked"{%/if%} name="q{%$i+1%}" type="radio" value="{%$j+1%}">{%$choice|escape%}</label>
        {%/if%}
    {%/foreach%}
</div>
{%/foreach%}
{%if $step==2%}
<div>
<img src="/captcha">
<input name="captcha" type=text/>
</div>
{%/if%}

<div>
<input type=submit value="提交">
</div>
</form>
<script src="http://faxin.soso.com/scripts/jquery.js"></script>
<script>
var time={%$time%};
setInterval(function(){
        time-=1;
        if(time<=0){
            $("form").submit();
        }
        $("#time").html(time);
},1000);
</script>
</form>
</body>
</html>
