
		<div class="page-sidebar nav-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->        
			<ul class="page-sidebar-menu">
				<li>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler hidden-phone"></div>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				</li>
				<li>
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                    <!--
					<form class="sidebar-search">
						<div class="input-box">
							<a href="javascript:;" class="remove"></a>
							<input type="text" placeholder="Search..." />
							<input type="button" class="submit" value=" " />
						</div>
					</form>
                    -->
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>
				<li class="start {%if $executeInfo.controllerName=='Index'%} active {%/if%}">
					<a href="/">
					<i class="icon-home"></i> 
                    <span class="title">首页</span>
					<span class="selected"></span>
					</a>
				</li>
				<li class="start {%if $executeInfo.controllerName=='SystemUserAdmin'%} active {%/if%}">
					<a href="/SystemUserAdmin">
					<i class="icon-home"></i> 
                    <span class="title">系统用户</span>
					<span class="selected"></span>
					</a>
				</li>
				<li class="">
					<a href="javascript:;">
					<i class="icon-cogs"></i> 
					<span class="title">旅行社管理</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						<li>
							<a href="/RemoteTravelAgency">
                                发团社
					            <span class="arrow "></span>
                            </a>
                            <ul class="sub-menu">
                                <li >
                                    <a href="/TravelTeam">
                                        添加
                                    </a>
                                </li>
                                <li >
                                    <a href="/Tourist">
                                        修改
                                    </a>
                                </li>
                                <li >
                                    <a href="/Tourist">
                                        查询
                                    </a>
                                </li>
                            </ul>
						</li>
						<li>
							<a href="/LocalTravelAgency">
                                地接社
					            <span class="arrow "></span>
                            </a>
                            <ul class="sub-menu">
                                <li >
                                    <a href="/TravelTeam">
                                        添加
                                    </a>
                                </li>
                                <li >
                                    <a href="/Tourist">
                                        修改
                                    </a>
                                </li>
                                <li >
                                    <a href="/Tourist">
                                        查询
                                    </a>
                                </li>
                            </ul>
						</li>
					</ul>
				</li>
				<li class="">
					<a href="javascript:;">
					<i class="icon-bookmark-empty"></i> 
					<span class="title">旅行团管理</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						<li >
							<a href="/TravelTeam">
                                旅行团
                            </a>
						</li>
						<li >
							<a href="/Tourist">
                                全部游客
                            </a>
						</li>
					</ul>
				</li>
				<li class="">
					<a href="/TravelNode">
					<i class="icon-table"></i> 
					<span class="title">景点管理（收费项目）</span>
					</a>
				</li>
				<li class="">
					<a href="javascript:;">
					<i class="icon-briefcase"></i> 
					<span class="title">面向旅行社界面(对外)</span>
					<span class="arrow "></span>
					</a>
					<ul class="sub-menu">
						<li >
							<a href="/Agency/Login">
                                旅行社登录
                            </a>
						</li>
						<li >
							<a href="/Agency/TravelTeam">
                                旅行团管理（新建旅行团，查看历史）
                            </a>
						</li>
						<li >
							<a href="/Agency/Tourist">
                                修改自己的信息
                            </a>
						</li>
					</ul>
				</li>
				<li class="">
					<a href="javascript:;">
					<i class="icon-gift"></i> 
					<span class="title">b2c用户管理</span>
					</a>
				</li>
				<li class="">
					<a href="javascript:;">
					<i class="icon-gift"></i> 
					<span class="title">b2c商品种类管理</span>
					</a>
				</li>
				<li>
					<a class="active" href="javascript:;">
					<i class="icon-sitemap"></i> 
					<span class="title">b2c订单列表</span>
					</a>
				</li>
				<li class="last">
					<a class="active" href="javascript:;">
					<i class="icon-sitemap"></i> 
					<span class="title">b2c电子票管理</span>
					</a>
				</li>
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
