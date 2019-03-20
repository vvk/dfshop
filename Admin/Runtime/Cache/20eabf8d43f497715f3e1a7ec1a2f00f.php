<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?php echo ($web_title); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="__PUBLIC__/Css/common.css" />
<link rel="stylesheet" href="__PUBLIC__/Css/admin.css" />
<link rel="Shortcut Icon" href="__PUBLIC__/Images/favicon.ico" />
<style>*{margin:0;padding:0}</style>
<frameset rows="80,*" framespacing="0" border="0" style="margin:0px;padding:0px;" class="main_frame">
  <frame src="__URL__/top" id="header-frame" name="header-frame" frameborder="no" scrolling="no">
  <frameset cols="180, 10, *" framespacing="0" border="0" id="frame-body">
    <frame src="__URL__/menu" id="menu-frame" name="menu-frame" frameborder="no" scrolling="yes">
    <frame src="__URL__/drag" id="drag-frame" name="drag-frame" frameborder="no" scrolling="no">
    <frame src="__URL__/main" id="main-frame" name="main-frame" frameborder="no" scrolling="yes">
  </frameset>
</frameset>
<frameset rows="0, 0" framespacing="0" border="0">
  <frame src="http://api.ecshop.com/record.php?mod=login&url=<?php echo ($shop_url); ?>" id="hidd-frame" name="hidd-frame" frameborder="no" scrolling="no">
  </frameset>

</html>