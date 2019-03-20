<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      用户信息操作
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class UserAction extends commonAction{
    /**
    *   @desc    用户名中心首页
    *   @access  public
    *   @return  void
    *   @date    2014-03-30
    */ 
    public function index(){
        //没有登录跳转到登录页面
        if(!$this->isLogin()){
            header("location:./login.html");
            exit();
        }      
        
        //用户信息
        $info = $this->getUserById($_SESSION['user']['id']); 
        $info['shop_name'] = $this->_CFG['shop_name']['value'];
        $info['last_login_ip'] = $_SESSION['user']['last_login_ip'];
        $info['last_login_time'] = date("Y-m-d H:i:s", $_SESSION['user']['last_login_time']);
        
        //订单信息
        $m = M('order_info');
        $info['shippingTotal'] = $m->where(array('user_id'=>$_SESSION['user']['id']))->count();   //订单总数
        $info['noPayShipping'] = $m->where(array('user_id'=>$_SESSION['user']['id'],'order_status'=>0))->count();  //未付款订单
        $info['payShipping'] = $info['shippingTotal'] - $info['noPayShipping'];   //已付款订单

        $this->assign('web_title','用户中心');
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);
        $this->assign('description', $this->_CFG['shop_desc']['value']);
        $this->assign('user_notice', $this->_CFG['user_notice']['value']);   //用户中心公告
        $this->assign('info', $info);
        $this->display();
    }   
    
    /**
    *   @desc    用户登录
    *   @access  public
    *   @return  void
    *   @date    2014-03-25
    */   
	public function login(){	
	    if($this->isLogin()){
            header("location:./login.html");
            exit();
        }
        
        if($this->qc){
            $this->assign('qqLoginUrl', $this->qc->getLoginUrl());
        }
        
        $this->assign('sinaLoginUrl', $this->sina->getAuthorizeURL(C('sina_callback')));
               
		$this->assign('web_title', '用户登录');
		$this->assign('keywords', $this->_CFG['shop_keywords']['value']);
		$this->assign('description', $this->_CFG['shop_desc']['value']);
		$this->display();
	}
    
    public function loginAct(){
        $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
        $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
        
        $m = M('users');
        
        if(!$email){
            $result['email'] = '邮箱不能为空';
        }elseif(!isEmail($email)){
            $result['email'] = '邮箱格式不正确';
        }else{
            if(!$password){
                $result['password'] = '密码不能为空';
            }elseif(strlen($password)<6 || strlen($password)>20){
                $result['password'] = '密码长度为6-20位';
            }else{
                $result['password'] = '';
            }
            
            if($result['password'] == ''){
                $where = array("email"=>$email);
                $field = 'id,user_name,password,email,sex,birthday,question_id,answer,reg_time,reg_ip,';
                $field .= 'last_login_time,last_login_ip,visit_count,salt,qq,phone,photo,integral,';
                $field .= 'is_approve,province,city,district,enabled';
                $user = $m->where($where)->field($field)->find();
            
                if(!$user){
                    $result['email'] = '您输入的邮箱不存在';
                }else{
                    if($user['password'] != md5(md5($password).$user['salt'])){
                        $result['password'] = '您输入的密码有错';
                    }   
                    $result['email'] = '';
                }            
            }
            
            //判断用户是否被禁用
            if($user['enabled'] == 1){
            	$result['enabled'] = '您的帐号已被禁用';
            	echo json_encode($result);
            	exit();
            }else{
            	$result['enabled'] = '';
            }            
                   
            if($result['email'] != '' || $result['password'] != ''){                
                echo json_encode($result);
                exit();
            }
            $_SESSION['user'] = $user ;//用户信息放在session中               
                
            $where = array('id'=>$user['id']);
            $m->where($where)->setInc('visit_count', 1);
            
            $data['last_login_time'] = time();
            $data['last_login_ip'] = get_client_ip();
            $data['salt'] = rand(1000,9999);
            $data['password'] = md5(md5($password).$data['salt']);
            
            $m->where($where)->save($data);
            $result['success'] = 1;
            echo json_encode($result);           
        } 
    }
	
    /**
    *   @desc    用户退出
    *   @access  public
    *   @return  void
    *   @date    2014-03-30
    */
	public function logout(){
		unset($_SESSION['user']);
	}
	
    /**
    *   @desc    用户注册
    *   @access  public
    *   @return  void
    *   @date    2014-03-26
    */ 
	public function register(){
	    //判断网站是否关闭
	    if($this->_CFG['shop_reg_closed']['value'] == 1){
            $this->referer('该网店暂停注册');
            exit();   
	    }
    
        //判断用户是否登录
	    if($this->isLogin()){
            header("location:./profile.html");
            exit();
        }
       
		$this->assign('web_title', '用户注册');	
		$this->assign('keywords', $this->_CFG['shop_keywords']['value']);
		$this->assign('description', $this->_CFG['shop_desc']['value']);
		$this->display();
	}    
    
    /**
    *   @desc    检查邮箱是否已被注册
    *   @access  public
    *   @return  void
    *   @date    2014-03-29
    */    
    public function isRegister(){
        $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
      
        /*if(!$email){
            echo 1;  //没有接收到值
            exit();
        }elseif(!isEmail($email)){
            echo 2;   //不是邮箱
            exit();
        }*/
 
        $m = M('users');
        $where = array('email'=>$email);
        $count = $m->where($where)->count();
        
        if($count>0){
            echo 3;  //邮箱已被注册
        }else{
            echo 0;
        }
    }  
    
    /**
    *   @desc    检查邮箱是否已被注册
    *   @access  public
    *   @return  void
    *   @date    2014-03-29
    */ 
    public function checkVerify(){
        $verify = isset($_REQUEST['verify']) ? trim($_REQUEST['verify']) : '';
        
        if($_SESSION['verify'] == md5($verify)){
            echo 1;
        }else{
            echo 0;
        } 
    }
    
    /**
    *   @desc    注册处理
    *   @access  public
    *   @return  void
    *   @date    2014-03-29
    */ 
    public function registerAct(){
        $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
        $email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : ''; 
        $password = isset($_REQUEST['repassword']) ? trim($_REQUEST['repassword']) : '';
        $repassword = isset($_REQUEST['repassword']) ? trim($_REQUEST['repassword']) : '';
        $verify = isset($_REQUEST['verify']) ? trim($_REQUEST['verify']) : '';
        
        $m = M('users');        
        
        if(!$username){
            $result['username'] = '用户名为空';  
        }elseif(strlen($username)<6 || strlen($username)>20){
            $result['username'] = '用户名为6-20位字母或数字';   
        }elseif(!isLetNum($username)){
            $result['username'] = '用户名只能为字母或数字';   
        }else{
            $result['username'] = '';   //用户名可用
        }
        
        if(!$email){
            $result['email'] = '邮箱不能为空';  
        }elseif(!isEmail($email)){
            $result['email'] = '邮箱格式不正确';  
        }else{
            $where = array('email'=>$email);
            $count = $m->where($where)->count();
            if($count>0){
                $result['email'] = '邮箱已注册'; 
            }else{
                $result['email'] = '';  //邮箱可用
            }
        }
        
        if(!$password){
            $result['password'] = '密码不能为空' ;  
        }elseif(strlen($password)<6 || strlen($password)>20){
            $result['password'] = '密码长度淡6-20位' ;  
        }else{
            $result['password'] = '';
        }
        
        if(!$repassword){
            $result['repassword'] = '确认密码不能为空'; 
        }elseif(strlen($repassword)<6 || strlen($repassword)>20){
            $result['repassword'] = '确认密码为6-20位' ;
        }elseif($password != $repassword){
            $result['repassword'] = '两次输入的密码不一致' ; 
        }else{
            $result['repassword'] = '' ;
        }
        
        if(!$verify){
            $result['verify'] = '验证码不能为空' ; 
        }elseif($_SESSION['verify'] != md5($verify)){
            $result['verify'] = '验证码错误' ;  
        }else{
            $result['verify'] = '';
        }
        
        if($result['username'] != '' || $result['email'] != '' || $result['repassword'] != '' || $result['password'] != '' || $result['verify'] != ''){
            echo json_encode($result);
            exit();
        }

        $data['user_name'] = $username;
        $data['email'] = $email;
        $data['salt'] = rand(1000, 9999);
        $data['password'] = md5(md5($password).$data['salt']);
        $data['sex'] = 0;  
        $data['birthday'] = date("Y", time()).'-'.date("m", time()).'-'.date("d", time());
        $data['question_id'] = 0;
        $data['answer'] = '';
        $data['reg_time'] = time();
        $data['reg_ip'] = get_client_ip();
        $data['last_login_time'] = time();
        $data['last_login_ip'] = get_client_ip();
        $data['visit_count'] = 1;   
        $data['qq'] = '';
        $data['phone'] = '';
        $data['photo'] = '';
        $data['integral'] = $this->_CFG['register_points']['value'];  //注册赠送积分
        $data['is_approve'] = 1;  //此功能尚未完善   0：未谁 1：认证
        $data['province'] = 0;
        $data['city'] = 0;
        $data['district'] = 0;
        $data['enabled'] = 0;   //  0:未禁用   1:禁用 
        
        if($m->create($data)){
            if($m->add()){
                $result['success'] = 1;
                $where = array('email'=>$data['email']);
                $user = $m->where($where)->find();
                $_SESSION['user'] = $user;
            }else{
                $result['success'] = 13;
            }
        }else{
            $result['success'] = 12;
        }
        
        echo json_encode($result);
    }
    
    /**
    *   @desc    编辑用户信息
    *   @access  public
    *   @return  void
    *   @date    2014-03-30
    */ 
    public function profile(){
       if(!$this->isLogin()){
           header("location:./login.html");
           exit();
       }
       
       //密码提示问题
       $m = M("reg_question");
       $field = "id,question";
       $order = "id ASC";
       $question = $m->field($field)->order($order)->select();     
       
       //查询地区
       $region = M("region");
       $field = "id,parent_id,region_name";
       if($_SESSION['user']['province'] == 0){
            $where = array("parent_id"=>$this->province[0]['id']);
            //$where = array("parent_id"=>1);
       }else{
            $where = array("parent_id"=>$_SESSION['user']['province']);
       }     
       $city = $region->field($field)->where($where)->select();
       
       if($_SESSION['user']['city'] == 0){
            $where = array("parent_id"=>$city[0]['id']);
       }else{
            $where = array("parent_id"=>$_SESSION['user']['city']);
       } 
       $district = $region->field($field)->where($where)->select();       
       
       $this->assign('province', $this->province);  //省
       $this->assign('city', $city);    //市
       $this->assign('district', $district);  //地区
       $this->assign('question', $question);
       $this->assign('menu', 'profile');
       $this->assign('web_title', '我的信息');
       $this->assign('keywords', $this->_CFG['shop_keywords']['value']);
       $this->assign('description', $this->_CFG['shop_desc']['value']);
       $this->display();        
    }
    
    /**
    *   @desc    个人中心修改密码
    *   @access  public
    *   @return  void
    *   @date    2014-04-16
    */ 
    public function resetPassword(){
        //没有登录跳转到登录页面
        if(!$this->isLogin()){
            header("location:./login.html");
            exit();
        }        
       
        $this->assign('menu', 'resetPassword');
        $this->assign('web_title', '修改密码');
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);
        $this->assign('description', $this->_CFG['shop_desc']['value']);
        $this->display();
    }
    
    /**
    *   @desc    查看密码是否正确
    *   @access  public
    *   @return  void
    *   @date    2014-04-16
    */ 
    public function checkPassword(){
        $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
        
        if(!$password){
            echo 0;
        }
 
        $m = M("users");
        $field = 'password,salt';
        $user = $m->field($field)->find($_SESSION['user']['id']);

        if($user['password']){
            if(md5(md5($password).$user['salt']) == $user['password']){
                echo 2;   //密码正确
            }else{
                echo 1;  //密码不正确
            }  
        }else{
            echo 0;
        } 
    }
    
    /**
    *   @desc    个人中心重置密码
    *   @access  public
    *   @return  void
    *   @date    2014-04-16
    */ 
    public function savePassword(){
        $data['password'] = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
        $data['newpassword'] = isset($_REQUEST['newpassword']) ? trim($_REQUEST['newpassword']) : '';
        $data['renewpassword'] = isset($_REQUEST['renewpassword']) ? trim($_REQUEST['renewpassword']) : '';

        if(!$data['password'] || !$data['newpassword'] || !$data['renewpassword']){
            $this->error("请将信息填写完整");
            exit();
        }
        
        if( $data['newpassword'] != $data['renewpassword']){
            $this->error('两次输入的密码不一致');
            exit();
        }       
        
        $m = M("users"); 
        $field = 'password,salt';
        $user = $m->field($field)->find($_SESSION['user']['id']);
        if(!$user['password']){
            $this->error('系统出现错误，请稍后再试……');
            exit();
        }
        
        if(md5(md5($data['password']).$user['salt']) != $user['password']){
            $this->error("原密码不正确");
            exit();
        }
        
        $save['id'] = $_SESSION['user']['id'];
        $save['salt'] = rand(1000,9999);
        $save['password'] = md5(md5($data['newpassword']).$save['salt']);    
        
        if($m->create($save)){
            if($m->save()){
                $this->logout();
                $this->success("修改成功，请重新登录", '/login.html');
            }else{
                $this->error("修改失败");
            }
        }else{
            $this->error("修改失败");
        }
    }
    
    /**
    *   @desc    个人中心--我的订单
    *   @access  public
    *   @return  void
    *   @date    2014-04-17
    */ 
    public function order(){
        //没有登录跳转到登录页面
        if(!$this->isLogin()){
            header("location:./login.html");
            exit();
        }        
        
        import('ORG.Util.Page');// 导入分页类
		$size = 10;    //每页显示条数
       
        $m = M('order_info'); 
        $field = 'id,order_sn,order_status,add_time,shipping_status,confirm_status,pay_fee,is_cancel';
        $order = 'id desc';   //按id倒序排序
        $where = array('user_id'=>$_SESSION['user']['id']);
        $count = $m->where($where)->count();    //订单总数
        
        $page = new Page($count, $size);   //声明分页对象
        $page->setConfig('header','个订单');
        $page->setConfig('theme', "共有 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
        $order = $m->field($field)->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();

        foreach($order as $key=>$val){
            $order[$key]['time'] = date($this->_CFG['time_format']['value'], $val['add_time']);
            
            if($val['confirm_status'] == 0){
            	//订单是否付款
            	if($val['order_status'] == 0){
            		$order[$key]['status'] = '未付款，';
            	}else{
            		$order[$key]['status'] = "<font color='red'>已付款，</font>";
            	}
            	//订单是否发货
            	if($val['shipping_status'] == 0){
            		$order[$key]['status'] .= '未发货';
            	}else{
            		$order[$key]['status'] .= "<font color='red'>已发货</font>";
            	}
            	
            	//对订单的操作
            	if($val['is_cancel'] == 1){
            		$order[$key]['handle'] = "<font color='red'>已取消</font>";
            	}elseif($val['order_status'] == 0){
            		$order[$key]['handle'] = "<a href='javascript:void(0)' onclick='cancelOrder(".$val['id'].")'>取消订单</a>";
            		$order[$key]['handle'] .= $this->alipay($val['order_sn'], $val['pay_fee']);
            	}elseif($val['order_status'] == 1 && $val['shipping_status'] == 0){
            		$order[$key]['handle'] = "<font color='blue'>发货中...</font>";
            	}else{
            		$order[$key]['handle'] =  "<a href='javascript:void(0)' onclick='confirmGoods(".$val['id'].")'>确认收货</a>";
            	}
            }else{
            	$order[$key]['status'] .= "<font color='blue'>已确认</font>";
            	$order[$key]['handle'] =  "<font color='blue'>已确认</font>";
            }           
        }
        
        $this->assign('count', $count);
        $this->assign('order', $order);
        $this->assign('page_list', $page_list);
        $this->assign("menu", 'order');
        $this->assign('web_title', '我的订单');
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);
        $this->assign('description', $this->_CFG['shop_desc']['value']);
        $this->display();
    }
    
    /**
    *   @desc    个人中心--我的订单详细信息
    *   @access  public
    *   @return  void
    *   @date    2014-04-17    2014-04-18
    */ 
    public function centerOrderInfo(){
        //没有登录跳转到登录页面
        if(!$this->isLogin()){
            header("location:./login.html");
            exit();
        }        
        
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        
        //没有接收到id跳转到前一页面
        if($id == 0){
            $url = $_SERVER['HTTP_REFERER'];   //前一页面
            if(!$url){    //前一页面不存在跳转到首页
                $url = '/';
            }
            $this->error('订单id丢失...', $url);
            exit();
        }
        
        $order_info = M('order_info');
        $field = 'id,order_sn,order_status,shipping_status,add_time,shipping_time,confirm_time,';
        $field .= 'pay_time,pay_id,integral,goods_amounts,shipping_fee,pay_fee';
        $order = $order_info->field($field)->find($id);  //订单信息
        
        $info['integral'] = $order['integral'];
        $info['goods_amounts'] = $order['goods_amounts'];
        $info['shipping_fee'] = $order['shipping_fee'];
        $info['pay_fee'] = $order['pay_fee'];
        $info['order_sn'] = $order['order_sn'];   
        
        $rate = $this->_CFG['integral_scale']['value'];   //每100积分可抵现金
        $info['integral_money'] = number_format(floor(($order['integral']/100))*$rate, 2);
        
             
        if($order['order_status'] == 0){
            $info['order_status'] = '未付款';
        }else{
            $info['order_status'] = '已付款';
        }
        
        if($order['shipping_status'] == 0){
            $info['shipping_status'] = '未发货';
        }else{
            $info['shipping_status'] = '已发货';
        }
        
        $info['add_time'] = date('Y-m-d H:i:s', $order['add_time']);
        $info['pay_time'] = date('Y-m-d H:i:s', $order['pay_time']);
                
        $pay = M('payment');
        $res = $pay->field('pay_name')->find($order['pay_id']);
        $info['pay_name'] = $res['pay_name'];      
        
        $order_goods = M('order_goods');
        $field = 'id,goods_id,goods_name,goods_sn,goods_number,market_price,shop_price';
        $where = array('order_id'=>$order['id']);
        $goods = $order_goods->field($field)->where($where)->select();
        foreach($goods as $key=>$val){
            $goods[$key]['money'] = number_format($val['shop_price']*$val['goods_number'], 2);
        }
        $info['goods'] = $goods;
    
        $this->assign('info', $info);
        $this->assign('goods', $goods);
        $this->assign("menu", 'order');
        $this->assign('web_title', '我的订单');
        $this->display();      
    }
    
    /**
    *   @desc    收货地址
    *   @access  public
    *   @return  void
    *   @date    2014-04-15
    */ 
    public function address(){
        //没有登录跳转到登录页面
        if(!$this->isLogin()){
            header("location:./login.html");
            exit();
        }
        
        $id = $_SESSION['user']['id'];
        $m = M("users_address");
        $field = "id,consignee,email,province,city,district,address,zipcode,phone";
        $address = $m->where(array('user_id'=>$id))->field($field)->find();
  
        if($address){
        	$address['provinceRetion'] =  $this->regionOption($this->province, $address['province']);
        	$city = $this->getSubRegion($address['province']);
        	$address['cityRegion'] =  $this->regionOption($city, $address['city']);
        	$city = $this->getSubRegion($address['city']);
        	$address['districtRegion'] =  $this->regionOption($city, $address['district']);
        }else{
        	$address['provinceRetion'] = $this->province;
        	$address['cityRegion'] = $this->getSubRegion($address['provinceRetion'][0]['id']);
        	$address['districtRegion'] = $this->getSubRegion($address['cityRegion'][0]['id']);
        	
        	$address['provinceRetion'] = $this->regionOption($address['provinceRetion']);
        	$address['cityRegion'] = $this->regionOption($address['cityRegion']);
        	$address['districtRegion'] = $this->regionOption($address['districtRegion']);
        }  
        
        $this->assign('address', $address);
        $this->assign('province', $this->province);
        $this->assign("menu", 'address');
        $this->assign('web_title', '收货地址');
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);
        $this->assign('description', $this->_CFG['shop_desc']['value']);
        $this->display();
    }
    
    /**
    *   @desc    保存收货地址
    *   @access  public
    *   @return  void
    *   @date    2014-04-15
    */
    public function saveAddress(){
        $data['consignee'] = isset($_REQUEST['consignee']) ? trim($_REQUEST['consignee']) : '';              $data['email'] = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
        $data['province'] = isset($_REQUEST['province']) ? intval($_REQUEST['province']) : 0;
        $data['city'] = isset($_REQUEST['city']) ? intval($_REQUEST['city']) : 0;
        $data['district'] = isset($_REQUEST['district']) ? intval($_REQUEST['district']) : 0;
        $data['address'] = isset($_REQUEST['address']) ? trim($_REQUEST['address']) : '';
        $data['zipcode'] = isset($_REQUEST['zipcode']) ? trim($_REQUEST['zipcode']) : '';
        $data['phone'] = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';
        $data['sign_building'] = '';
        $data['default'] = 0;
        
        $error = '';
        if(!$data['consignee']){
            $error = '收货人姓名不能为空&nbsp;&nbsp;';
        }
        if($data['province'] == 0 || $data['city'] == 0 || $data['district'] == 0){
            $error .= '配送地区不能为空&nbsp;&nbsp;';
        }
        if(!$data['email']){
            $error .= '邮箱不能为空&nbsp;&nbsp;';
        }elseif(!isEmail($data['email'])){
            $error = '邮箱格式不正确&nbsp;&nbsp';
        }
        if(!$data['address']){
            $error = '详细信息不能为空&nbsp;&nbsp';
        }
        if(!$data['zipcode']){
            $error = '邮政编码不能为空&nbsp;&nbsp';
        }
        if(!$data['phone']){
            $error .= '手机号码不能为空';
        }elseif(!isPhone($data['phone'])){
            $error .= '手机号码格式不正确';
        }
        
        if($error){
            $this->error($error);
            exit();
        }
        
        $m = D("users_address");
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

        if($id == 0){           
            $data['user_id'] = $_SESSION['user']['id'];
            if($m->create($data)){
                if($m->add()){
                    $this->success('添加成功');
                }else{
                    $this->error('添加失败');
                }
            }else{
                
                $this->error('添加失败');
            }
        }else{
            $data['id'] = $id;
            if($m->create($data)){
                if($m->save()){
                    $this->success('保存成功');
                }else{
                    $this->error('保存失败');
                }
            }else{                
                $this->error('保存失败');
            }
        }             
    } 
    
    /**
    *   @desc    个人中心我的收藏
    *   @access  public
    *   @return  void
    *   @date    2014-04-16
    */
    public function collect(){
        //没有登录跳转到登录页面
        if(!$this->isLogin()){
            header("location:./login.html");
            exit();
        }
        
        import('ORG.Util.Page'); // 导入分页类		
		$size = 10;              //每页显示数量
        
        $m = M("collect_goods");
        $order = 'id desc';
        $field = 'id,goods_id,add_time';
        $where = array('user_id'=>$_SESSION['user']['id']);
        $count = $m->where($where)->count();
        
        $page = new Page($count, $size);        //声明分页对象
        $page->setConfig('header','件收藏商品');
        $page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
        $collect = $m->field($field)->where($where)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
        
        $goods = M('goods');
        $field = 'goods_name,shop_price';
        foreach($collect as $key=>$val){
            $good = $goods->field($field)->where(array('id'=>$val['goods_id']))->find();
            $collect[$key]['goods_name'] = $good['goods_name'];           
            $collect[$key]['price'] = $good['shop_price'];
            $collect[$key]['time'] = date("Y-m-d H:i:s", $val['add_time']);   
        }

        $this->assign('count', $count);
        $this->assign('page_list', $page_list);
        $this->assign('collect', $collect);
        $this->assign("menu", 'collect');
        $this->assign('web_title', '我的收藏');
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);
        $this->assign('description', $this->_CFG['shop_desc']['value']);
        $this->display();
    }
    
    /**
    *   @desc    删除收藏商品
    *   @access  public
    *   @return  void
    *   @date    2014-04-16
    */
    public function delCollectGoods(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        if($id == 0){
            echo 0;  //没有接收到要删除商品的id
        }
        
        $m = M('collect_goods');
        if($m->delete($id)){
            echo 1;
        }else{
            echo 0;
        }
    }
 
    /**
    *   @desc    保存用户信息
    *   @access  public
    *   @return  void
    *   @date    2014-04-08
    */ 
    public function saveInfo(){
        $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
        $sex = isset($_REQUEST['sex']) ? intval($_REQUEST['sex']) : 0 ;
        $birthday = isset($_REQUEST['birthday']) ? trim($_REQUEST['birthday']) : date("Y-m-d");
        $qq = isset($_REQUEST['qq']) ? trim($_REQUEST['qq']) : '';
        $phone = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';
        $question_id = isset($_REQUEST['question_id']) ? intval($_REQUEST['question_id']) : 0;
        $answer = isset($_REQUEST['answer']) ? trim($_REQUEST['answer']) : '';
        $province = isset($_REQUEST['province']) ? intval($_REQUEST['province']) : 0;
        $city = isset($_REQUEST['city']) ? intval($_REQUEST['city']) : 0;
        $district = isset($_REQUEST['district']) ? intval($_REQUEST['district']) : 0;
        
        $error = '';
        
        if(!$username){
            $error = '用户名不能为空<br>';
        }elseif(strlen($username)<6 || strlen($username)>20){
            $error = '用户名长度为6-20位<br>';
        }elseif(!isLetNum($username)){
            $error = '用户名中能为字母或数字<br>';
        }
        
        if($phone){
            if(!isPhone($phone)){
                $error .= '手机号码格式不正确<br>';
            }
        }
        
        if($qq){
            if(!isQQ($qq)){
                $error = 'QQ号码格式不正确<br>';
            }
        }
        
        if($question_id != 0){
            if(!$answer){
                $error = '密码提示问题不能为空';
            }
        }
        
        if($error){
            $this->error($error);
            exit();
        }
        
        $m = M("users");

        $data['id'] = $_SESSION['user']['id'];
        $data['sex'] = $sex;
        $data['qq'] = $qq;
        $data['phone'] = $phone;
        $data['question_id'] = $question_id;
        $data['answer'] = $answer;
        $data['province'] = $province;
        $data['city'] = $city;
        $data['district'] = $district;
		$data['birthday'] = $birthday;
        
        if($m->create($data)){
            if($m->save()){
            	$user = $m->find($_SESSION['user']['id']);
            	$_SESSION['user'] = $user;            	
                $this->success('保存成功',"./profile.html");
            }else{
               $this->error('保存失败');
            }
        }else{
            $this->error('保存失败');
        }
    }
    
    /**
    *   @desc    找回密码页面--输入邮箱查看是否存在
    *   @access  public
    *   @return  void
    *   @date    2014-04-13
    */ 
    public function forgetPwd(){        
        $this->assign('web_title', '忘记密码');
        $this->display();
    }
    
    /**
    *   @desc    检测邮箱是否存在
    *   @access  public
    *   @return  void
    *   @date    2014-04-13
    */ 
    public function checkMail(){
        $email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
        
        $m = M('users');
        $where = array('email'=>$email);
        $count = $m->where($where)->count();
        
        if($count>0){
            $_SESSION['registerEmail'] = $email;
            echo 1;  //邮箱已被注册
        }else{
            echo 0;
        }
    }
    
    /**
    *   @desc    找回密码页面--输入密码提示问题
    *   @access  public
    *   @return  void
    *   @date    2014-04-13
    */ 
    public function checkQuestion(){
        $email = $_SESSION['registerEmail'];
        
        //没有接收到邮箱
        if(!$email){ 
            header("Location:./login.html");
            exit();
        }
        
        $m = M("users");
        $sql = "select `question` from `df_reg_question` as q,`df_users` where `email` = '".$email."'";
        $sql .= " and q.`id`=df_users.`question_id`";
        
        $question = $m->query($sql);

        $this->assign('question', $question[0]['question']);
        $this->assign('web_title', '找回密码');
        $this->display();
    }
    
    /**
    *   @desc   检测找回断码问题是否正确
    *   @access  public
    *   @return  void
    *   @date    2014-04-13
    */ 
    public function checkAnswer(){
        $answer =isset($_REQUEST['answer']) ? trim($_REQUEST['answer']) : '';
        
        if(!$answer){
            echo 1;  //没有接收到问题答案
            exit();
        }
        
        $email = $_SESSION['registerEmail'];
        $m = M("users");
        $where = array('email'=>$email,'answer'=>$answer);
        $count = $m->where($where)->count();
       
        if($count>0){
            $_SESSION['registerAnswer'] = $answer;
            echo 3;  //答案正确
        }else{
            echo 2;
        }  
    }
    
    /**
    *   @desc    找回密码页面--输入新密码
    *   @access  public
    *   @return  void
    *   @date    2014-04-13
    */ 
    public function resetPwd(){
        if(!$_SESSION['registerAnswer'] || !$_SESSION['registerEmail']){
            header("Location:./login");
            exit();
        }        
        
        $this->assign('web_title', '找回密码');
        $this->display();
    }
    
    /**
    *   @desc    找回密码页面--更新密码
    *   @access  public
    *   @return  void
    *   @date    2014-04-14
    */ 
    public function setNewPwd(){
        $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';

        if(!$password){
            echo 1;  //没有接收到密码
            exit();
        }elseif(strlen($password)<6 || strlen($password)>20){
            echo 2;  //长度超出范围
            exit();
        }   

        $m = M('users');
        $email = $_SESSION['registerEmail'];
        $data['salt'] = rand(1000, 9999);        
        $data['password'] = md5(md5($password).$data['salt']);
        $sql = "update df_users set salt ='".$data['salt']."',password='".$data['password']."' ";
        $sql .= "where email='".$email."'";
        $res = $m->execute($sql);
        if($res){
            echo 4;  //更新成功
        }else{
            echo 3;  //更新失败
        } 
    }
    
    public function sinaLogin(){
        $code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
        if(!$code){
            $this->error('登录失败', './login.html');
        }

        $keys = array();
    	$keys['code'] = $code;
    	$keys['redirect_uri'] = C('sina_callback');
    	$token = $this->sina->getAccessToken('code', $keys) ;
    	$_SESSION['token'] = $token;
     
        $this->weibo = new weibo(C('sina_app_key'), C('sina_app_secret'), $_SESSION['token']['access_token']);
        $uid_get = $this->weibo->get_uid();  //获取用户uid
        $user = $this->weibo->show_user_by_id($uid_get['uid']); //根据用户UID或昵称获取用户资料
        $this->weibboLoginAct($uid_get['uid'], $user);
    }

    /**
    *  新浪微博登录取消授权回调页地址
    */
    public function sinaNoAccess(){
        $this->error('登录失败', './login.html');
    }
    
    /**
     *  新浪微博登录
     *  @param  $uid       微博用户uid
     *  @param  $userInfo  微博用户信息
     */ 
    public function weibboLoginAct($uid, $userInfo){
        $m = M('users');
        $user = $this->isWeiboUser($uid);
        if(!empty($user)){//用户存在 
            //判断用户是否被禁用
            if($user['enabled'] == 1){
              $this->error('您的帐号已被禁用', './login.html');
              exit();
            }
            
            $data['user_name'] = $userInfo['name'];
            if($userInfo['gender'] == 'm'){
                $data['sex'] = 1;  
            }elseif($userInfo['gender'] == 'f'){
                $data['sex'] = 2;  
            }else{
                $data['sex'] = 0;  
            }
            $data['last_login_time'] = time();
            $data['last_login_ip'] = get_client_ip();
            
            $where = array('weibo_uid'=>$uid);
            $m->where($where)->setInc('visit_count', 1);  //登录次数加1
            $m->where($where)->save($data);
            $user['weibo_uid'] = $uid;
            $_SESSION['user'] = $user;
            $_SESSION['user']['visit_count']++;            
            $this->success('登录成功', '/profile.html');
        }else{//用户不存在
            $data['user_name'] = $userInfo['name'];
            $data['email'] = $uid.'.@qq.com';
            $data['salt'] = '';
            $data['password'] = '';
            if($userInfo['gender'] == 'm'){
                $data['sex'] = 1;  
            }elseif($userInfo['gender'] == 'f'){
                $data['sex'] = 2;  
            }else{
                $data['sex'] = 0;  
            }
            
            $data['birthday'] = date("Y", time()).'-'.date("m", time()).'-'.date("d", time());
            $data['question_id'] = 0;
            $data['answer'] = '';
            $data['reg_time'] = time();
            $data['reg_ip'] = get_client_ip();
            $data['last_login_time'] = time();
            $data['last_login_ip'] = get_client_ip();
            $data['visit_count'] = 1;   
            $data['qq'] = '';
            $data['phone'] = '';
            $data['photo'] = '';
            $data['integral'] = $this->_CFG['register_points']['value'];  //注册赠送积分
            $data['is_approve'] = 1;  //此功能尚未完善   0：未谁 1：认证
            $data['province'] = 0;
            $data['city'] = 0;
            $data['district'] = 0;
            $data['enabled'] = 0;   //  0:未禁用   1:禁用 
            $data['weibo_uid'] = $uid; 
            
            if($m->create($data)){
                if($m->add()){
                    $where = array('weibo_uid'=>$data['weibo_uid']);
                    $res = $m->where($where)->find();
                    $_SESSION['user'] = $res;
                    $this->success('登录成功', './profile.html');
                }else{
                    $this->error('登录失败', './login.html');
                }
            }else{
                $this->error('登录失败', './login.html');
            }
        }
    }
    
    /**
     *  判断是否为微博用户
     *  @param  $uid  用户id
     */ 
    public function isWeiboUser($uid){
        $m = M('users');
        $where = array('weibo_uid'=>$uid);
        $res  =$m->where($where)->find();
        if(!empty($res)){
            return $res;
        }else{
            return array();
        }
    }
    /*
    Array ( 
        [id] => 1306149274 
        [idstr] => 1306149274 
        [class] => 1 
        [screen_name] => 龙卷风-fighting2014 
        [name] => 龙卷风-fighting2014 
        [province] => 13 
        [city] => 2 
        [location] => 河北 唐山 
        [description] => 所谓的光辉岁月，并不是后来闪耀的日子，而是无人问津时，你对梦想的偏执，你是否有勇气，对自己忠诚到底。 
        [url] => http://blog.sina.com.cn/u/1306149274 
        [profile_image_url] => http://tp3.sinaimg.cn/1306149274/50/40050113219/1 
        [profile_url] => u/1306149274 
        [domain] => 
        [weihao] => 
        [gender] => m 
        [followers_count] => 205 
        [friends_count] => 150 
        [statuses_count] => 1420 
        [favourites_count] => 34 
        [created_at] => Tue Apr 12 15:41:51 +0800 2011 
        [following] => 
        [allow_all_act_msg] => 1 
        [geo_enabled] => 1 
        [verified] => 
        [verified_type] => 220 
        [remark] => 
        [status] => 
            Array ( 
              [created_at] => Sat Aug 09 13:29:23 +0800 2014 
              [id] => 3741649262068591 
              [mid] => 3741649262068591 
              [idstr] => 3741649262068591 
              [text] => 程序员应聘必备词汇：了解＝听过名 字；熟悉＝知道是啥；熟练＝用过；精通 ＝做过东西。 
              [source] => Android客户端 
              [favorited] => 
              [truncated] => 
              [in_reply_to_status_id] => 
              [in_reply_to_user_id] => 
              [in_reply_to_screen_name] => 
              [pic_urls] => Array ( ) 
              [geo] => 
              [annotations] => 
              Array (
                 [0] => Array ( 
                    [client_mblogid] => 
                    8489cb5e-ff09-4bc1-a519-7508fe090163 
                    )
              ) 
              [reposts_count] => 0 
              [comments_count] => 0 
              [attitudes_count] => 0 
              [mlevel] => 0 
              [visible] => Array ( 
                [type] => 0 
                [list_id] => 0 
                ) 
              [darwin_tags] => Array ( ) 
            ) 
        [ptype] => 0 
        [allow_all_comment] => 1 
        [avatar_large] => http://tp3.sinaimg.cn/1306149274/180/40050113219/1 
        [avatar_hd] => http://ww1.sinaimg.cn/crop.0.0.198.198.1024/4dda419agw1eenolv4sxoj205k05kaa8.jpg 
        [verified_reason] => 
        [verified_trade] => 
        [verified_reason_url] => 
        [verified_source] => 
        [verified_source_url] => 
        [follow_me] => 
        [online_status] => 1 
        [bi_followers_count] => 52 
        [lang] => zh-cn 
        [star] => 0 
        [mbtype] => 2 
        [mbrank] => 1 
        [block_word] => 0 
        [block_app] => 0 
    )
    */
}
