/* 切换商品描述与评论 */
$(function(){
	//删除评论最后和条边框
	$(".goodsComment .commontList:last").css('border','none');
	
	
	$(".goodsConTitle span").click(function(){
		$(this).addClass("currentTab").siblings().removeClass("currentTab");
		var k =$(".goodsConTitle span").index($(this));

		if(k == 0){
			$(".goodsDesc").show();
			$(".goodsComment").hide();
			$('.commontZone').hide();
		}else{
			$(".goodsDesc").hide();
			$('.commontZone').show();
			$(".goodsComment").show();
		}
	})
	
	$("#email").focus(function(){
		$("#emailWarning").text("").hide();
	});
	
	$("#commontCon").focus(function(){
		$("#commontWarning").text("").hide();
	});
	
	$("#verify").focus(function(){
		$("#verifyWarning").text("").hide();
	});

	
	
})

/* 商品数量减1 */
function goodsReduce(){
	var val = parseInt($("#buyNum").val());
	if(val <= 1){
		return false;
	}else{
		$("#buyNum").val(val-1);
	}
}

/* 商品数量加1 */
function goodsAdd(){
	var val = parseInt($("#buyNum").val());
	$("#buyNum").val(val+1);
}

//提交评论
function submitComment(id){
	$("#submitBtn").val("提交中...");
	ZENG.msgbox.show('正在提交...', 6, 0);
	var email = $("#email").val();
	var commont = $("#commontCon").val();
	var verify = $("#verify").val();
	var radio = $(".commentRank input");
	for(var i=0; i<radio.length; i++){
		if(radio[i].checked){
			var rank = radio[i].value;
			break;
		}
	}

	var disable = false;
	if(!email){
		$("#emailWarning").html("email不能为空").show();
		disable = true;
	}else if(!isMail(email)){
		$("#emailWarning").html("email格式不正确").show();
		disable = true;
	}else{
		$("#emailWarning").html("").hide();
	}
	
	if(!commont){
		$("#commontWarning").text('评论内容不能为空').show();
		disable = true;
	}else{
		$("#commontWarning").text('').hide();
	}
	
	if(!verify){
		$("#verifyWarning").text('验证码不能为空').show();
		disable = true;
	}else{
		$("#verifyWarning").text('').hide();
	}

	if(disable){
		$("#submitBtn").val("提交评论");
		ZENG.msgbox.hide();
		return false;
	}
	
	var url = '/index.php/Goods/goodsComment';
	var data = {email:email,commont:commont,rank:rank,verify:verify,id:id};
	$.post(url, data, function(data){
		if(data == '0'){
			ZENG.msgbox.hide();
			ZENG.msgbox.show('提交成功', 4, 3000);
			document.form.reset();
		}else if(data == '2'){
			ZENG.msgbox.hide();
			$("#verifyWarning").text('验证码错误').show();
		}else if(data == '3'){
			ZENG.msgbox.hide();
			$("#emailWarning").html("email不能为空").show();
		}else if(data == '4'){
			ZENG.msgbox.hide();
			$("#emailWarning").html("email格式不正确").show();
		}else if(data == '5'){
			ZENG.msgbox.hide();
			$("#commontWarning").text('评论内容不能为空').show();
		}else{
			ZENG.msgbox.show('提交失败', 5, 3000);
		}
		$("#submitBtn").val("提交评论");
	})
	

	/*
	 *   1、警告  4、成功   5、失败    6、加载
	 */
	//ZENG.msgbox.show('成功', 4, 3000);
}

/* 商品添加到收藏夹 */
function collect(id){
	ZENG.msgbox.show('正在添加到收藏夹...', 6, 0);
	var url = '/index.php/Goods/goodsCollect';
	var data = {id:id};
	$.post(url, data, function(data){
		if(data == '1'){
			ZENG.msgbox.show('您还没有登录，不能收藏商品', 5, 3000);
		}else if(data == '2'){
			ZENG.msgbox.show('您已经收藏该商品', 1, 3000);
		}else if(data == '4'){
			ZENG.msgbox.show('收藏成功', 4, 3000);
		}else{
			ZENG.msgbox.show('收藏失败', 5, 3000);
		}
	})
}

/* 立即购买 */
function buyGoods(id){
	var num = parseInt($('#buyNum').val());
	if(num<1 || isNaN(num)){
		ZENG.msgbox.show('请输入正确数量', 5, 3000);
		return false;
	}	
	// 1、警告  4、成功   5、失败    6、加载
	ZENG.msgbox.show('正在添加到购物车...', 6, 0);
	var url = "/index.php/Cart/addCart";
	var data = {id:id,num:num};
	$.post(url, data, function(data){
        if(data == '0'){
            alert('您要购买的商品不存在');
            window.location.reload();
        }else if(data == '1' || data == '2'){
            window.location.href='/index.php/Cart';
        }else if(data == '4'){
        	ZENG.msgbox.show('您还没有登录，不能购买商品', 1, 3000);
        }else if(data == '5'){
        	ZENG.msgbox.show('商品库存不足', 1, 3000);
        }else{
            ZENG.msgbox.show('购买失败', 5, 3000);
        }
    });
}



