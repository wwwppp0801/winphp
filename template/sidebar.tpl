<div class="page-sidebar nav-collapse collapse">
	<ul  class="page-sidebar-menu">
		<li {%if $executeInfo.controllerName=='Index'%} class="active" {%/if%}><a href="/admin/Index"><i class="icon icon-home"></i>首页</a></li>
		<li {%if $executeInfo.controllerName == 'Resource'%}class="active"{%/if%}><a href="/admin/Resource"><i class="icon icon-th-list"></i>素材管理</a></li>
		<li {%if $executeInfo.controllerName == 'ResponseRule'%}class="active"{%/if%}><a href="/admin/ResponseRule"><i class="icon icon-th-list"></i>规则管理</a></li>
	    <li {%if $executeInfo.controllerName=='ManageSystemUser'%} class="active" {%/if%}><a href="/ManageSystemUser"><i class="icon icon-th-list"></i>用户管理</a></li>
	</ul>
</div>

