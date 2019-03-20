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

<div class="list-div" id="listDiv">
	<table width="100%" cellspacing="1" cellpadding="2" id="list-table">
	  <tr>
	    <th>分类名称</th>
	    <th>商品数量</th>
	    <th>是否显示</th>
	    <th>排序</th>
	    <th>操作</th>
	  </tr>
	  
	  <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr align="center" rel="<?php echo ($vo["level"]); ?>" id="<?php echo ($vo["id"]); ?>" class="category_list" id="<?php echo ($vo["id"]); ?>">
		  	<td align="left" class="cat_name">
		  		<?php if($vo["children"] == 1): ?><img src="__PUBLIC__/Images/menu_minus.gif" width="9" onclick="rowClicked(<?php echo ($vo["id"]); ?>,<?php echo ($vo["level"]); ?>)" height="9" border="0" style="cursor:pointer;margin-left:<?php echo ($vo['level']); ?>em" />
				<?php else: ?>
					<img src="__PUBLIC__/Images/menu_minus.gif" width="9" onclick="rowClicked(<?php echo ($vo["id"]); ?>,<?php echo ($vo["level"]); ?>)" height="9" border="0" style="cursor:pointer;margin-left:<?php echo ($vo['level']); ?>em" /><?php endif; ?>
				<a href="__APP__/Goods/index/id/<?php echo ($vo["id"]); ?>"><?php echo ($vo["cat_name"]); ?></a></td>
			<td><?php echo ($vo["goods_number"]); ?></td>
			<td>
				<?php if($vo["is_show"] == 1): ?><img src="__PUBLIC__/Images/yes.gif" />
				<?php else: ?>
					<img src="__PUBLIC__/Images/no.gif" /><?php endif; ?>
			</td>
			<td><?php echo ($vo["sort_order"]); ?></td>
			<td>
				<a href='__URL__/categoryInfo/id/<?php echo ($vo["id"]); ?>'>编辑</a>&nbsp;|&nbsp;
				<a href='javascript:void' onclick="remove_category(<?php echo ($vo["id"]); ?>)">删除</a>
			</td>
		  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
	 </table>
</div>
<script type="text/javascript"> 
	 $(function(){
	 	$("#list-table .category_list").hover(function(){
			var i = $("#list-table .category_list").index($(this));
			$("#list-table .category_list").eq(i).css('background','red');
		});
	 });

	//点击显示、隐藏下级分类
	function rowClicked(id, level){
		var cat = $('#'+id).nextAll();	
		if($('#'+id+' .cat_name img').attr('src') == "__PUBLIC__/Images/menu_minus.gif"){
			$('#'+id+' .cat_name img').attr('src','__PUBLIC__/Images/menu_plus.gif');					
		}else{
			$('#'+id+' .cat_name img').attr('src','__PUBLIC__/Images/menu_minus.gif');
		}
		
		for(var i=0; i<cat.length; i++){	
			if(cat.eq(i).attr('rel') >level){
				cat.eq(i).toggle(0);
			}else{
				return;
			}	
		}
	}
	
	//删除指定分类
	function remove_category(id){
		var con = confirm("您真的确定要删除吗？删除后就无法恢复。");
		if(!con){
			return;
		}
	
		loading();
		var url = '__APP__/Category/delCategory/id/'+id;
		var data = {id:id};
		$.post(url, data, function(data){
			complete_loading();
			if(data.state == 0){ //删除成功
				$("#"+id).remove()
			}else{  //删除失败
				alert(data.info);
			}
		},"JSON");
	}
	
</script>

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