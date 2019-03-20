<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>东方商城后台管理中心</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/global.css" />
<link href="__PUBLIC__/Css/general.css" rel="stylesheet" type="text/css" />
<link href="__PUBLIC__/Css/main.css" rel="stylesheet" type="text/css" />
<link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/lanrenzhijia.css" />
<link rel="Shortcut Icon" href="__PUBLIC__/Images/favicon.ico" />
<script type="text/javascript" src="__PUBLIC__/Js/jquery-1.7.2.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/lanrenzhijia.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/admin/admin.js"></script>

</head>
<style type='text/css'>
	td{color:#fff;}
	.button{width:75px;line-height:30px;background:#ddd}
	.button:hover{background:url(__PUBLIC__/Images/button_bg.gif) repeat-x;}x
</style>
<body style="background: #278296">
<form method="" action="" name='theForm'>
  <table cellspacing="0" cellpadding="0" style="margin-top: 100px" align="center">
  <tr>
    <td><img src="__PUBLIC__/Images/admin_logo2.gif" width="178" height="256" border="0" alt="东方商城" /></td>
    <td style="padding-left: 50px">
      <table>
      <tr>
        <td>用户名：</td>
        <td><input type="text" name="username" id='username'  onkeyup="isEnter(event)" /></td>
      </tr>
      <tr>
        <td style='text-align:right;'>密码：</td>
        <td><input type="password" name="password" id='password' onkeyup="isEnter(event)" /></td>
      </tr>
      <tr>
      <td colspan="2" align="right">
      </td>
      </tr>
      <tr><td>&nbsp;</td><td><input type="button" value="登录" class="button" onclick="login()" /></td></tr>
  	  <tr>
  	  	<td>&nbsp;</td><td align="left">&raquo; <a href="<?php echo ($host); ?>" style="color:white">返回首页</a></td>
      </tr>
      </table>
    </td>
  </tr>
  </table>

</form>
</body>