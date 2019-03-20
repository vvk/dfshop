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



<div class="main-div">
	<form action="__URL__/saveCategory" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
	<table width="100%" id="general-table">
		<tr>
	        <td class="label">分类名称:</td>
	        <td>
	          <input type='text' name='cat_name' x-webkit-speech id="d1" lang="zh-CN" x-webkit-grammar="bUIltin:search" maxlength="20" value='<?php echo ($category["cat_name"]); ?>' size='27' /> <font color="red">*</font>
	          <span class="notice-span" id=""></span>	
			</td>
       </tr>
	   
	   <tr>
        <td class="label">上级分类:</td>
        <td>
          <select name="parent_id">
            <option value="0">顶级分类</option>
            	<?php echo ($category["option"]); ?>
          </select>
        </td>
      </tr>

	 <tr id="measure_unit">
        <td class="label">排序:</td>
        <td>
          <input type="text" name='sort_order' value='<?php echo ($category["sort_order"]); ?>' size="12" />
        </td>
      </tr>
	  
	  <tr id="measure_unit">
        <td class="label">是否显示:</td>
        <td>
          <?php if(($category["is_show"]) == "0"): ?><input type="radio" checked name="is_show" value="0" />
			<?php else: ?>
				<input type="radio" name="is_show" value="0" /><?php endif; ?>否			
			<?php if(($category["is_show"]) == "1"): ?><input type="radio" checked name="is_show" value="1" />
			<?php else: ?>
				<input type="radio" name="is_show" value="1" /><?php endif; ?>是
        </td>
      </tr>
	  
	  <tr id="measure_unit">
        <td class="label">关键字:</td>
        <td>
          <input type='text' name='keywords'  value='<?php echo ($category["keywords"]); ?>' size='40' />
        </td>
      </tr>
	  
	  <tr id="measure_unit">
        <td class="label">分类描述:</td>
        <td>
          <textarea name="cat_desc" cols="40" rows="5"><?php echo ($category["cat_desc"]); ?></textarea>
        </td>
      </tr>
	</table>
	<div class="button-div">
		<input type="hidden" name="id" value="<?php echo ($category["id"]); ?>" />
		<input type="hidden" name='action' value="<?php echo ($action); ?>" />
        <input type="submit" value="确定" style="cursor:pointer;" />
        <input type="reset" value="重置" style="cursor:pointer;"/>
      </div>
	
	</form>
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