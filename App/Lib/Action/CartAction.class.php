<?php
header("Content-type:text/html;charset=utf-8");
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      购物车
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class CartAction extends commonAction{  
    
    /**
    *   @desc    购物车首页
    *   @access  public
    *   @return  void
    *   @date    2014-04-09
    */
    public function index(){    
    	if(!$this->isLogin()){
    		$this->error('您还没有登录，请登录后再操作', './login.html');
    		exit();
    	}

    	$m = M('cart');
    	$field = 'id,goods_id,goods_name,goods_number,shop_price,market_price';
    	$where = array('user_id'=>$_SESSION['user']['id']);
    	$order = 'time desc';
    	$cart = $m->field($field)->where($where)->order($order)->select();		
    	
    	
    	$m1 = M('goods');
    	$field = 'goods_thumb';
    	foreach($cart as $key=>$val){
    		$goods = $m1->field($field)->find($val['goods_id']);
    		$cart[$key]['goods_thumb'] = $goods['goods_thumb'];   //查询商品缩略图
    		$cart[$key]['money'] = number_format($val['goods_number']*$val['shop_price'], 2);
    	}
    	
    	$this->assign('cart', $cart);
        $this->assign('web_title', '我的购物车');
        $this->display();
    }
    
    /**
     *   @desc    确认订单
     *   @access  public
     *   @return  void
     *   @date    2014-05-02
     */
    public function confirmOrder(){
    	if(!$this->isLogin()){
    		header("Location:/login.html");
    		exit();
    	}
    	
    	$m1 = M('cart');
    	$field = 'id,goods_id,goods_name,goods_number,shop_price,market_price';
    	$order = 'id desc,time desc';
    	$where = array('user_id'=>$_SESSION['user']['id']);
    	$cart = $m1->field($field)->where($where)->order($order)->select();
    	
    	if(!$cart){
    		$this->referer('您的购物车中没有商品');
    		exit();
    	}	

    	$count = 0;
    	$totalPay = 0.00;
    	foreach ($cart as $key=>$val){
    		$count += $val['goods_number'];
			$totalPay += $val['shop_price']*$val['goods_number'];
    		$cart[$key]['money'] = number_format($val['shop_price']*$val['goods_number'], 2);
    	}
    	
    	//收货地址
    	$m2 = M('users_address');
 		$field = 'consignee,email,province,city,district,address,zipcode,phone';
 		$address = $m2->field($field)->where(array('user_id'=>$_SESSION['user']['id']))->find();
 		if(!$address){
 			$this->error('您的收货地址不存在，请先赶写收货地址', './address.html');
 			exit();
 		} 		
 		
		$shipping = $this->getShipping();   //配送方式
		$payment = $this->getPayment(); //支付方式

		//用户积分
		$m5 = M('users');
		$user = $m5->field('integral')->where(array('user_id'=>$_SESSION['user_id']))->find();

    	$this->assign('cart', $cart);                             //购物车商品
    	$this->assign('count', $count);                           //商品数量 
    	$this->assign('totalPay', number_format($totalPay, 2, '.', ''));   //商品总金额
    	$this->assign('address', $address);                       //收货地址 
    	$this->assign('shipping', $shipping);                     //配送方式
    	$this->assign('payment', $payment);                       //支付方式
    	$this->assign('integral', $user['integral']);             //用户积分
        $this->assign('web_title', '确认订单信息');
        $this->display();
    }
    
    /**
     *   @desc    提交订单
     *   @access  public
     *   @return  void
     *   @date    2014-05-02   2014-05-05
     */
    public function submitOrder(){
    	if(!$this->isLogin()){
    		header("Location:/login.html");
    		exit();
    	}
    	
    	if(!$_REQUEST['express']){
    		$this->error('您未选择配送方式');
    		exit();
    	}
    	if(!$_REQUEST['payment']){
    		$this->error('您未选择支付方式');
    		exit();
    	}

    	$goods = $this->getCartGoods(1);  //购物车中的商品		
    	if(!$goods){
    		$this->error('您的购物车中没有商品');
    		exit();
    	}

    	$goods_amounts = $goods_number = $gainIntegral = 0;
    	foreach($goods as $row){
    		$goods_amounts += $row['goods_number']*$row['shop_price'];
    		$goods_number += $row['goods_number'];
    		$good = $this->getGoodsById($row['goods_id']);
    		$gainIntegral += $good['integral']*$row['goods_number'];    //积分
    	}

    	$shipping = $this->getShipping($_REQUEST['express']);  //配送方式
    	$payment  = $this->getPayment($_REQUEST['payment']);   //支付方式
    	$address = $this->getUserAddress();                    //收货人信息
    	
    	$data['goods_amounts'] = number_format(($goods_amounts+$shipping['shipping_fee']+$payment['pay_fee']), 2, '.', '');   //商品总价钱
  		$data['goods_number'] = $goods_number;                       //商品数量 

  		//判断是否使用积分
  		if($_REQUEST['useIntegral']){
  			if($_REQUEST['integral']<100){
  				$data['integral'] = 0;
  			}else{
  				$data['integral'] = intval($_REQUEST['integral']);
  			}
  			$integral_scale = $this->_CFG['integral_scale']['value'];
  			$integral_money = floor($data['integral']/100)*$integral_scale;   //使用积分抵现金额
  			if($data['goods_amounts']<$integral_money){
  				$this->error('使用积分的抵现金额不能大于商品的总金额');
  				exit();
  			}
  			$pay_fee = $data['goods_amounts'] - $integral_money + $shipping[0]['shipping_fee'] + $payment[0]['pay_fee'];
  			$data['pay_fee'] = number_format($pay_fee, 2, '.', '');   //初实际需要支付的金额
  		}else{
  			$data['integral'] = 0;
  			$data['pay_fee'] = $data['goods_amounts'] + $shipping[0]['shipping_fee'] + $payment[0]['pay_fee'];
  		}
  		
		$user = $this->getUserById($_SESSION['user']['id']);
		if($data['integral']>$user['integral']){
			$this->error('您所使用的积分大于您的总积分');
			exit();
		}

  		$data['shipping_fee'] = $shipping[0]['shipping_fee'];
  		$data['payment_fee'] = $payment[0]['pay_fee'];
    	$data['order_sn'] = date('YmdHis').rand(1000, 9999);   //订单编号   年月日时分秒+四位随机数
    	$data['user_id'] = $_SESSION['user']['id'];
    	$data['order_status'] = 0;
    	$data['shipping_status'] = 0;
    	$data['consignee'] = $address['consignee'];
    	$data['phone'] = $address['phone'];
    	$data['province'] = $address['province'];
    	$data['city'] = $address['city'];
    	$data['district'] = $address['district'];
    	$data['address'] = $address['address'];
    	$data['zipcode'] = $address['zipcode'];
    	$data['email'] = $address['email'];
    	$data['shipping_id'] = $_REQUEST['express'];
    	$data['pay_id'] = $_REQUEST['payment'];
    	$data['add_time'] = time();
    	$data['confirm_time'] = '';
    	$data['pay_time'] = '';
    	$data['shipping_time'] = '';
    	$data['is_cancel'] = 0;
    	$data['gainIntegral'] = $gainIntegral;  //用户获得积分
    	
    	$m = M('order_info');
    	if(!$m->create($data)){
    		$this->error('订单生成失败');
    		exit();
    	}else{
    		if(!$m->add()){
    			$this->error('生成订单失败');
    			exit();
    		}
    	}
    	$order_id = $m->getLastInsID();
   	
    	$order_goods_sql = 'insert into df_order_goods(`order_id`,`goods_id`,`goods_name`,`goods_sn`,';
    	$order_goods_sql .= '`goods_number`,`market_price`,`shop_price`)values';
    	foreach($goods as $key=>$val){
    		$order_goods_sql .= "({$order_id},{$val['id']},'".$val['goods_name']."','".$val['goods_sn']."',";
    		$order_goods_sql .= "{$val['goods_number']},".number_format($val['shop_price'], 2, '.', '').",";
    		$order_goods_sql .= number_format($val['market_price'], 2, '.', '').'),';
    	}
    	$order_goods_sql = substr($order_goods_sql, 0, -1);
    	
    	$m2 = M('order_goods');
    	if(!$m2->execute($order_goods_sql)){
    		$m->delete($order_id);
    		$this->error('生成订单失败');
    		exit();
    	}
    	
    	$this->reduceInventory($goods);  //减少库存中商品的数量
    	
    	$m3 = M('cart');
    	$m3->where(array('user_id'=>$_SESSION['user']['id']))->delete();   //清除用户购物车信息
    	
    	//更新用户积分
    	$m4 = M('users');
    	$u['id'] = $_SESSION['user']['id'];
    	$u['integral'] = $user['integral']-$data['integral'];
    	$m4->create($u);
    	$m4->save($u);

    	$payment = $this->alipay($data);
    
        $this->assign('goods_amounts', $data['pay_fee']);   //支付金额
        $this->assign('gainIntegral', $gainIntegral);       //用户获得积分
        $this->assign('payment', $payment);                 //支付按钮
        $this->assign('web_title', '提交订单成功');
        $this->display();
    }

    /**
     *   @desc    保存购物车中的商品
     *   @access  public
     *   @return  void
     *   @date    2014-04-27  
     */
    public function saveCart(){
    	$arr = $_REQUEST;
    	
    	$m = M('cart');
    	$m2 = M('goods');
    	foreach($arr as $key=>$val){
    		if(preg_match('/cart/', $key)){
    			$cart = $m->field('goods_id,goods_name')->find(substr($key, 5));
    			$goods = $m2->field('goods_number')->find($cart['goods_id']);
    			if($val>$goods['goods_number']){
    				$this->error($cart['goods_name'].'库存不足');
    				exit();
    			}    			
    			$data['id'] = substr($key, 5);
    			$data['goods_number'] = $val;
    			$m->create($data);
    			$m->save();
    		}
    	}    	
    	header("location:".__APP__.'/Cart/');
    }
    
    /**
	 *   @desc    将商品添加到购物车
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-17
	 */
     public function addCart(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  
        $num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : 1;   //默认添加一件

        $m1 = M("goods");
        $field = 'goods_sn,goods_name,market_price,shop_price,goods_number';
        $goods = $m1->field($field)->find($id);

        if(!$goods){
            echo 0;   //查询的商品不存在
            exit();
        }
        
        if(!$_SESSION['user']){
        	echo 4;   //未登录
        	exit(); 
        }
        
        $m2= M('cart');
        $where = array('goods_id'=>$id, 'user_id'=>$_SESSION['user']['id']);
        $count = $m2->where($where)->count();
        if($count>0){
            echo 1;    //已添加到购物车
            exit();
        }
        
        if($goods['goods_number']<$num){
        	echo 5;
        	exit();
        } 
        
        $data['user_id'] = $_SESSION['user']['id'];
        $data['goods_id'] = $id;
        $data['goods_sn'] = $goods['goods_sn'];
        $data['goods_name'] = $goods['goods_name'];
        $data['goods_number'] = $num;
        $data['market_price'] = $goods['market_price'];
        $data['shop_price'] = $goods['shop_price'];
        $data['time'] = time();

        if($m2->create($data)){
            if($m2->add()){
                echo 2;  //添加成功
            }else{
                echo 3;  //添加失败
            }
        }else{
            echo 3;   //添加失败
        } 
     }
    
     /**
      *   @desc    删除购物车中的商品
      *   @access  public
      *   @return  void
      *   @date    2014-04-27
      */
     public function delCartGoods(){
     	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
     	if($id == 0){
     		echo 0;  //没有接收到id
     		exit();
     	}
     	$m = M('cart');
     	if($m->delete($id)){
     		echo 1;
     	}else{
     		echo 0;
     	}
     }
     
     /**
      *   @desc    查询购物车中的商品
      *   @access  public
      *   @param   int   返回方式     0:JSON(默认)   1:数组
      *   @return  sting  or   array
      *   @date    2014-04-28
      */
     public function getCartGoods($type = 0){
     	$m = M('cart');
     	$field = 'id,goods_id,goods_sn,goods_name,goods_number,shop_price,market_price,time';
     	$order = 'time desc';
     	$where = array('user_id'=>$_SESSION['user']['id']);
     	$goods = $m->field($field)->where($where)->order($order)->select();
		if($type == 1){
			return $goods;
		}else{
			echo json_encode($goods);
		}
     }
     
     /**
      *   @desc    减少库存中商品的数量
      *   @access  public
      *   @param   array   $goods  购物车中的商品
      *   @return  void
      *   @date    2014-04-28
      */
     protected function reduceInventory($goods){
     	$m = M('goods');
     	foreach($goods as $row){
     		$m->where(array('id'=>$row['goods_id']))->setDec('goods_number', $row['goods_number']);
     	}
     }
   
}
    
    
    
    
    
    
    