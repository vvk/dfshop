<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php if($ur_here): echo ($ur_here); ?>-<?php endif; ?>东方商城管理中心</title>
<meta name="robots" content="noindex, nofollow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="__PUBLIC__/Css/general.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/Css/main.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/Css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="__PUBLIC__/Js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/common.js"></script>
</head>
<style type="text/css">
.loading{position:absolute;top:10px;left:0;height:38px;line-height;38px;width:100%;display:none;}
.loading span{width:120px;height:38px;line-height:38px;margin:0 auto;color:#F29708;display:block;
	background:url(__PUBLIC__/Images/loading.gif) no-repeat left center;padding-left:25px;}
</style>
<body>
<div class='loading'><span>正在处理您的请求……</span></div>
<h1>
<?php if($action_link): ?><span class="action-span"><a href="<?php echo ($action_link["href"]); ?>"><?php echo ($action_link["text"]); ?></a></span><?php endif; ?>
<?php if($action_link2): ?><span class="action-span"><a href="<?php echo ($action_link2["href"]); ?>"><?php echo ($action_link2["text"]); ?></a>&nbsp;&nbsp;</span><?php endif; ?>
<span class="action-span1"><a href="__APP__/Index/main">东方商城管理中心</a> </span><span id="search_id" class="action-span1"><?php if($ur_here): ?>- <?php echo ($ur_here); endif; ?></span>
<div style="clear:both"></div>
</h1>


<!-- start order statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title">订单统计信息</th>
  </tr>
  <tr>
    <td width="20%"><a href="__APP__/Order/index/type/1">未付款订单:</a></td>
    <td width="30%"><strong style="color: red"><?php echo ($order["no_pay"]); ?></strong></td>
    <td width="20%"><a href="__APP__/Order/index/type/2">已付款订单:</a></td>
    <td width="30%"><strong><?php echo ($order["pay"]); ?></strong></td>
  </tr>
  <tr>
    <td><a href="__APP__/Order/index/type/3">待发货订单:</a></td>
    <td><strong><?php echo ($order["no_ship"]); ?></strong></td>
    <td><a href="__APP__/Order/index/type/4">已发货订单数:</a></td>
    <td><strong><?php echo ($order["ship"]); ?></strong></td>
  </tr>
</table>
</div><br />
<!-- end order statistics -->

<!-- start goods statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title">商品统计信息</th>
  </tr>
  <tr>
    <td width="20%"><a href="__APP__/Goods">商品总数:</a></td>
    <td width="30%"><strong><?php echo ($goods["total"]); ?></strong></td>
    <td width="20%"><a href="__APP__/Goods/index/type/1">库存警告商品数:</a></td>
    <td width="30%"><strong style="color: red"><?php echo ($goods["warn_number"]); ?></strong></td>
  </tr>
  <tr>
    <td><a href="__APP__/Goods/index/type/2">推荐商品:</a></td>
    <td><strong><?php echo ($goods["recommend"]); ?></strong></td>
    <td><a href="__APP__/Goods/index/type/3">新品:</a></td>
    <td><strong><?php echo ($goods["new"]); ?></strong></td>
  </tr>

</table>
</div><br />
<!-- end goods statistics -->

<!-- start system information -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title">系统信息</th>
  </tr>
  <tr>
    <td width="20%">服务器操作系统:</td>
    <td width="30%"><?php echo ($sys_info["os"]); ?> (<?php echo ($sys_info["ip"]); ?>)</td>
    <td width="20%">Web 服务器:</td>
    <td width="30%"><?php echo ($sys_info["web_server"]); ?></td>
  </tr>
  <tr>
    <td>PHP 版本:</td>
    <td><?php echo ($sys_info["php_ver"]); ?></td>
    <td>MySQL 版本:</td>
    <td><?php echo ($sys_info["mysql_ver"]); ?></td>
  </tr>
  <tr>
    <td>安全模式:</td>
    <td><?php echo ($sys_info["safe_mode"]); ?></td>
    <td>安全模式GID:</td>
    <td><?php echo ($sys_info["safe_mode_gid"]); ?></td>
  </tr>
  <tr>
    <td>Socket 支持:</td>
    <td><?php echo ($sys_info["socket"]); ?></td>
    <td>时区设置:</td>
    <td><?php echo ($sys_info["timezone"]); ?></td>
  </tr>
  <tr>
    <td>GD 版本:</td>
    <td><?php echo ($sys_info["gd"]); ?></td>
    <td>文件上传的最大大小:</td>
    <td><?php echo ($sys_info["max_filesize"]); ?></td>
  </tr>
  <tr>
    <td>Zlib 支持:</td>
    <td><?php echo ($sys_info["zlib"]); ?></td>
    <td>当前语言:</td>
    <td><?php echo ($sys_info["lang"]); ?></td>
  </tr>
  <tr>
    <td>编码:</td>
    <td><?php echo ($sys_info["DEFAULT_CHARSET"]); ?></td>
    <td></td>
    <td></td>
  </tr>
  
  
  
  
  
  
</table>
</div><br />
<!-- end system information -->