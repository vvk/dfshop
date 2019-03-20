<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      订单管理
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class OrderAction extends commonAction{
	
    /*
    *   @desc    取消订单
    *   @access  public
    *   @return  void
    *   @date    2014-04-18
    */ 
    public function cancelOrder(){
        $_SESSION['user'] or die('0');  //没有登录返回0
        
        $data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

        //没有接收到要删除订单的id
        if($data['id'] == 0){
            echo 1;
            exit();
        }
       
        $data['is_cancel'] = 1; 
        $m = M("order_info");
        if($m->create($data)){
            if($m->save()){
                echo 3;
            }else{
                echo 2;
            }
        }else{
            echo 2;
        }      
    }
    
    /*
    *   @desc    确认收货
    *   @access  public
    *   @return  void
    *   @date    2014-05-09
    */
    public function confirmGoods(){
    	$_SESSION['user'] or die('0');  //没有登录返回0
    	
    	$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	//没有接收到确认收货订单id
    	if($data['id'] == 0){
    		echo 1;
    		exit();
    	}
    	 
    	$data['confirm_status'] = 1;
    	$data['confirm_time'] = time();
    	$m = M("order_info");
    	if($m->create($data)){
    		if($m->save()){
    			echo 3;
    		}else{
    			echo 2;
    		}
    	}else{
    		echo 2;
    	}
    }
    
    /**
    *   支付宝服务器异步通知
    */
    public function alipay_notify(){
        import('@ORG.alipay.alipay_notify');
        
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner'] = C('alipty_partner');
        
        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = C('alipay_key');
      
        //签名方式 不需修改
        $alipay_config['sign_type'] = C('alipay_sign_type');
        
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= C('apliay_input_charset');
        
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = C('apliay_cacert');
        
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = C('apilay_transport');
        
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功
        	//请在这里加上商户的业务逻辑程序代        	
        	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——        	
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        	
        	//商户订单号        
        	$out_trade_no = $_POST['out_trade_no'];
        
        	//支付宝交易号        
        	$trade_no = $_POST['trade_no'];
        
        	//交易状态
        	$trade_status = $_POST['trade_status'];
        
            $m = M('order_info');
            $where = array('order_sn'=>$out_trade_no);
        
        	if($_POST['trade_status'] == 'WAIT_BUYER_PAY') {
        	    //该判断表示买家已在支付宝交易管理中产生了交易记录，但没有付款
     	  	    //判断该笔订单是否在商户网站中已经做过处理
       		    //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
       		    //如果有做过处理，不执行商户的业务程序
      			
                echo "success";		//请不要修改或删除
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        	else if($_POST['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
        	    //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
        		//判断该笔订单是否在商户网站中已经做过处理
        		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        		//如果有做过处理，不执行商户的业务程序

                $data['order_status'] = 1;
                $data['shipping_status'] = 0;
                $data['confirm_status'] = 0;
                $m->where($where)->save($data);   
                    
                echo "success";		//请不要修改或删除
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        	else if($_POST['trade_status'] == 'WAIT_BUYER_CONFIRM_GOODS') {
        	    //该判断表示卖家已经发了货，但买家还没有做确认收货的操作
        		//判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        		//如果有做过处理，不执行商户的业务程序

                $data['order_status'] = 1;
                $data['shipping_status'] = 1;
                $data['confirm_status'] = 0;
                $m->where($where)->save($data);
                
                echo "success";		//请不要修改或删除
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        	else if($_POST['trade_status'] == 'TRADE_FINISHED') {
        	    //该判断表示买家已经确认收货，这笔交易完成
        		//判断该笔订单是否在商户网站中已经做过处理
        		//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        		//如果有做过处理，不执行商户的业务程序
        			
                $data['order_status'] = 1;
                $data['shipping_status'] = 1;
                $data['confirm_status'] = 1;
                $m->where($where)->save($data);    
                    
                echo "success";		//请不要修改或删除
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }else {
        		//其他状态判断
                echo "success";
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult ("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        }
        else {
            //验证失败
            echo "fail";
        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }  
    }
    
    /**
    *   支付宝服务器同步通知
    */
    public function alipay_return(){
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner'] = C('alipty_partner');
        
        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key'] = C('alipay_key');
      
        //签名方式 不需修改
        $alipay_config['sign_type'] = C('alipay_sign_type');
        
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= C('apliay_input_charset');
        
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert'] = C('apliay_cacert');
        
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport'] = C('apilay_transport');
        
        import('@ORG.alipay.alipay_notify');
        
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        
    	$out_trade_no = $_GET['out_trade_no'];  //商户订单号
    	$trade_no = $_GET['trade_no'];    	    //支付宝交易号
    	$trade_status = $_GET['trade_status'];  //交易状态
        
        $m = M('order_info');
        if($_GET['trade_status'] == 'WAIT_SELLER_SEND_GOODS') {
            //该判断表示买家已在支付宝交易管理中产生了交易记录且付款成功，但卖家没有发货
            //判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
            //买家付款---商品发货---买家确认收货----交易完成
            $where = array('order_sn'=>$out_trade_no);
            $data['order_status'] = 1;
            $data['shipping_status'] = 0;
            $data['confirm_status'] = 0;
            $m->where($where)->save($data);
        }
        
        $field = 'consignee,pay_fee,phone,integral,gainIntegral,province,city,address';
        $where = array('order_sn'=>$out_trade_no);
        $order = $m->field($field)->where($where)->find();
        $province = $this->getRegion($order['province']);
        $city = $this->getRegion($order['city']);

        echo '恭喜您支付成功，您的订单号为'.$order['order_sn'].',您本次共计支付<b>'.$order['pay_fee'].'</b>元，获得<b>'.$order['gainIntegral'].'</b>积分<br>';
        echo '收货人为：<span style="color:red;font-weight:bold">'.$order['consignee'].'</span>，收货地址为：';
        echo '商家将会以最快的速度发货，请保持通信畅通，祝您购物愉快！<br>';
        echo '<a href="'.$this->host.'">继续购物</a>';
   }
    
}