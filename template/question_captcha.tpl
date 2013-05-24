{%extends "question_base.tpl"%}
{%block name="body"%}
<div class="tips">
    <div class="tips10">
        <form action="/question/checkCaptcha" class="form1">
            <input name="captcha" type="text" class="input3" />
            <img src="/captcha" alt="" class="img" />
            <input value="" type="submit" class="button1" style="margin-right:130px;" />
        </form>
    </div>
</div>

<div class="dt_main">
    <div class="center">
    </div>
</div>
{%/block%}
