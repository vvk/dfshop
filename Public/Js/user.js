	/* 注册用户名获得焦点 */
	function usernameRegFocus(){
		$("#usernameBox").css('border','1px solid #CCCCCC');
		$('.usernameImg').css('background-position','-26px -205px');
		$("#usernameWwarning b").css('background-position','0 -119px');
		$("#usernameWwarning font").css('color','#999');
		$("#usernameWwarning font").text('用户名为6-20位字母或数字');
		$("#usernameWwarning").show();	
	}
	
	/* 注册用户名失去焦点 */
	function usernameRegBlur(){
		$('.usernameImg').css('background-position','0 -205px');
		var username = $("#username").val();
		var error = '';
		if(!username){
			error = '用户名不能为空';
		}else if(username.length<6 || username.length>20){
			error = '用户名长度为6-20位';
		}else if(!isLetNum(username)){
			error = '用户名必须为字母或数字';
		}
		
		if(error){
			$("#usernameBox").css('border','1px solid red');
			$('#usernameWwarning b').css('background-position','0 1px');
			$("#usernameWwarning font").css('color','#f00');
			$("#usernameWwarning font").text(error);
			$("#usernameWwarning").show();	
		}else{
       	    $("#usernameBox").css('border','1px solid #CCCCCC');
			$("#usernameWwarning font").css('color','#999');
			$('#usernameWwarning b').css('background-position','0 -199px');
			$("#usernameWwarning font").text('用户名可用');
            $("#usernameWwarning").show();
		}
	}
	
	/* 注册密码获得焦点 */
	function passRegFocus(){
		$('.passImg').css('background-position','-26px -180px');
		$("#passBox").css('border','1px solid #CCCCCC');
		$("#passWwarning b").css('background-position','0 -119px');
		$("#passWwarning font").css('color','#999');
		$("#passWwarning font").text('密码长度为6-20位字符');
		$("#passWwarning").show();	
	}
	
	/* 注册密码失去焦点 */
	function passRegBlur(){
		$('.passImg').css('background-position','0 -180px');
		var pass = $("#pass").val();
		var repass = $("#repass").val();
		var error1 = '';
		
		if(!pass){
			error1 = '密码不能为空';
		}else if(pass.length<6 || pass.length>20){
			error1 = '密码长度为6-20字符';
		}else if(repass){
			if(pass != repass){
				$('#repassWwarning b').css('background-position','0 1px');
				$("#repassWwarning font").css('color','#f00');
				$("#repassWwarning font").text('再次输入的密码不一致');
				$("#repassWwarning").show();
			}else{
				repassRegBlur();
			}
		}		
		
		if(error1){
			$("#passBox").css('border','1px solid red');
			$('#passWwarning b').css('background-position','0 1px');
			$("#passWwarning font").css('color','#f00');
			$("#passWwarning font").text(error1);
			$("#passWwarning").show();	
		}else{
			$("#passBox").css('border','1px solid #CCCCCC');
			$("#passWwarning font").css('color','#999');
			$('#passWwarning b').css('background-position','0 -199px');
			$("#passWwarning font").text('密码或用');
		}
		
		
	}
	
	/* 注册确认密码获得焦点 */
	function repassRegFocus(){
		$('.repassImg').css('background-position','-26px -180px');
		$("#repassBox").css('border','1px solid #ccc');
	}
	
	/* 注册确认密码失去焦点 */
	function repassRegBlur(){
		$('.repassImg').css('background-position','0 -180px');
		$("#repassBox").css('border','1px solid red');
		var repass = $("#repass").val();
		var pass = $("#pass").val();
		var error = '';
		
		if(!repass){
			error = '确认密码不能为空';
		}else if(repass.length<6 || repass.length>20){
			error = '密码长度为6-20字符';
		}else if(pass != repass){
			error = '两次输入的密码不一致';
		}		
		
		if(error){
			$("#repassBox").css('border','1px solid red');
			$('#repassWwarning b').css('background-position','0 1px');
			$("#repassWwarning font").css('color','#f00');
			$("#repassWwarning font").text(error);
			$("#repassWwarning").show();	
		}else{
			$("#repassBox").css('border','1px solid #CCCCCC');
			$("#repassWwarning font").css('color','#999');
			$('#repassWwarning b').css('background-position','0 -199px');
			$("#repassWwarning font").text('');
			$("#repassWwarning").show();
		}
	}
	
	/* 注册邮箱获得焦点 */
	function emailFocus(){
		$('.emailImg').css('background-position','-26px -231px');
		$("#emailBox").css('border','1px solid #CCCCCC');
		$("#emailWwarning b").css('background-position','0 -120px');
		$("#emailWwarning font").css('color','#999');
		$("#emailWwarning font").text('此邮箱将用来登录，注册后不能更改');
		$("#emailWwarning").show();	
	}
	
	/* 注册邮箱失去焦点 */
	function emailRegFocus(){
		$('.emailImg').css('background-position','0 -231px');
		var email = $("#email").val();
		var error = '';
		
		if(!email){
			error = '邮箱不能为空';
		}else if(!isMail(email)){
			error = '邮箱格式不正确';
		}
		
		if(error){
			$("#emailBox").css('border','1px solid red');
			$('#emailWwarning b').css('background-position','0 1px');
			$("#emailWwarning font").css('color','#f00');
			$("#emailWwarning font").text(error);
			$("#emailWwarning").show();	
		}else{
		    var url = '/index.php/User/isRegister';           
            var data = {email:email};
            
            $.post(url, data, function(data){
                if(data == '0'){
                    $("#emailBox").css('border','1px solid #CCCCCC');
        			$("#emailWwarning font").css('color','#999');
        			$('#emailWwarning b').css('background-position','0 -200px');
        			$("#emailWwarning font").text('邮箱可用');
                }else{
                    $("#emailBox").css('border','1px solid red');
        			$('#emailWwarning b').css('background-position','0 1px');
        			$("#emailWwarning font").css('color','#f00');
        			$("#emailWwarning font").text('邮箱已被注册');
        			$("#emailWwarning").show();	
                }
            },'TEXT');
		}
	}
	
	function registerEnter(event){
		event = event ? event : window.event;
		if(event.keyCode == 13){
			register();
		}
	}
	
	/* 注册验证码获得焦点 */
	function verifyRegFocus(){
		$("#verifyWwarning").hide();
	}
	
	/* 注册验证码获得焦点 */
	function verifyRegBlur(){
		var verify = $("#verify").val();
		var error = '';
		
		if(!verify){
			error = '验证码不能为空';
		}		
		
		if(error){
			$("#verifyBox").css('border','1px solid red');
			$('#verifyWwarning b').css('background-position','0 1px');
			$("#verifyWwarning font").css('color','#f00');
			$("#verifyWwarning font").text(error);
			$("#verifyWwarning").show();	
		}else{
            var url = '/index.php/User/checkVerify';
            var data = {verify:verify};
            
            $.post(url, data, function(data){
                if(data == '1'){
                    $("#verifyBox").css('border','1px solid #CCCCCC');
        			$("#verifyWwarning font").css('color','#999');
        			$('#verifyWwarning b').css('background-position','0 -200px');
        			$("#verifyWwarning font").text('');
        			$("#verifyWwarning").show();
                }else{
                    $("#verifyBox").css('border','1px solid red');
        			$('#verifyWwarning b').css('background-position','0 1px');
        			$("#verifyWwarning font").css('color','#f00');
        			$("#verifyWwarning font").text('验证码错误');
        			$("#verifyWwarning").show();
                }
            }, "TEXT");		
		}
	}
	
	/* 注册 */
	function register(){
		$("#registerBtn").val('正在注册...');

		var username = $("#username").val();
		var email = $("#email").val();
		var password = $("#pass").val();		
		var repassword = $("#repass").val();
		var verify = $("#verify").val();
  
		var disable = false;	
		var usernameError = passError = repassError = emailError = verifyError = '';
		if(!username){
			usernameError = '用户名不能为空';
		}else if(username.length<6 || username.length>20){
			usernameError = '用户名长度为6-20位';
		}else if(!isLetNum(username)){
			usernameError = '用户名必须为字母或数字';
		}

        /* 用户名有错  */
		if(usernameError){
			$("#usernameBox").css('border','1px solid red');
			$('#usernameWwarning b').css('background-position','0 1px');
			$("#usernameWwarning font").css('color','#f00');
			$("#usernameWwarning font").text(usernameError);
			$("#usernameWwarning").show();	
			disable = true;
		}else{
			$("#usernameWwarning font").text('');
			$("#usernameWwarning").hide();	
		}
        
        if(!email){
            emailError = '邮箱不能为空'; 
        }else if(!isMail(email)){
            emailError = '邮箱格式不正确';
        }
        
        /* 邮箱有错 */
       	if(emailError){
			$("#emailBox").css('border','1px solid red');
			$('#emailWwarning b').css('background-position','0 1px');
			$("#emailWwarning font").css('color','#f00');
			$("#emailWwarning font").text(emailError);
			$("#emailWwarning").show();	
            disable = true;
		}else{
			$("#emailWwarning font").text('');
			$("#emailWwarning").hide();	
		}
		
        if(!password){
            passError = '密码不能为空';
        }else if(password.length<6 || password.length>20){
            passError = '密码长度为6-20位';
        }
        
        /* 密码有错 */
       	if(passError){
			$("#passBox").css('border','1px solid red');
			$('#passWwarning b').css('background-position','0 1px');
			$("#passWwarning font").css('color','#f00');
			$("#passWwarning font").text(passError);
			$("#passWwarning").show();	
			disable = true;
		}else{
			$("#passWwarning font").text('');
			$("#passWwarning").hide();	
		}
        
        if(!repassword){
            repassError = '确认密码不能为空';
        }else if(password != repassword){
            repassError = '两次输入的密码不一致'
        }else if(repassword.length<6 || repassword.length>20){
            repassError = '确认密码长度为6-20位';
        }       
                
         /* 确认密码有错 */
       	if(repassError){
			$("#repassBox").css('border','1px solid red');
			$('#repassWwarning b').css('background-position','0 1px');
			$("#repassWwarning font").css('color','#f00');
			$("#repassWwarning font").text(repassError);
			$("#repassWwarning").show();	
			disable = true;
		}else{
			$("#repassWwarning font").text('');
			$("#repassWwarning").hide();	
		}
        
        if(!verify){
            verifyError = '验证码不能为空'
        }
        
        if(verifyError){
            $("#verifyBox").css('border','1px solid red');
			$('#verifyWwarning b').css('background-position','0 1px');
			$("#verifyWwarning font").css('color','#f00');
			$("#verifyWwarning font").text(verifyError);
			$("#verifyWwarning").show();
            disable = true; 
        }else{
            $("#verifyBox").css('border','1px solid #CCCCCC');
			$("#verifyWwarning font").css('color','#999');
			$('#verifyWwarning b').css('background-position','0 -200px');
			$("#verifyWwarning font").text('');
			$("#verifyWwarning").show();
        }
     
		if(disable){
			$("#registerBtn").val('同意协议并注册');
            
            /*
        	 *   1、警告  4、成功   5、失败    6、加载
        	 */
        	ZENG.msgbox.show('请完善信息', 5, 3000);
			return false;
		}
        
        var url = '/index.php/User/registerAct';
        var data = {username:username,password:password,repassword:repassword,email:email,verify:verify};
        $.post(url, data, function(data){
            
            if(data.username != ''){
                $("#usernameBox").css('border','1px solid red');
    			$('#usernameWwarning b').css('background-position','0 1px');
    			$("#usernameWwarning font").css('color','#f00');
    			$("#usernameWwarning font").text(data.username);
    			$("#usernameWwarning").show();	
    			disable = true; 
            }
            
            if(data.email != ''){
                $("#emailBox").css('border','1px solid red');
    			$('#emailWwarning b').css('background-position','0 1px');
    			$("#emailWwarning font").css('color','#f00');
    			$("#emailWwarning font").text(data.email);
    			$("#emailWwarning").show();	
                disable = true;
            }
            
            if(data.password != ''){
                $("#passBox").css('border','1px solid red');
    			$('#passWwarning b').css('background-position','0 1px');
    			$("#passWwarning font").css('color','#f00');
    			$("#passWwarning font").text(data.password);
    			$("#passWwarning").show();	
    			disable = true;
            }
            
            if(data.repassword != ''){
                $("#repassBox").css('border','1px solid red');
    			$('#repassWwarning b').css('background-position','0 1px');
    			$("#repassWwarning font").css('color','#f00');
    			$("#repassWwarning font").text(data.repassword);
    			$("#repassWwarning").show();
                disable = true;
            }
            
            if(data.verify != ''){
                $("#verifyBox").css('border','1px solid red');
    			$('#verifyWwarning b').css('background-position','0 1px');
    			$("#verifyWwarning font").css('color','#f00');
    			$("#verifyWwarning font").text(data.verify);
    			$("#verifyWwarning").show();
                disable = true; 
            }
            
            if(disable){
                ZENG.msgbox.show('请填写正确信息', 5, 3000);
               	$("#registerBtn").val('同意协议并注册');
                return false;
            }
            
            if(data.success == '1'){
                location.href = '/profile.html';
            }else{
                ZENG.msgbox.show('注册失败', 5, 3000);
               	$("#registerBtn").val('同意协议并注册');
                return false;
            }
        }, "JSON"); 
	}
    
    
    /* 登录邮箱获得焦点 */
	function emailLogFocus(){
		$("#emailBox b").css('background-position','-26px -231px');
		$("#emailBox").css('border','1px solid #CCCCCC');	
		$("#emailWwarning b").css('background-position','0 -119px');
		$("#emailWwarning font").css('color','#999');
		$("#emailWwarning font").text('请输入您注册时的邮箱');
		$("#emailWwarning").show();	 
	}

	/* 登录邮箱失去焦点 */
	function emailLogBlur(){
		$("#emailBox b").css('background-position','0 -231px');
		$("#emailBox").css('border','1px solid red');
        
        var email = $("#email").val();
        var error = '';
      
        if(!email){
           error = '邮箱不能为空';
        }else if(!isMail(email)){
           error = '邮箱格式不正确';
        }
        
        if(error){
            $("#emailBox").css('border','1px solid red');
			$('#emailWwarning b').css('background-position','0 1px');
			$("#emailWwarning font").css('color','#f00');
			$("#emailWwarning font").text(error);
			$("#emailWwarning").show();
            return false;
        }
        
        var url = '/index.php/User/isRegister';
        var data = {email:email};
        $.post(url, data, function(data){
            if(data == '3'){
                $("#emailBox").css('border','1px solid #CCCCCC');
    			$("#emailWwarning font").css('color','#999');
    			$('#emailWwarning b').css('background-position','0 -200px');
    			$("#emailWwarning font").text('');
    			$("#emailWwarning").show();
            }else{
                $("#emailBox").css('border','1px solid red');
    			$('#emailWwarning b').css('background-position','0 1px');
    			$("#emailWwarning font").css('color','#f00');
    			$("#emailWwarning font").text('您输入的邮箱不存在');
    			$("#emailWwarning").show();
            }            
        }, "TEXT");
	}
	
	/* 登录密码获得焦点 */
	function passlogFocus(){
		$('#passBox b').css('background-position','-26px -180px');
		$("#passBox").css('border','1px solid #CCCCCC');	
		$("#passBox").css('border','1px solid #CCCCCC');	
		$("#passWwarning b").css('background-position','0 -119px');
		$("#passWwarning font").css('color','#999');
		$("#passWwarning font").text('请输入密码');
		$("#passWwarning").show();	 
	}
	
	/* 登录密码失去焦点 */
	function passlogBlur(){
		$('#passBox b').css('background-position','0 -180px');
		$('#passBox').css('border','1px solid red');
       
        var password = $("#pass").val();
        var error = '';
        
        if(!password){
            error = '密码不能为空';
        }else if(password.length<6 || password.length>20){
            error = '密码长度为6-20位';          
        }	
        
        if(error){
            $("#passBox").css('border','1px solid red');
			$('#passWwarning b').css('background-position','0 1px');
			$("#passWwarning font").css('color','#f00');
			$("#passWwarning font").text(error);
			$("#passWwarning").show();
            return false;
        }else{
            $("#passBox").css('border','1px solid #CCCCCC');
			$("#passWwarning font").css('color','#999');
			$('#passWwarning b').css('background-position','0 -200px');
			$("#passWwarning font").text('');
			$("#passWwarning").show();
        }      
	}
    
    /* 用户登录 */
	function login(){
	   $("#loginBtn").val('登录中...');
       var email = $("#email").val();
       var password = $("#pass").val();
       var emailError = passwordError = '';
       var disable = false;
       
       if(!email){
           emailError = '邮箱不能为空';
       }else if(!isMail(email)){
           emailError = '邮箱格式不正确';
       }
       if(emailError){
           $("#emailBox").css('border','1px solid red');
		   $('#emailWwarning b').css('background-position','0 1px');
		   $("#emailWwarning font").css('color','#f00');
		   $("#emailWwarning font").text(emailError);
		   $("#emailWwarning").show();
           disable = true;
       }
       
       if(!password){
           passwordError = '密码不能为空';
       }else if(password.length<6 || password.length>20){        
           passwordError = '密码长度为6-20位';
       }
       if(passwordError){
           $("#passBox").css('border','1px solid red');
		   $('#passWwarning b').css('background-position','0 1px');
		   $("#passWwarning font").css('color','#f00');
		   $("#passWwarning font").text(passwordError);
		   $("#passWwarning").show();
           disable = true;
       }
       
       if(disable){
           ZENG.msgbox.show('请正确填写信息', 5, 3000);
           $("#loginBtn").val('登录');
           return false;
       }
       
       var url = '/index.php/User/loginAct';
       var data = {email:email,password:password};
       $.post(url, data, function(data){
            if(data.email != ''){
               $("#emailBox").css('border','1px solid red');
    		   $('#emailWwarning b').css('background-position','0 1px');
    		   $("#emailWwarning font").css('color','#f00');
    		   $("#emailWwarning font").text(data.email);
    		   $("#emailWwarning").show(); 
               disable = true;
            }
            
            if(data.enabled){
            	ZENG.msgbox.show(data.enabled, 5, 3000);
            	$("#loginBtn").val('登录');
                return false;
            }
            
            if(data.password != ''){
                $("#passBox").css('border','1px solid red');
    		    $('#passWwarning b').css('background-position','0 1px');
    		    $("#passWwarning font").css('color','#f00');
    		    $("#passWwarning font").text(data.password);
    		    $("#passWwarning").show();
                disable = true;
            }
            
            if(disable){
                ZENG.msgbox.show('请正确填写信息', 5, 3000);
                $("#loginBtn").val('登录');
                return false;
            }else{
                location.href = "/user.html";
            }        
       }, 'JSON'); 
	}
	
	/* 回车登录 */
	function keyEnterLogin(event){
		event = event ? event : window.event;
		if(event.keyCode == 13){
			login();
		}
	}
    
    /* 找回密码检测输入邮箱 */
    function checkEmail(){
        var email = $("#forgetPwdEmail").val();
        var error = '';
        
        if(!email){
            error = '请输入您注册时填写的邮箱';
        }else if(!isMail(email)){
            error = '邮箱格式不正确';
        }
        
        if(error){
            $(".error font").text(error);
            $(".error").show();
            return false;
        }
        
        var url = '/index.php/User/checkMail';
        var data = {email:email};
        $.post(url, data, function(data){
            if(data == '0'){
                error = '您输入的邮箱不存在';
            }
            
            if(error){
                $(".error font").text(error);
                $(".error").show();
                return false;
            }else{
                window.location.href="/checkQuestion.html";
            }
        })
    }
    
    /* 找回密码输入邮箱时点击回车 */
	function checkKey(e){
	   e = (e) ? e : window.event;
       if(e.keyCode == '13'){
           checkEmail();
       }
	}
    
    
    /* 提交找回密码问题答案 */
	function submitQuestion(){
	   var answer = $("#question").val();
       var error = '';
       
       if(!answer){
          error = '提示问题答案不能为空';
       }
       
       var url = "/index.php/User/checkAnswer";
       var data = {answer:answer};
       $.post(url, data, function(data){
            if(data == '1'){
                error = '提交失败';
            }else if(data == '2'){
                error = '密码提示答案错误';
            }
            
            if(error){
                $(".error font").text(error);
                $(".error").show();
                return false;
            }
            window.location.href="/resetPwd.html";
       });
       
	}
	

	/* 地区发生变化 */
    function regionChange(val, leval){
        var url = '/index.php/Common/regionChange';
        var data = {val:val,leval:leval};
        $.post(url, data, function(data){
            if(leval == 1){
                var zone = data.split(',');
                $("#city").html(zone[0]);
                $("#district").html(zone[1]);
            }else{
                $("#district").html(data);
            }
        });
    }    
	
    /* 检查收获地址 */
    function checkAddress(){
        var province = $("#province").val();
        var city = $("#city").val();
        var district = $("#district").val();
        var consignee = $("#consignee").val();
        var email = $("#email").val();
        var phone = $("#phone").val();
        var zipcode = $("#zipcode").val();
        var detailedAddress = $("#detailedAddress").val();
        var error = '';
        
        if(province == 0 || city == 0 || district == 0){
            error += '请选择配送地区\n';
        }
        if(!consignee){
            error += '收货人姓名不能为空\n';
        }
        if(!email){
            error += '邮箱不能为空\n';
        }else if(!isMail(email)){
            error += '邮箱格式不正确\n';
        }
        if(!zipcode){
            error += '邮政编码不能为空\n'; 
        }
        if(!phone){
            error += '手机号码不能为空\n';
        }else if(!isPhone(phone)){
            error += '手机号码格式不正确\n';
        }
        if(!detailedAddress){
            error += '详细信息不能为空';
        }
        if(error){
            alert(error);
            return false;
        }
    }
    
    /* 删除收藏商品 */
    function delCollectGoods(id){
        var con = confirm("您真的确定要删除吗？");
        if(!con){
            return false;
        }
        
        var url = "/index.php/User/delCollectGoods";
        var data = {id:id};
        $.post(url, data, function(data){
            if(data == '1'){
                alert("删除成功");
                window.location.reload();
            }else{
                /*
            	*   1、警告  4、成功   5、失败    6、加载
           	    */
            	ZENG.msgbox.show('删除失败', 5, 3000);
            }
        }) 
    }
	
    /* 取消订单 */
    function cancelOrder(id){
        var con = confirm('您真的要取消订单吗？');
        if(!con){
            return false;
        }

        ZENG.msgbox.show('正在取消订单...', 6,0);
        var url = '/index.php/Order/cancelOrder';
        var data = {id:id};
        $.post(url, data, function(data){
            if(data == '3'){
                ZENG.msgbox.show('取消订单成功', 4,3000);
                $(".cnenterOrderBox #order_"+id+" td:last").html("<font color='red'>已取消</font>");
            }else if(data == '2'){
                ZENG.msgbox.show('取消订单失败', 5,3000);
            }else if(data == '1'){ //没有接收到要取消的订单id
                ZENG.msgbox.show('操作失败...', 5,3000);
            }else{
                ZENG.msgbox.show('系统出现错误，请稍后重试...', 5,3000);
            }
        });
    }
    
    /* 确认收货 */
    function confirmGoods(id){
    	var con = confirm('确定已收到货了吗？');
        if(!con){
            return false;
        }
        
        ZENG.msgbox.show('正在处理您的操作...', 6,0);
        var data = {id:id};
        var url = '/index.php/Order/confirmGoods';
        $.post(url, data, function(data){
        	if(data == '3'){
                ZENG.msgbox.show('操作成功', 4,3000);
                $(".cnenterOrderBox #order_"+id+" .order_status").html("<font color='blue'>已确认</font>");
                $(".cnenterOrderBox #order_"+id+" td:last").html("<font color='blue'>已确认</font>");
            }else if(data == '2'){
                ZENG.msgbox.show('确认收货失败...', 5,3000);
            }else if(data == '1'){ //没有接收到要取消的订单id
                ZENG.msgbox.show('操作失败...', 5,3000);
            }else{
                ZENG.msgbox.show('系统出现错误，请稍后重试...', 5,3000);
            }
        })
        
        
        
    }
	
	