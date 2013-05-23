{%extends "question_base.tpl"%}
{%block name="body"%}
<div class="tips">
{%if $wrong_questions%}
    <div class="tips2">
        <input onclick="location.href='/question';" name="" type="button" class="button2">
    </div>
{%else if $step==1%}
    <div class="tips6">
        <input onclick="location.href='/user';" name="" type="button" class="button6">
        <input onclick="location.href='/question';" name="" type="button" class="button7">
    </div>
{%else if $step==2%}
    <div class="tips7">
        <input onclick="location.href='/user';" name="" type="button" class="button6">
        <input onclick="location.href='/question';" name="" type="button" class="button7">
    </div>
{%else if $step==3%}
    <div class="tips8">
        <input onclick="location.href='/user';" name="" type="button" class="button6">
        <input onclick="location.href='/question';" name="" type="button" class="button8">
    </div>
{%/if%}
</div>

{%if $wrong_questions%}
<div class="shadow"></div>
{%/if%}

<div class="dt_main">
    <div class="center">
        <div class="answer">
        <p class="title">您的答题结果如下：</p>
        {%foreach $results as $result%}
        <span>{%$result@index%}：{%if $result%}√{%else%}×{%/if%}</span>
        {%/foreach%}
        </div>
    </div>
</div>
{%/block%}

