<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ECSHOP Menu</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLIC__/Css/general.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__PUBLIC__/Js/jquery-1.7.2.js"></script>
<style type="text/css">
body {
  background: #80BDCB;
}
#tabbar-div {
  background: #278296;
  padding-left: 10px;
  height: 21px;
  padding-top: 0px;
}
#tabbar-div p {
  margin: 1px 0 0 0;
}
.tab-front {
  background: #80BDCB;
  line-height: 20px;
  font-weight: bold;
  padding: 4px 15px 4px 18px;
  border-right: 2px solid #335b64;
  cursor: hand;
  cursor: pointer;
}
.tab-back {
  color: #F4FAFB;
  line-height: 20px;
  padding: 4px 15px 4px 18px;
  cursor: hand;
  cursor: pointer;
}
.tab-hover {
  color: #F4FAFB;
  line-height: 20px;
  padding: 4px 15px 4px 18px;
  cursor: hand;
  cursor: pointer;
  background: #2F9DB5;
}
#top-div
{
  padding: 3px 0 2px;
  background: #BBDDE5;
  margin: 5px;
  text-align: center;
}
#main-div {
  border: 1px solid #345C65;
  padding: 5px;
  margin: 5px;
  background: #FFF;
}
#menu-list {
  padding: 0;
  margin: 0;
}
#menu-list ul {
  padding: 0;
  margin: 0;
  list-style-type: none;
  color: #335B64;
}
#menu-list li {
  padding-left: 16px;
  line-height: 16px;
  cursor: hand;
  cursor: pointer;
}
#main-div a:visited, #menu-list a:link, #menu-list a:hover {
  color: #335B64
  text-decoration: none;
}
#menu-list a:active {
  color: #EB8A3D;
}
.explode {
  background: url(__PUBLIC__/Images/menu_minus.gif) no-repeat 0px 3px;
  font-weight: bold;
}
.collapse {
  background: url(__PUBLIC__/Images/menu_plus.gif) no-repeat 0px 3px;
  font-weight: bold;
}
.menu-item {
  background: url(__PUBLIC__/Images/menu_arrow.gif) no-repeat 0px 3px;
  font-weight: normal;
}
#help-title {
  font-size: 14px;
  color: #000080;
  margin: 5px 0;
  padding: 0px;
}
#help-content {
  margin: 0;
  padding: 0;
}
.tips {
  color: #CC0000;
}
.link {
  color: #000099;
}
</style>
</head>
<body>
<div id="tabbar-div">
<p><span style="float:right; padding: 3px 5px;" ><a href="javascript:toggleCollapse();"><img id="toggleImg" src="__PUBLIC__/Images/menu_minus.gif" width="9" height="9" border="0" alt="" /></a></span>
  <span class="tab-front" id="menu-tab">菜单</span>
</p>
</div>
<div id="main-div">
<div id="menu-list">
<ul>

  <li class="explode" id="goods"><a href="javascript:toggleCollapse('goods');" target="main-frame">商品管理</a></li>
  <li>    
	<ul>
      <li class="menu-item"><a href="__APP__/Goods/index" target="main-frame">商品列表</a></li>
      <li class="menu-item"><a href="__APP__/Goods/addGoods" target="main-frame">添加商品</a></li>
      <li class="menu-item"><a href="__APP__/Category/index" target="main-frame">商品分类</a></li>
      <li class="menu-item"><a href="__APP__/Goods/index/action/trash" target="main-frame">商品回收站</a></li>
      <li class="menu-item"><a href="__APP__/Goods/goodsComments" target="main-frame">商品评论</a></li>
    </ul>
  </li>  
  
  <li class="explode" id='orders'><a href="javascript:toggleCollapse('orders');" target="main-frame">订单管理</a></li>
  <li>	
	<ul>
      <li class="menu-item"><a href="__APP__/Order" target="main-frame">订单列表</a></li>
      <!--<li class="menu-item"><a href="" target="main-frame">订单查询</a></li>-->
    </ul>
  </li>
  
  <li class="explode" id="article"><a href="javascript:toggleCollapse('article');" target="main-frame">文章管理</a></li>
  <li>	
	<ul>
	  <li class="menu-item"><a href="__APP__/Article/category" target="main-frame">文章分类</a></li>
      <li class="menu-item"><a href="__APP__/Article/index" target="main-frame">文章列表</a></li>
      <li class="menu-item"><a href="__APP__/Article/articleInfo" target="main-frame">添加文章</a></li>
    </ul>
  </li>
  
  <li class="explode" id="user"><a href="javascript:toggleCollapse('user');" target="main-frame">会员管理</a></li>
  <li>	
	<ul>
      <li class="menu-item"><a href="__APP__/User/index" target="main-frame">会员列表</a></li>
      <li class="menu-item"><a href="__APP__/User/userInfo" target="main-frame">添加会员</a></li>
    </ul>
  </li>
  
  <li class="explode" id="sys"><a href="javascript:toggleCollapse('sys');" target="main-frame">系统设置</a></li>
  <li>	
	<ul>
      <li class="menu-item"><a href="__APP__/Config/index" target="main-frame">商店设置</a></li>
      <li class="menu-item"><a href="__APP__/System/nav" target="main-frame">自定义导航</a></li>
      <li class="menu-item"><a href="__APP__/System/pay" target="main-frame">支付方式</a></li>
      <li class="menu-item"><a href="__APP__/System/shipping" target="main-frame">配送方式</a></li>
      <li class="menu-item"><a href="__APP__/System/banner" target="main-frame">首页banner</a></li>
      <li class="menu-item"><a href="__APP__/System/flink" target="main-frame">友情链接</a></li>
      <li class="menu-item"><a href="__APP__/System/hotSearch" target="main-frame">热门搜索关键字</a></li>
      <li class="menu-item"><a href="__APP__/System/editPassword" target="main-frame">修改密码</a></li>
    </ul>
  </li>
  

 
</ul>
</div>
<div id="help-div" style="display:none">
<h1 id="help-title"></h1>
<div id="help-content"></div>
</div>
</div>
<script type="text/javascript">
	function toggleCollapse(id){
		if(typeof id !== 'undefined'){
			$("#"+id).toggleClass('explode');
			$("#"+id).toggleClass('collapse');
			$("#"+id+' + li ul').toggle();
		}else{
			$("#menu-list ul li ul").toggle();
			$("#menu-list ul li").toggleClass('explode');
			$("#menu-list ul li").toggleClass('collapse');
			
			if($('#toggleImg').attr('src') == '__PUBLIC__/Images/menu_minus.gif'){
				$('#toggleImg').attr('src','__PUBLIC__/Images/menu_plus.gif');
			}else{
				$('#toggleImg').attr('src','__PUBLIC__/Images/menu_minus.gif');
			}				
		}
	}
</script>

</body>
</html>