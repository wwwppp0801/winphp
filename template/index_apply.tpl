{%extends "index_framework.tpl"%}
{%block name="title"%}
<title>填写申请</title>
{%/block%}


{%block name="content"%}
		<div class="content">
        <form action="/index/addApply" method="post">
			<div class="left">
            	<div class="title"><span class="icon"></span>申请说明：</div>
                <span class="list">请完整填写以下必选信息，申请结果会在活动结束后直接公布在《达人榜》</span>
            	<div class="title"><span class="icon"></span>填写内容:</div>
                <span class="list"><span class="num">1、</span>（必选）您的手机号码（工作人员将通过这种方式通知您获奖，手机号码不会对外公布，请放心填写。）:</span>
                <span class="list"><input value="{%$phone|escape%}" name="phone" type="text" class="input1" /></span>
                <span class="list"><span class="num">2、</span>（必选）您的电子邮箱：</span>
                <span class="list"><input value="{%$email|escape%}" name="email" type="text" class="input1" /></span>
                <span class="list"><span class="num">3、</span>（必选）您对养乐多产品的是否了解（了解或者不了解）</span>
                <span class="list"><input value="{%$awareness|escape%}" name="awareness" type="text" class="input1" /></span>
            </div>
			<div class="right">
                <span class="list"><span class="num">4、</span>（必选）请提供您亲自在搜搜问问下面的"肠道健康"标签里面回答过的关于肠道健康相关问题的标题和链接，最少1条，最多3条.</span>
                <span class="list"><span class="num">标题一：</span><input name="question_title1" type="text" class="input1" /></span>
                <span class="list"><span class="num">链接一：</span><input name="question_url1" type="text" class="input1" /></span>
                <span class="list"><span class="num">标题二：</span><input name="question_title2" type="text" class="input1" /></span>
                <span class="list"><span class="num">链接二：</span><input name="question_url2" type="text" class="input1" /></span>
                <span class="list"><span class="num">标题三：</span><input name="question_title3" type="text" class="input1" /></span>
                <span class="list"><span class="num">链接三：</span><input name="question_url3" type="text" class="input1" /></span>
                <span class="list"><span class="num"></span><input type="submit" value="提交" class="button1" /></span>
            </div>
        </form>
            <div class="clear"></div>
		</div>
{%/block%}

