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

<link type="text/css" href="__PUBLIC__/Js/calendar/calendar.css" />
<script type="text/javascript" src="__PUBLIC__/Js/calendar/calendar.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/utils.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/selectzone.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/admin/transport.js"></script>

<script type="text/javascript" src="__PUBLIC__/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="__PUBLIC__/ueditor/ueditor.all.js"></script>
<script type="text/javascript" src="__PUBLIC__/ueditor/lang/zh-cn/zh-cn.js"></script>

<div class="tab-div">
  
    <!-- tab body -->
    <div id="tabbody-div">
      <form enctype="multipart/form-data" action="__APP__/Goods/saveGoods" method="post" name="theForm" onsubmit="return validate();">
        <!-- 最大文件限制 -->
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
 		<table width="90%" id="general-table" align="center">
        <tr >
            <td class="label">商品名称:</td>
            <td>
            	<input type="text" name="goods_name" id="goods_name" value="<?php echo ($goods["goods_name"]); ?>"  size="30" />
				<span style="color:red" id="goods_name_warning"></span>
           </td>
         </tr>
		 <tr>
            <td class="label">商品货号:</td>
            <td>
            	<input type="text" name="goods_sn" id='goods_sn' value="<?php echo ($goods["goods_sn"]); ?>" readonly  size="20" />
           </td>
         </tr>
		 <tr>
            <td class="label">商品分类:</td>
            <td>
            	<select name='cat_id'> 
					<option value="0">顶级分类</option><?php echo ($goods["option"]); ?>
				</select>
           </td>
         </tr>
		  <tr>
            <td class="label">本店售价:</td>
            <td>
            	<input type="text" name="shop_price" id='shop_price' value="<?php echo ($goods["shop_price"]); ?>"  size="20" />
           		<input type="button" value="按市场价计算" onclick="calculate_by_market_price()" style="cursor:pointer" />
		   </td>
         </tr>
		 <tr>
            <td class="label">市场售价:</td>
            <td>
            	<input type="text" name="market_price" id='market_price' value="<?php echo ($goods["market_price"]); ?>"  size="20" />
		   </td>
         </tr>
         <tr>
            <td class="label">点击量:</td>
            <td>
            	<input type="text" name="click_count" id='lick_count' value="<?php echo ($goods["click_count"]); ?>"  size="20" />
           </td>
         </tr>
		 <tr>
            <td class="label">排序:</td>
            <td>
            	<input type="text" name="sort_order" id='sort_order' value="<?php echo ($goods["sort_order"]); ?>"  size="20" />
           </td>
         </tr>
		 <tr>
            <td class="label">销量:</td>
            <td>
            	<input type="text" name="sale_number" id='sale_number' value="<?php echo ($goods["sale_number"]); ?>"  size="20" />
           </td>
         </tr>
		 <tr>
            <td class="label">商品数量:</td>
            <td>
            	<input type="text" name="goods_number" id='goods_number' value="<?php echo ($goods["goods_number"]); ?>"  size="20" />
           </td>
         </tr>
		 
		 <tr>
            <td class="label">报警数量:</td>
            <td>
            	<input type="text" name="warn_number" id='warn_number' value="<?php echo ($goods["warn_number"]); ?>"  size="20" />
           </td>
         </tr>
		 
		 <tr>
            <td class="label">购买些商品时赠送积分:</td>
            <td>
            	<input type="text" name="integral" id='integral' value="<?php echo ($goods["integral"]); ?>"  size="20" />
           </td>
         </tr>

		<tr>
            <td class="label">上架:</td>
            <td>
            <?php if(($goods["is_on_sale"]) == "0"): ?><input type="radio" checked name="is_on_sale" value="0" />
			<?php else: ?>
				<input type="radio" name="is_on_sale" value="0" /><?php endif; ?>否			
			<?php if(($goods["is_on_sale"]) == "1"): ?><input type="radio" checked name="is_on_sale" value="1" />
			<?php else: ?>
				<input type="radio" name="is_on_sale" value="1" /><?php endif; ?>是
           </td>
         </tr>
	
		<tr>
            <td class="label">新品:</td>
            <td>
            <?php if(($goods["is_new"]) == "0"): ?><input type="radio" checked name="is_new" value="0" />
			<?php else: ?>
				<input type="radio" name="is_new" value="0" /><?php endif; ?>否			
			<?php if(($goods["is_new"]) == "1"): ?><input type="radio" checked name="is_new" value="1" />
			<?php else: ?>
				<input type="radio" name="is_new" value="1" /><?php endif; ?>是
           </td>
         </tr>
		 
		 <tr>
            <td class="label">推荐此商品:</td>
            <td>
	            <?php if(($goods["is_recommend"]) == "0"): ?><input type="radio" checked name="is_recommend" value="0" />
				<?php else: ?>
					<input type="radio" name="is_recommend" value="0" /><?php endif; ?>否			
				<?php if(($goods["is_recommend"]) == "1"): ?><input type="radio" checked name="is_recommend" value="1" />
				<?php else: ?>
					<input type="radio" name="is_recommend" value="1" /><?php endif; ?>是
           </td>
         </tr>
		 <tr style="display:none">
            <td class="label">促销:</td>
            <td>
	            <?php if(($goods["is_promote"]) == "0"): ?><input type="checkbox" id="is_promote" name="is_promote" value="1" />
				<?php else: ?>
					<input type="checkbox" id='is_promote' checked name="is_promote" value="1" /><?php endif; ?>
           </td>
         </tr>
		 
		 <tr style="display:none">
            <td class="label">促销价钱:</td>
            <td>
	            <input type="text" name="promote_price" size="20" id="promote_price" value="<?php echo ($goods["promote_price"]); ?>">	
				<span class="notice-span" id="noticeUserPrice">只有选中促销时才生效</span>	
           </td>
         </tr>
		 <tr style="display:none">
            <td class="label">促销日期:</td>
            <td>
	            <input type="text" name="promote_start_time" size="10" id="promote_start_time" value="<?php echo ($goods["promote_start_time"]); ?>">	----				
	            <input type="text" name="promote_end_time" size="10" id="promote_end_time" value="<?php echo ($goods["promote_end_time"]); ?>">		
           		<span class="notice-span" id="noticeUserPrice">只有选中促销时才生效，日期格式为：<b>2014-02-16</b></span>	
		   </td>
         </tr>
		 
		 <tr>
            <td class="label">上传商品图片:</td>
            <td>            	
	            <input type="file" name="goods_img" id='goods_img' size="20" value="">
				<?php if(empty($goods["goods_img"])): ?><img src="__PUBLIC__/Images/no.gif" />
				<?php else: ?>
					<img src="__PUBLIC__/Images/yes.gif" /><?php endif; ?>		
           </td>
         </tr>
		 
		 <tr>
            <td class="label">商品关键字:</td>
            <td>
            	<input type="text" name="keywords" id='keywords' value="<?php echo ($goods["keywords"]); ?>"  size="40" />用空格分隔
           </td>
         </tr>
		 
		 <tr>
            <td class="label">商品简单描述:</td>
            <td>
            	<textarea name="goods_brief" id='goods_brief' cols="40" rows="5"><?php echo ($goods["goods_brief"]); ?></textarea>
           </td>
         </tr>
		 
		 <tr>
            <td class="label">商品详细描述:</td>
            <td>
            	<!--<textarea name="goods_desc" cols="40" rows="5"><?php echo ($goods["goods_desc"]); ?></textarea>-->
            	<script id="goods_desc" name="goods_desc" type="text/plain" ><?php echo ($goods["goods_desc"]); ?></script>
           </td>
         </tr>
    	</table>      
        <div class="button-div">
          <input type="hidden" name="goods_id" value="<?php echo ($goods["id"]); ?>" />
          <input type="hidden" name="action" value="<?php echo ($action); ?>" />
          <input type="reset" value="重置" class="button"  style="background:url(__PUBLIC__/Images/button_bg.gif) repeat-x;" />
           <input type="submit" value="确定" class="button"  style="background:url(__PUBLIC__/Images/button_bg.gif) repeat-x;" />
		</div>
      </form>
    </div>
</div>
<!-- end goods form -->
<script type="text/javascript">
	$(function(){
		var shop_price = $('#shop_price');
		shop_price.blur(function(){
			var rate = <?php echo ($goods["market_price_rate"]); ?>;
			var price = FormatAfterDotNumber(this.value*rate,2);
			$('#market_price').val(price);
		});
		
		$('#promote_start_time').blur(function(){
			var val = this.value;
			var reg = /^([\d]{4})-([\d]{2})-([\d]{2})$/;
			if(!reg.test(val) && val){
				alert('开始促销日期输入不合法');	
			}
		})
		
		$('#promote_end_time').blur(function(){
			var val = this.value;
			var reg = /^([\d]{4})-([\d]{2})-([\d]{2})$/;
			if(!reg.test(val) && val){
				alert('结束促销日期输入不合法');	
			}
		})
	});
	
	//本店价格通过高地价格计算机
	function calculate_by_market_price(){
		var rate = <?php echo ($goods["market_price_rate"]); ?>;
		var market_price = $('#market_price');
		if(market_price.val() == 0){
			$('#shop_price').val(0);
		}else{
			var price = FormatAfterDotNumber(market_price.val()/rate, 2);
			$('#shop_price').val(price);
		}
	}
	
	
	var config = {'initialFrameWidth':700,'initialFrameHeight':300,toolbars:[
            ['fullscreen', 'source', '|', 'undo', 'redo', '|',
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc', '|',
                'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
                'directionalityltr', 'directionalityrtl', 'indent', '|',
                'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
                'link', 'unlink', 'anchor', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
                'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'gmap', 'insertframe','insertcode', 'webapp', 'pagebreak', 'template', 'background', '|',
                'horizontal', 'date', 'time', 'spechars', 'snapscreen', 'wordimage', '|',
                'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
                'print', 'preview', 'searchreplace', 'help', 'drafts']
        ]};
    var editor = UE.getEditor('goods_desc', config);
    
	function validate(){
		var goods_name = $('#goods_name').val();
		if(!goods_name){
			alert('商品名称不能为空');
			return false;
		}
	}
</script>