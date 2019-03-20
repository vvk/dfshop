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

<style type="text/css">
	td{text-align:center;}
</style>

<!-- 商品列表 -->
<form method="post" action="" name="listForm" onsubmit="return confirmSubmit(this)">
  <!-- start goods list -->
<div class="list-div" id="listDiv">
<table cellpadding="3" cellspacing="1">
  <tr>
    <th>序号</th>
    <th>商品名称</th>
    <th>货号</th>
    <th>市场价格</th>
    <th>本店价格</th>
    <th>上架</th>
    <th>新品</th>
    <th>推荐</th>
    <th>推荐排序</th>
    <th>库存</th>
    <th>销量</th>
    <th>点击量</th>
    <th>操作</th>
  <tr>
  	<?php if($goods_list): if(is_array($goods_list)): $i = 0; $__LIST__ = $goods_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><tr id="<?php echo ($goods["id"]); ?>">
	  		<td><?php echo ($goods["id"]); ?></td>
			<td><span onclick=""><?php echo ($goods["goods_name"]); ?></span></td>
			<td><span><?php echo ($goods["goods_sn"]); ?></span></td>
			<td><span><?php echo ($goods["market_price"]); ?></span></td>
			<td><span><?php echo ($goods["shop_price"]); ?></span></td>
			<td><span>
					<?php if($goods["is_on_sale"] == 1): ?><img src="__PUBLIC__/Images/yes.gif" title="点击修改" style="cursor:pointer;" onclick="on_sale_toggle(this,<?php echo ($goods["id"]); ?>,'is_on_sale')"/>
					<?php else: ?>
						 <img src="__PUBLIC__/Images/no.gif" title="点击修改" style="cursor:pointer;" onclick="on_sale_toggle(this,<?php echo ($goods["id"]); ?>,'is_on_sale')"/><?php endif; ?>
			</span></td>
			<td><span>
					<?php if($goods["is_new"] == 1): ?><img src="__PUBLIC__/Images/yes.gif" title="点击修改" style="cursor:pointer;" onclick="on_sale_toggle(this,<?php echo ($goods["id"]); ?>,'is_new')"/>
					<?php else: ?>
						 <img src="__PUBLIC__/Images/no.gif" title="点击修改" style="cursor:pointer;" onclick="on_sale_toggle(this,<?php echo ($goods["id"]); ?>,'is_new'),''"/><?php endif; ?>
			</span></td>
			<td><span>
					<?php if($goods["is_recommend"] == 1): ?><img src="__PUBLIC__/Images/yes.gif" title="点击修改" style="cursor:pointer;" onclick="on_sale_toggle(this,<?php echo ($goods["id"]); ?>,'is_recommend')"/>
					<?php else: ?>
						 <img src="__PUBLIC__/Images/no.gif" title="点击修改" style="cursor:pointer;" onclick="on_sale_toggle(this,<?php echo ($goods["id"]); ?>,'is_recommend')"/><?php endif; ?>
			</span></td>
			<td><span><?php echo ($goods["sort_order"]); ?></span></td>
			<td><span><?php echo ($goods["goods_number"]); ?></span></td>
			<td><span><?php echo ($goods["sale_number"]); ?></span></td>
			<td><span><?php echo ($goods["click_count"]); ?></span></td>
			<td>
		   		<a href="<?php echo ($host); ?>index.php/Goods/index/id/<?php echo ($goods["id"]); ?>" target="_blank" title="查看"><img src="__PUBLIC__/Images/icon_view.gif" width="16" height="16" border="0" /></a>
	       	    <a href="__APP__/Goods/editGoods/id/<?php echo ($goods["id"]); ?>" title="编辑"><img src="__PUBLIC__/Images/icon_edit.gif" width="16" height="16" border="0" /></a>
		        <a href="javascript:void(0);" onclick="remove_goods(<?php echo ($goods["id"]); ?>)" title="回收站"><img src="__PUBLIC__/Images/icon_trash.gif" width="16" height="16" border="0" /></a>
			</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
   <?php else: ?>
   		<tr><td colspan='11' align="center">没有找到任何记录</td></tr><?php endif; ?>   
  <tr>
  	<td colspan='13'>
  		 <div class="page"><p><?php echo ($page_list); ?></p></div>	
  	</td>
  </tr>
 
</table>
<!-- end goods list -->
<script type="text/javascript">
	//删除指点商品
	function remove_goods(id){
		var con = confirm("您真的要将此商品放入回收站吗？");
		if(!con){
			return;
		}
		
		loading();
		var url = "__APP__/Goods/remove/action/trash/id"+id;
		var data = {id:id};
		$.post(url, data, function(data){
			if(data == '1'){
				$("#"+id).remove();
				window.location.reload();
				complete_loading();
			}else{
				complete_loading();
				alert('放入回收站失败');
			}
		},'text');
	}
	
	//商品如果是上架则改为下架，如果是下架，刚改为下架
	function on_sale_toggle(obj, id, type){
		loading();
		var url = "__APP__/Goods/onSaleToggle";
		var data = {id:id,type:type};
		$.post(url, data, function(data){
			if(data == '2'){
				var reg = /yes/;
				if(reg.test(obj.src)){
					obj.src = '__PUBLIC__/Images/no.gif';
				}else{
					obj.src = '__PUBLIC__/Images/yes.gif';
				}
				complete_loading();
			}else{
				if(data == 0){
					complete_loading();
					alert('操作失败');
				}else if(data == 1){
					complete_loading();
					alert('商品不存在');
				}
			}
		},'TEXT');		
	}
	
</script>
</div>
<div id="footer">
<div id="popMsg">
  <table cellspacing="0" cellpadding="0" width="100%" bgcolor="#cfdef4" border="0">
  <tr>
    <td style="color: #0f2c8c" width="30" height="24"></td>
    <td style="font-weight: normal; color: #1f336b; padding-top: 4px;padding-left: 4px" valign="center" width="100%"> <?php echo ($lang["order_notify"]); ?></td>
    <td style="padding-top: 2px;padding-right:2px" valign="center" align="right" width="19"><span title="关闭" style="cursor: hand;cursor:pointer;color:red;font-size:12px;font-weight:bold;margin-right:4px;" onclick="Message.close()" >×</span><!-- <img title=关闭 style="cursor: hand" onclick=closediv() hspace=3 src="msgclose.jpg"> --></td>
  </tr>
  <tr>
    <td style="padding-right: 1px; padding-bottom: 1px" colspan="3" height="70">
    <div id="popMsgContent">
      <p><?php echo ($lang["new_order_1"]); ?><strong style="color:#ff0000" id="spanNewOrder">1</strong><?php echo ($lang["new_order_2"]); ?>
      <strong style="color:#ff0000" id="spanNewPaid">0</strong><?php echo ($lang["new_order_3"]); ?></p>
      <p align="center" style="word-break:break-all"><a href="order.php?act=list"><span style="color:#ff0000"><?php echo ($lang["new_order_link"]); ?></span></a></p>
    </div>
    </td>
  </tr>
  </table>
</div>
版权所有 ©&nbsp;&nbsp;&nbsp;&nbsp;copyRight&nbsp;2014&nbsp;&nbsp;东方商城 
</body>
</html>