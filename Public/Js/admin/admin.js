/* 后台登录  */
function login(){
	ZENG.msgbox.show('正在登录...', 6, 0);

	var username = $('#username').val();
	var password = $("#password").val();

	if(!username){
		ZENG.msgbox.show('用户名不能为空', 5, 3000);
		return false;
	}
	if(!password){
		ZENG.msgbox.show('密码不能为空', 5, 3000);
		return false;
	}
	
	var url = '/admin.php/Privilege/loginAct';
	var data = {username:username,password:password};
    $.post(url, data,function(data){
        var error = '';
        if(data == '1'){
            error = '用户名不能为空';
        }else if(data == '2'){
            error = '密码不能为空';
        }else if(data == '3'){
            error= '您输入的帐号不存在';
        }else if(data == '4'){
            error ='帐号或密码错误';
        }else if(data == '0'){
            window.location.href='/admin.php';
            return false;
        }else{
            error = '登录失败';
        }
        
        if(error){
            ZENG.msgbox.show(error, 5, 3000);
            return false;
        }
    },"TEXT");
	//1、警告  4、成功   5、失败    6、加载
	//ZENG.msgbox.show('删除失败', 5, 3000);
}

/* 登录页面，如果按回车刚登录 */
function isEnter(event){
	event = event ? event : window.event;
    if(event.keyCode == 13){
        login();
    } 
}




