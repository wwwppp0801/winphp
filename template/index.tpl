{%extends "admin/framework.tpl"%}
{%block name="content"%}
<div class="container-fluid">
    <div class="row-fluid">
        <div class="news-item-page span8">

            <h3>左侧菜单，除了<b>“系统用户”</b>均不可用<h3>
                    
                    
            <h4>每一类信息的管理界面，都会和“系统用户”类似，在一个页面里实现“增删改查”<h4>

            <p>
                <h4>分为四个部分：</h4>
                <ol>
                    <li>
                        旅行社信息收集
                        <ul>
                            <li>
                               旅行社登录
                            </li>
                            <li>
                               旅行团管理（新建旅行团，查看历史）
                            </li>
                            <li>
                               修改自己的信息
                            </li>
                        </ul>
                    </li>
                    <li>
                        旅行社信息后台管理
                        <ul>
                            <li>
                               系统用户（管理可以登录系统的帐号）
                            </li>
                            <li>
                               旅行社管理
                            </li>
                            <li>
                               旅行团管理（包括编辑旅行团经过的景点，旅行团）
                            </li>
                            <li>
                               景点管理
                            </li>
                        </ul>
                    </li>
                    <li>
                        b2c网站前端
                        <ul>
                            <li>
                               注册页
                            </li>
                            <li>
                               登录页
                            </li>
                            <li>
                               首页（商品列表页）
                            </li>
                            <li>
                               商品详情页
                            </li>
                            <li>
                                我的订单（查看已买到的电子票号）
                            </li>
                            <li>
                                <del>下订单页（填写订单信息，地址、电话等）</del>这个版本不包含需要支付的商品，所以不需下订单，直接购买即可，下同
                            </li>
                            <li>
                                <del>支付页</del>
                            </li>
                        </ul>
                    </li>
                    <li>
                        b2c网站后台管理
                        <ul>
                            <li>
                               用户
                            </li>
                            <li>
                               商品种类
                            </li>
                            <li>
                               订单
                            </li>
                            <li>
                               电子票
                            </li>
                        </ul>
                    </li>
                </ol>
                </p>

            

        </div>
    </div>
</div>
{%/block%}
