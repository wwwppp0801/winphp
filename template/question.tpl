{%extends "question_base.tpl"%}
{%block name="body"%}
{%*
	<div class="tips">
    	<div class="tips1">
            <form class="form" action="" method="get">
                <input name="" type="text" class="input1" /><br />
                <input name="" type="text" class="input1" /><br />
                <select name="" class="select1">
                  <option value="1">北京</option>
                  <option value="1">天津</option>
                  <option value="1">上海</option>
                  <option value="1">成都</option>
                  <option value="1">武汉</option>
                  <option value="1">四川</option>
                  <option value="1">宁夏</option>
                </select><br />
                <input name="" type="button" class="button1" />
            </form>
        </div>
    	<div class="tips2" style="display:none;">
            <input name="" type="button" class="button2" />
        </div>
    	<div class="tips3" style="display:none;">
            <input name="" type="button" class="button3" /><input name="" type="button" class="button4" />
        </div>
    	<div class="tips4" style="display:none;">
            <input name="" type="button" class="button3" /><input name="" type="button" class="button5" />
        </div>
    	<div class="tips5" style="display:none;">
            <img src="/images/dc1.gif" alt="" />
        </div>
    	<div class="tips5" style="display:none;">
            <img src="/images/dd1.gif" alt="" />
        </div>
    	<div class="tips5" style="display:none;">
            <img src="/images/jt.gif" alt="" />
        </div>
    	<div class="tips6" style="display:none;">
            <input name="" type="button" class="button6" /><input name="" type="button" class="button7" />
        </div>
    	<div class="tips7" style="display:none;">
            <input name="" type="button" class="button6" /><input name="" type="button" class="button7" />
        </div>
    	<div class="tips8" style="display:none;">
            <input name="" type="button" class="button6" /><input name="" type="button" class="button8" />
        </div>
    	<div class="tips9" style="display:none;">
            <input name="" type="button" class="button9" />
        </div>
    </div>
	<div class="shadow"></div>
*%}
	<div class="dt_main">
    	<div id="time" class="time">{%$time%}</div>
        <div class="center">
{%foreach range($step+1,3) as $i%}
        	<div class="step{%$i%}"></div>
{%/foreach%}
{%for $i=4;$i<=3+$step;$i++%}
        	<div class="step{%$i%}"></div>
{%/for%}
        	<div class="begin zb_1"></div>
<form action="/question/answer">
{%foreach $questions as $i=>$question%}
            <div class="q"{%if $i!=0%} style="display:none"{%/if%}>
            	<p class="title">{%$question.title|escape%} {%$question.answer%}</p>
    {%foreach $question.choices as $j=>$choice%}
        {%if trim($choice)%}
                    <a href="javascript:;">{%chr(65+$j)%}：{%$choice|escape%}</a>
        {%/if%}
    {%/foreach%}
                <input name="q{%$i+1%}" type="hidden" value="">
            </div>
{%/foreach%}
</form>
{%*
            <div class="answer">
            	<p class="title">您的答题结果如下：</p>
            	<span>1：√</span>
            	<span>2：√</span>
            	<span>3：×</span>
            	<span>4：√</span>
            	<span>5：√</span>
            	<span>6：√</span>
            	<span>7：√</span>
            	<span>8：√</span>
            	<span>9：√</span>
            </div>
*%}
        </div>
{%foreach $questions as $i=>$question%}
    	<a {%if $i!=0%} style="display:none"{%/if%} href="http://wenwen.soso.com/z/Search.e?sp={%urlencode($question.title)%}" target="_blank" class="search" title="搜搜找答案"></a>
{%/foreach%}
    </div>
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

var divs=$(".q");
var searches=$(".search");
divs.each(function(i,div){
    var input=$(div).find("input");
    var links=$(div).find("a");
    links.each(function(j,link){
        $(link).click(function(){
            input.val(j+1);
            if(divs.eq(i+1).size()>0){
                $(div).hide();
                divs.eq(i+1).show();
                searches.eq(i).hide();
                searches.eq(i+1).show();
            }else{
                $("form").submit();
            }
        });
    });
});
</script>
{%/block%}
