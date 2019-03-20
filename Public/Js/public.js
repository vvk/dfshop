$(function(){
	//收藏本站图标旋转
	$("#collection").hover(function(){
		$(".webAction b").css('transform','rotate(720deg)');
		$(".webAction b").css('-webkit-transform','rotate(720deg)');
	},function(){
		$(".webAction b").css('transform','');
		$(".webAction b").css('-webkit-transform','');
	})
	
	$("#headerSearch").focus(function(){
		$("#headerSearch").css('border','0px');
		var rel = $("#headerSearch").attr("rel");
		var val = $("#headerSearch").val();
		$("#headerSearch").css("color",'#404040');
		if(rel == val){
			$("#headerSearch").css("color",'red !important');
			$("#headerSearch").val('');
		}
	})
	
	$("#headerSearch").blur(function(){
		var rel = $("#headerSearch").attr("rel");
		var val = $("#headerSearch").val();
		if(!val){
			$("#headerSearch").css("color",'#999')
			$("#headerSearch").val(rel)
		}else{
			$("#headerSearch").css("color",'#404040');
		}
	})
	
	$(".header .headerCon .cart span").hover(function(){
		$(".header .headerCon .cart span").css("background",'#fff');
		var loading = "<img src='/Public/Images/loading1.gif' id='loadingCartGoods'/>";
		$('.cartGoods').html(loading);
		$(".cartGoods").show();
		getCartGoods();
	},function(){
		$(".header .headerCon .cart span").css("background",'#F8F8F8');
		$(".cartGoods").hide();
	})
	getCartGoods();

	//每3秒更新一次
	setInterval(function(){
		getCartGoods();
	},3000)
	
	/* 去掉浏览历史中最后商品下边的边框 */
	$(".history li:last").find("dl").css("border","none");
	
	/* 去掉热门商品最后一件的边框 */
	$(".hotGoods li:last dl").css("border",'none');

	//鼠标在商品图片是划过效果
	$('.transitionAll').hover(function(){
        $(this).addClass('scaleImg');
    },function(){
        $(this).removeClass('scaleImg');
    })
 
    //导航高亮
	setMenuHeightLight();
})

/* 查询购物车中的商品 */
function getCartGoods(){
	var url = '/index.php/Cart/getCartGoods';
	$.post(url, '', function(data){
		if(data){
			var str = "<div  class='cartGoodsList'><ul>";
			var num = 0
			for(var i =0; i<data.length; i++){
				num = parseInt(num) + parseInt(data[i].goods_number);
				if(data[i].goods_name.length>20){
					var goods_name = data[i].goods_name.substr(0,19)+'...';
				}else{
					var goods_name = data[i].goods_name
				}
				str += "<li><a href='/goods-"+data[i].goods_id+".html' class='goodsName' target='_blank' title='"+data[i].goods_name+"'>"+goods_name+"</a><a href='javascript:void(o)' onclick='delCartGoods("+data[i].id+")' style='float:right;margin-right:5px;'>删除</a><font style='float:right;margin-right:5px;color:#666'>("+data[i].goods_number+"件)</font></li>"
			}
			str += '</ul>';
		}else{		
			var str = "<p class='noGoods'>您的购物车中没有商品</p>";
			var num  = 0;
		}
		$('#cartGoodsNum').text(num);
		$('.cart .cartGoods').html(str);
	},'JSON');
}

//加入收藏夹
function AddFavorite(sURL, sTitle) {   
     var sURL = encodeURI(sURL);          
     try{      
            window.external.addFavorite(sURL, sTitle);      
     }catch(e) {      
            try{      
                window.sidebar.addPanel(sTitle, sURL, "");      
            }catch (e) {      
                alert("加入收藏失败，请使用Ctrl+D进行添加,或手动在浏览器里进行设置.");   
            }  
	}
}

//设为首页
function SetHome(url){   
        if (document.all) {   
            document.body.style.behavior='url(#default#homepage)';   
            document.body.setHomePage(url);
		}else{   
	            alert("您好,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");   
  		 }   
}


//滚动到顶部不再向上滚动
function float_lay(obj,id,left){
    if(!left || left == 'undefined'){
        left = 0;
    }
    
	//导航距离屏幕顶部距离
	var _defautlTop = $("#"+id).offset().top;
	//导航距离屏幕左侧距离
	var _defautlLeft = $("#"+id).offset().left;
	//导航默认样式记录，还原初始样式时候需要
	var _position = $("#"+id).css('position');
	var _top = $("#"+id).css('top');
	var _left = $("#"+id).css('left');
	var _zIndex = $("#"+id).css('z-index');
	//鼠标滚动事件
	$(window).scroll(function(){
		if($(obj).scrollTop() > _defautlTop){
			$("#"+id).css({'position':'fixed','top':'0px','left':_defautlLeft-left,'z-index':998});
		}else{
			$("#"+id).css({'position':_position,'top':_top,'left':_left,'z-index':_zIndex});
		}
	});	
}


function logout(){   
   var con = confirm('确定要退出吗？');
   if(!con){
	   return false;	
   }
  
   var url = '/index.php/User/logout';
   $.get(url, '', function(){
        window.location.reload();
   }); 
}

//积分抵现金
function forCash(obj){
    var integral = obj.value;
    var reg = /^[\d]+$/;
    if(!reg.test(integral) && integral != ''){
    	alert('您输入的不合法');
        $("#integralNum").text('￥0.00');
        $('#integral').val('');
        $("#forCash").text(0.00)
        $('#expenseIntegral').text(0);
    	$('#trueCost').text($("#goodsValue").text());
		return false;
    }    
    
    if(!integral){
        $("#integralNum").html('￥0.00');
        $('#expenseIntegral').text(0);
        $("#forCash").text(0.00)
        $('#trueCost').text($("#goodsValue").text());
        return false;
    }
    
    integral = parseInt(integral);
    if((typeof integral) == 'number' && integral != ''){
        if(integral<100){ //每次使用积分最少为100
            $("#integralNum").text('￥0.00');
            $('#expenseIntegral').text(0);
            $("#forCash").text(0.00)
            $('#trueCost').text($("#goodsValue").text());
            return false;
        }
        
        var integralNum = (Math.floor((integral/100))/100).toFixed(2);
        var goodsValue = $("#goodsValue").text();
        if(goodsValue<integralNum){
        	alert('使用积分的抵现金额不能大于商品的总金额');        	
        	return false;
        }
        var trueCost = goodsValue - integralNum;
        $('#expenseIntegral').text(integral);
        $('#trueCost').text(trueCost);
      
        $("#integralNum").text('￥'+integralNum);
        $("#forCash").text('￥'+integralNum);
    }
}


/* 加入购物车 */
function addCart(id){
    var url = "/index.php/Cart/addCart";
    var data = {id:id};
    $.post(url, data, function(data){
        if(data == '0'){
            alert('您要添加到购物车的商品不存在');
            window.location.reload();
        }else if(data == '1'){
        	//   1、警告  4、成功   5、失败    6、加载
        	ZENG.msgbox.show('该商品已经添加到购物车', 1, 3000);
        }else if(data == '2'){
            ZENG.msgbox.show('添加购物车成功', 4, 3000);
        }else if(data == '4'){
        	ZENG.msgbox.show('您还没有登录，不能添加到购物车', 1, 3000);
        }else if(data == '5'){
        	ZENG.msgbox.show('商品库存不足', 1, 3000);
        }else{
            ZENG.msgbox.show('添加到购物车失败', 5, 3000);
        }
    })
}

/* 清空浏览历史 */
function clearHistory(){
	var historyNum = $('.historyGoods li').length;
	if(historyNum < 1){
		ZENG.msgbox.show('您的浏览历史为空', 5, 3000);
		return false;
	}
	ZENG.msgbox.show('正在清除浏览历史...', 6,0);
	var url = '/index.php/Common/clearHistory';
	$.post(url, '', function(data){
		$('.historyGoods').remove();
		$(".history").append("<p class='noHistory'>您的浏览历史为空</p>");
		ZENG.msgbox.show('删除历史记录成功', 4, 3000);
	})
}

/* 删除购物车中的商品 */
function delCartGoods(id){
	var con = confirm('确定要删除吗？');
	if(!con){
		return false;
	}
	ZENG.msgbox.show('正在删除购物车中的商品...', 6, 0);
	var url = "/index.php/Cart/delCartGoods";
	var data = {id:id};
	$.post(url, data, function(data){
		if(data == '1'){
			getCartGoods();
			ZENG.msgbox.show('删除成功', 4, 3000);
			$("#"+id).remove();
		}else{
			ZENG.msgbox.show('删除失败', 5, 3000);
		}
	})
}	

/* 检查的提交的订单的表单 */
function checkOrderForm(){
	var shipping = $('.express input:checked').val();
	var pay = $('.payment input:checked').val();
	var error = '';
	if(shipping == undefined){
		error += '请选择支付方式\n';
	}
	if(pay == undefined){
		error += '请选择支付方式';
	}
	if(error){
		alert(error);
		return false;
	}
}

/* 导航高亮 */
function setMenuHeightLight(){
	var url = window.location.href;	
	var link = '';
	if(url.indexOf('Goods')>=0 || url.indexOf('Cart')>=0 || url.indexOf('Search')>=0 || url.indexOf('User')>=0){
		$('.nav .navBox li').removeClass('currentNav');
		return false;
	}
	
	//从分类树跳转过来时导航高亮
	if(url.indexOf('categoryTree')>=0){
		$('.nav .navBox li').removeClass('currentNav');
	   /*var reg = /id\/([\d]+)/i;
	   var res = url.match(reg);
	   var data = {id:res[1]};
	   var href = '/index.php/Common/getCategoryById';
	   $.post(href, data, function(data){		   
		   reg = /id\/[\d]+\categoryTree/i;
		   url.replace(reg, '/id/'+data);
		   $('.nav .navBox li').each(function(k){		
				link = '/'+$(this).find('a').attr('href')+'/i';
				alert(link)
				if(url.search(link)>=0){
					$(this).addClass('currentNav').siblings().removeClass('currentNav');
					return false;        
				}
			})
	   })*/
	}else{
		$('.nav .navBox li').each(function(k){		
			link = $(this).find('a').attr('href');
			
			if(url.indexOf(link)>=0){
				$(this).addClass('currentNav').siblings().removeClass('currentNav');
				return false;        
			}
		})
	}

}

/* 顶部商品搜索 */
function searchGoods(){
	var val = $('#headerSearch').val();
	window.location.href="/search-"+val+".html";
}

/* 顶部商品回车搜索 */
function searchGoodsEnter(event){
	event = event ? event : window.event;
	if(event.keyCode == 13){
		searchGoods();
	}
}







