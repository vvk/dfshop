<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta http-equiv="keywords" content="<?php echo ($keywords); ?>" />
	<meta http-equiv="description" content="<?php echo ($description); ?>" />
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/global.css" />
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/index.css" />
	<link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/lanrenzhijia.css" />
	<link rel="Shortcut Icon" href="__PUBLIC__/Images/favicon.ico" />
    <link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/dhk.css" />
	<script type="text/javascript" src="__PUBLIC__/Js/jquery-1.7.2.js"></script>
	<script type="text/javascript" src="__PUBLIC__/Js/jquery.lazyload.js"></script>
	<script type="text/javascript" src="__PUBLIC__/Js/lanrenzhijia.js"></script>
	<script type="text/javascript" src="__PUBLIC__/Js/public.js"></script>
	<script type="text/javascript" src="__PUBLIC__/Js/index.js"></script>
	<script type="text/javascript" src="__PUBLIC__/Js/banner.js"></script>
    <script type="text/javascript" src="__PUBLIC__/Js/dhk.js"></script>
	<title><?php echo ($web_title); ?></title>
	<meta property="qc:admins" content="0011510633070631611006375" />
	<meta property="wb:webmaster" content="bb228913ddf9074d" />
</head>
<script type="text/javascript">
	$(function(){
		$("img.lazy").show().lazyload({ //页面加载完毕运行
			effect : "fadeIn",         //淡入效果
	　　　　　　　threshold : 100,        // 提前200像素加载
	　　　　　　　failurelimit : 0,         //随机加载10张不在范围内图片
	　　　　　　　skip_invisible : false      //设置为display：none 的图片 也加载   false否  True是
		});
		
        //商店公告
        onLoad(function (){
        	initQuirkyPopup();
        });
	    $("img").lazyload({
       		  event : "sporty"
     	});
	});
	
</script>
<body>
	<div class="header">
	<div class="headerBanner">
		<div class="bannerCon">
			<div class="webAction">
				<b title='收藏'></b>
				<a href="javascript:AddFavorite(window.location,document.title)" id="collection" title='收藏'>收藏本站</a>
			</div>
			<div class="userAction">
                <?php if($_SESSION['user']!= ''): ?>您好&nbsp;<font style='color:red;font-weight:bold;'><?php echo ($_SESSION['user']['user_name']); ?></font>，欢迎来到东方商城&nbsp;&nbsp;&nbsp;<a href="/user.html" title="个人中心">个人中心</a><a href="javascript:void()" onclick="logout()" class="logout">退出</a><?php endif; ?>
                
                <?php if(empty($_SESSION['user'])): ?>您好，欢迎来到东方商城&nbsp;&nbsp;&nbsp;<a href="/login.html" title="登录">请登录</a>&nbsp;
				   <a href="/register.html" title="注册">免费注册</a>&nbsp;<a href="/user.html" title="个人中心">个人中心</a><?php endif; ?>
			</div>
		</div>
	</div>
	<div class="headerCon">
		<div class="logo"><a href='/'><img src="__PUBLIC__/Uploads/<?php echo ($logo); ?>"/></a></div>
		<div class="search">
			<form>
				<span></span>
				<input type="text" name="search" id="headerSearch" onkeydown="searchGoodsEnter(event)" rel="<?php echo ($search_keywords); ?>" value="<?php echo ($search_keywords); ?>" required x-webkit-speech x-webkit-grammar="builtin:translate">
				<input type="button" name="submit" id="headerSubmit" onclick="searchGoods()" value="搜索">
			</form>
			<div class="hotSearch">
				<dl>
					<dt>热门搜索：</dt>
					<?php if(is_array($hotSearch)): $i = 0; $__LIST__ = $hotSearch;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dd><a href="/search-<?php echo ($vo["keyword"]); ?>.html" title='<?php echo ($vo["keyword"]); ?>' target="_blank"><?php echo ($vo["keyword"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
				</dl>	
			</div>
		</div>
		<div class="cart">
			<span>
				<b class="ico"></b><a href="/cart.html" target='_blank'>去购物车结算</a><em id='cartGoodsNum'>0</em>
				<div class="cartGoods">
					<img src='__PUBLIC__/Images/loading1.gif' id='loadingCartGoods'/>
					<p class="noGoods">您的购物车中没有商品</p>
				</div>
			</span>
		</div>
	</div>
	
	<div class="nav">
		<div class="navBox">
			<ul>
				<li class="currentNav"><a href="<?php echo ($host); ?>/">首页</a></li>			
                <?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo["opennew"] == '0'): ?><li><a href="<?php echo ($host); echo ($vo["url"]); ?>"><?php echo ($vo["name"]); ?></a></li>
                    <?php else: ?>
                        <li><a href="<?php echo ($host); echo ($vo["url"]); ?>" target="_blank"><?php echo ($vo["name"]); ?></a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
	</div>
</div>
<!-- 在线客服 -->
<div id="online_service_bar">
	<div id="online_service_minibar"></div>
	<div id="online_service_fullbar">
		<div class="service_bar_head"><span id="service_bar_close" title="点击关闭">点击关闭</span></div>
		<div class="service_bar_main">
			<ul class="service_menu">
				<li class="hover">
					<dl>
						<dd>
					<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo ($serviceQQ); ?>&site=qq&menu=yes"><img width="74" height="22" border="0" src="http://wpa.qq.com/pa?p=2:123456:41"  title="东方商城在线客服" /></a>
					</dd>
					</dl>
				</li>
			</li>
			</ul>
		</div>
	</div>
</div>
<script type="text/javascript">
var default_view = 0; <!--1是默认展开，0是默认关闭，新开窗口看效果，别在原页面刷新-->
</script>
<link type="text/css" rel="stylesheet" href="__PUBLIC__/Css/onlineService.css" />
<script type="text/javascript" src="__PUBLIC__/Js/onlineService.js"></script>
<!-- 在线客服 -->


	<div class="main">
		<div class="floor1">
			<!-- 鍒嗙被鏍�-->
<div class="cat">
	<ul>
        <?php if(is_array($category)): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="catList" style="padding-left: 20px;">
                <?php if(is_array($vo["cat"])): $i = 0; $__LIST__ = $vo["cat"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cat): $mod = ($i % 2 );++$i;?><a href="/category-<?php echo ($cat["id"]); ?>-tree.html" target="_blank"><?php echo ($cat["cat_name"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?><span></span>
                <div class="subMenu">
                    <ul>
                        <?php if(is_array($vo["goods"])): $i = 0; $__LIST__ = $vo["goods"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$goods): $mod = ($i % 2 );++$i;?><li><a href="/goods-<?php echo ($goods["id"]); ?>.html" target="_blank" title='<?php echo ($goods["goods_name"]); ?>'><?php echo ($goods["goods_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
      </ul>
</div>
  <!-- 分类树 -->
			
			<div class="floor1Con">
				<div id="banner">
					<ul id="bannerImg">
                        <?php if(is_array($banner)): $i = 0; $__LIST__ = $banner;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><img src="<?php echo ($host); echo ($vo["banner_url"]); ?>" width='780' height='330' /></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
					<ul id="imgNum">
					</ul>
				</div>
			</div>
			<div class="clear"></div>
		</div>	
		
		<div class="floor2">
			<div class="floor2Title">
				<ul>
					<li class="item">新品上架</li>
					<li>推荐商品</li>
					<li>热销商品</li>
				</ul>
			</div>
			<div class="floorGoods">
				<!-- 新口上架 start -->
				<ul id="newGoods">
					<?php if(is_array($newGoods)): $i = 0; $__LIST__ = $newGoods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<dl>
								<dd><a href="/goods-<?php echo ($vo["id"]); ?>.html" target='_blank'><img src="__PUBLIC__/Images/grey.gif" data-original="<?php echo ($host); echo ($vo["goods_thumb"]); ?>" class='lazy transitionAll' title="<?php echo ($vo["goods_name"]); ?>" alt='<?php echo ($vo["goods_name"]); ?>'></a></dd>
								<dt>
									<p><a href="/goods-<?php echo ($vo["id"]); ?>.html" target='_blank' title="<?php echo ($vo["goods_name"]); ?>"><?php if($vo.goods_name|strlen > '20'): echo (mb_substr($vo["goods_name"],0,19,'utf-8')); ?>...<?php else: echo ($vo["goods_name"]); endif; ?></a></p>
									<p class="price">
										<span class='shop_price'>东方价：<em>￥<?php echo ($vo["shop_price"]); ?></em></span>
										<span class="market_price">￥<?php echo ($vo["market_price"]); ?></span>	
									</p>
								</dt>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<!-- 新口上架 end -->
				
				<!-- 推荐商品 start --->
				<ul id="recommendedGoods" style="display:none">
					<?php if(is_array($recommendedGoods)): $i = 0; $__LIST__ = $recommendedGoods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<dl>
								<dd><a href="/goods-<?php echo ($vo["id"]); ?>.html" target='_blank'><img  class='lazy  transitionAll' data-original="<?php echo ($host); echo ($vo["goods_thumb"]); ?>" onerror="this.src='__PUBLIC__/Uploads/<?php echo ($defaultGoodsImg); ?>'" src="__PUBLIC__/Images/grey.gif" alt='<?php echo ($vo["goods_name"]); ?>' title="<?php echo ($vo["goods_name"]); ?>"></a></dd>
								<dt>
									<p><a href="/goods-<?php echo ($vo["id"]); ?>.html" target='_blank' title="<?php echo ($vo["goods_name"]); ?>"><?php if($vo.goods_name|strlen > '20'): echo (mb_substr($vo["goods_name"],0,19,'utf-8')); ?>...<?php else: echo ($vo["goods_name"]); endif; ?></a></p>
									<p class="price">
										<span class='shop_price'>东方价：<em>￥<?php echo ($vo["shop_price"]); ?></em></span>
										<span class="market_price">￥<?php echo ($vo["market_price"]); ?></span>	
									</p>
								</dt>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<!-- 推荐商品 end --->
				
				<!-- 热销商品 start -->
				<ul id="hotGoods" style="display:none">
					<?php if(is_array($hotGoods)): $i = 0; $__LIST__ = $hotGoods;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<dl>
								<dd><a href="/goods-<?php echo ($vo["id"]); ?>.html" target='_blank'><img class='lazy  transitionAll' data-original="<?php echo ($host); echo ($vo["goods_thumb"]); ?>" onerror="this.src='__PUBLIC__/Uploads/<?php echo ($defaultGoodsImg); ?>'" src="__PUBLIC__/Images/grey.gif" alt='<?php echo ($vo["goods_name"]); ?>' title="<?php echo ($vo["goods_name"]); ?>"></a></dd>
								<dt>
									<p><a href="/goods-<?php echo ($vo["id"]); ?>.html" target='_blank' title="<?php echo ($vo["goods_name"]); ?>"><?php if($vo.goods_name|strlen > '20'): echo (mb_substr($vo["goods_name"],0,19)); ?>...<?php else: echo ($vo["goods_name"]); endif; ?></a></p>
									<p class="price">
										<span class='shop_price'>东方价：<em>￥<?php echo ($vo["shop_price"]); ?></em></span>
										<span class="market_price">￥<?php echo ($vo["market_price"]); ?></span>	
									</p>
									<p class="saleNum">销量：<span><?php echo ($vo["sale_number"]); ?></span></p>
								</dt>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<!-- 热销商品 end -->
			</div>
		</div>
		
		<div class=""floor3>
			<div class="floorTitle"><span class="mobile">手机</span><a href='/category-1.html' target="_blank" title='more'>more</a></div>
				<div class='floorGoods'>
				<ul>
					<?php if(is_array($phone)): $i = 0; $__LIST__ = $phone;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<dl>
								<dd><a href="/goods-<?php echo ($vo["id"]); ?>.html" target="_blank"><img src="__PUBLIC__/Images/grey.gif" data-original="<?php echo ($host); echo ($vo["goods_thumb"]); ?>"  onerror="this.src='__PUBLIC__/Uploads/<?php echo ($defaultGoodsImg); ?>'" class='lazy  transitionAll' alt='<?php echo ($vo["goods_name"]); ?>' title="<?php echo ($vo["goods_name"]); ?>"></a></dd>
								<dt>
									<p><a href="/goods-<?php echo ($vo["id"]); ?>.html" target="_blank" title="<?php echo ($vo["goods_name"]); ?>"><?php if($vo.goods_name|strlen > '20'): echo (mb_substr($vo["goods_name"],0,19,'utf-8')); ?>...<?php else: echo ($vo["goods_name"]); endif; ?></a></p>
									<p class="price">
										<span class='shop_price'>东方价：<em>￥<?php echo ($vo["shop_price"]); ?></em></span>
										<span class="market_price">￥<?php echo ($vo["market_price"]); ?></span>	
									</p>
								</dt>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
	</div>
	
		<div class=""floor5>
			<div class="floorTitle"><span class="computer">电脑</span><a href='/category-2.html' target="_blank" title='more'>more</a></div>
				<div class='floorGoods'>
				<ul>
					<?php if(is_array($computer)): $i = 0; $__LIST__ = $computer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<dl>
								<dd><a href="/goods-<?php echo ($vo["id"]); ?>.html" target="_blank"><img src="__PUBLIC__/Images/grey.gif" data-original="<?php echo ($host); echo ($vo["goods_thumb"]); ?>"  onerror="this.src='__PUBLIC__/Uploads/<?php echo ($defaultGoodsImg); ?>'" class='lazy  transitionAll' alt='<?php echo ($vo["goods_name"]); ?>' title="<?php echo ($vo["goods_name"]); ?>"></a></dd>
								<dt>
									<p><a href="/goods-<?php echo ($vo["id"]); ?>.html" target="_blank" title="<?php echo ($vo["goods_name"]); ?>"><?php if($vo.goods_name|strlen > '20'): echo (mb_substr($vo["goods_name"],0,19,'utf-8')); ?>...<?php else: echo ($vo["goods_name"]); endif; ?></a></p>
									<p class="price">
										<span class='shop_price'>东方价：<em>￥<?php echo ($vo["shop_price"]); ?></em></span>
										<span class="market_price">￥<?php echo ($vo["market_price"]); ?></span>	
									</p>
								</dt>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
	</div>
	
	<div class=""floor5>
			<div class="floorTitle"><span class="camera">相机</span><a href='/category-3.html' target="_blank" title='more'>more</a></div>
				<div class='floorGoods'>
				<ul>
					<?php if(is_array($camera)): $i = 0; $__LIST__ = $camera;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<dl>
								<dd><a href="/goods-<?php echo ($vo["id"]); ?>.html" target="_blank"><img src="__PUBLIC__/Images/grey.gif" data-original="<?php echo ($host); echo ($vo["goods_thumb"]); ?>"  onerror="this.src='__PUBLIC__/Uploads/<?php echo ($defaultGoodsImg); ?>'" class='lazy  transitionAll' alt='<?php echo ($vo["goods_name"]); ?>' title="<?php echo ($vo["goods_name"]); ?>"></a></dd>
								<dt>
									<p><a href="/goods-<?php echo ($vo["id"]); ?>.html" target="_blank" title="<?php echo ($vo["goods_name"]); ?>"><?php if($vo.goods_name|strlen > '20'): echo (mb_substr($vo["goods_name"],0,19,'utf-8')); ?>...<?php else: echo ($vo["goods_name"]); endif; ?></a></p>
									<p class="price">
										<span class='shop_price'>东方价：<em>￥<?php echo ($vo["shop_price"]); ?></em></span>
										<span class="market_price">￥<?php echo ($vo["market_price"]); ?></span>	
									</p>
								</dt>
							</dl>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
	</div>
    
    <!-- 商店公告start -->
    <?php if($shop_notice): ?><div id="messageBoardContainer">
      <div id="messageBoard"> ﻿
        <div class="wrap">
          <h2> <span>东方商城公告！！</span> <a href="javascript:if(g_fnQuirkyPopupClose){g_fnQuirkyPopupClose()};" title="关闭"></a> </h2>
          <div class="content" style="font-family:Arial;">
            <p style="position:relative; top:-16px;padding-top:10px"><span><?php echo ($shop_notice); ?></span></p>
          </div>
        </div>
        <div class="bg"></div>
      </div>
    </div>
    <a href="javascript:;" id="quirkyPopupShowBtn" style="display:none;"></a> ﻿<?php endif; ?>
    <!-- 商店公告end -->
    
    
	<script type="text/javascript">
		$(function(){
			
			/*分类树*/
			$(".floor1 .cat li[class='catList']").hover(function(){			
				var k = $(".floor1 .cat li[class='catList']").index($(this));
				var length = $(".floor1 .cat li[class='catList']").length;
				if(k == 0){
					$(".floor1 .cat li[class='catList']").eq(k).css({"background":"url(__PUBLIC__/Images/bg2.png) no-repeat center center","border-right":"none"});
				}else if(k == (length-1)){
					$(".floor1 .cat li[class='catList']").eq(k).css({"background":"url(__PUBLIC__/Images/bg3.png) no-repeat center center","border-right":"none"});
				}else{
					$(".floor1 .cat li[class='catList']").eq(k).css({"background":"url(__PUBLIC__/Images/bg1.png) no-repeat center center","border-right":"none"});	
				}	
				$(".floor1 .cat li[class='catList']").eq(k).children('span').css("background","url(__PUBLIC__/Images/arror02.png) no-repeat center center");
				$(this).find(".subMenu").show()
			},function(){
				var k = $(".floor1 .cat li[class='catList']").index($(this));
				$(".floor1 .cat li[class='catList']").eq(k).css({"background":"none","border-right":"2px solid #E60012"});		
				$(".floor1 .cat li[class='catList']").eq(k).children('span').css("background","url(__PUBLIC__/Images/arror01.png) no-repeat center center");
				$(this).find(".subMenu").hide();
			});
			
			/*banner*/
			var img = document.getElementById("bannerImg");
			var num = document.getElementById("imgNum");
			var ali = img.getElementsByTagName("li");
			var oli = num.getElementsByTagName("li");
			var time = null;
			lanrenzhijiaing = document.getElementById("banner");
			img.style.width = ali.length * 780 + "px", inow = 0;
			for (var i = 0; i < oli.length; i++) {
				oli[i].index = i;
				oli[i].onmouseover = function() {
					inow = this.index;
					tab();
					window.clearInterval(time);
				}
				oli[i].onmouseout = function() {
					time = window.setInterval(autoPlay, 2000);
				}
			}			
			function tab() {
				for (var i = 0; i < oli.length; i++) {
					oli[i].className = "";
				}
				oli[inow].className = "currentBanner";
				startMove(img, {
					left: -inow * 780
				}, 'buffer')
			}			
			function autoPlay() {
				inow++;
				if (inow >= ali.length) {
					inow = 0;
				}
				tab();
			}
			time = window.setInterval(autoPlay, 3000)})
		
			var bannerNum = $("#bannerImg li").length;
			var str = '';
			for(var i=0; i<bannerNum; i++){
				if(i == 0){
					str += "<li class='currentBanner'>"+(i+1)+"</li>";
				}else{
					str += "<li>"+(i+1)+"</li>";
				}
			}
			$("#imgNum").append(str);
	
			$(".floor2Title li").mouseover(function(){
				$(this).addClass("item").siblings().removeClass("item")
				var k = $(".floor2Title li").index($(this));
				if(k == 0){
					$("#newGoods").css("display","").siblings().css("display","none");
				}else if(k == 1){
					$("#recommendedGoods").css("display","").siblings().css("display","none");
				}else if(k == 2){
					$("#hotGoods").css("display","").siblings().css("display","none");
				}
			});
	</script>
	
<div class="footer">
	<div class="footerBox">
		<div class="footerBanner">
			<ul>
				<li><b class="ico ensure"></b><span>正品保障</span></li>
				<li><b class="ico heart"></b><span>用心服务</span></li>
				<li><b class="ico shipping"></b><span>配货迅速</span></li>
			</ul>
		</div>
		<div class="footerNav">
			<?php if(is_array($bottomLink)): $i = 0; $__LIST__ = $bottomLink;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl>
					<dt><?php echo ($vo["cat_name"]); ?></dt>
					<?php if(is_array($vo["article"])): $i = 0; $__LIST__ = $vo["article"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?><dd><a href="/article-<?php echo ($item["id"]); ?>.html" target="_blank" title="<?php echo ($item["title"]); ?>"><?php echo ($item["title"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
				</dl><?php endforeach; endif; else: echo "" ;endif; ?>
			

		</div>
	
		<div class="flink">
			<ul>
                <?php if(is_array($flink)): $i = 0; $__LIST__ = $flink;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo ($vo["link_url"]); ?>" title="<?php echo ($vo["link_name"]); ?>" target="_blank"><?php echo ($vo["link_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<div class="copuRight">
			<p>CopyRight© 东方商城2014，All Rights Reserved&nbsp;&nbsp;&nbsp;&nbsp;
			<?php if($serviceEmail): ?>服务邮箱：<a href='mailto:<?php echo ($serviceEmail); ?>'><?php echo ($serviceEmail); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;<?php endif; ?>
			<?php echo ($stats_code); ?></p>
		</div>
		<div class="credit">
			<ul>
				<li><img src="__PUBLIC__/Images/credit1.png" /></li>
				<li><img src="__PUBLIC__/Images/credit2.png" /></li>
				<li><img src="__PUBLIC__/Images/credit3.png" /></li>
				<li><img src="__PUBLIC__/Images/credit4.jpg" /></li>
				<li><img src="__PUBLIC__/Images/credit5.gif" /></li>
			</ul>
		</div>
	</div>
	
	<div id="scrollTop" >
	    <div class="level-2"></div>
	    <div class="level-3" title='TOP'></div>
	</div>
	<script type="text/javascript" src="__PUBLIC__/Js/goTop.js"></script>
</div>
<script type='text/javascript'>
	$(function(){
		$('.footerNav dl:last').css('border','none');
	})
</script>

</body>
</html>