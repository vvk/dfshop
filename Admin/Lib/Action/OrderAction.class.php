<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     订单管理
 * @author   sunwanqing
 +------------------------------------------------------------------------------
 */

class OrderAction extends CommonAction {
    /**
     * @desc   订单列表
     * @access public
     * @return void
     * @date   2014-04-19
     */
     public function index(){
     	$type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;
        import('ORG.Util.Page');// 导入分页类
        $size = 10;    //每页显示数量
        
        $order_info = M("order_info");
        $field = 'id,order_sn,order_status,shipping_status,confirm_status,consignee,goods_amounts,pay_fee,add_time,is_cancel';
        $o = 'id desc';
        
        if($type == 1){
        	$where2 = 'order_status = 0 and shipping_status=0';
        }elseif($type == 2){
        	$where2 = 'order_status = 1 and shipping_status=0';
        }elseif($type == 3){
        	$where2 = 'shipping_status=0';
        }elseif($type == 4){
        	$where2 = 'order_status = 1 and shipping_status=1';
        }else{
        	$where2 = '';
        }
        
        $count = $order_info->where($where2)->count();
        $page = new Page($count, $size);        //声明分页对象
        $page->setConfig('header','个订单');
		$page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
        
        $order = $order_info->field($field)->where($where2)->order($o)->limit($page->firstRow.','.$page->listRows)->select();

        foreach($order as $key=>$val){
            $order[$key]['time'] = date('Y-m-d H:i:s', $val['add_time']);
            
            if($val['confirm_status'] == 0){
            	if($val['is_cancel'] == 1){
            		$order[$key]['status'] = "<font color='red'>已取消</font>";
            	}elseif($val['order_status'] == 1 && $val['shipping_status'] == 1){
            		$order[$key]['status'] = "<font color='red'>已发货</font>";
            	}else{
            		if($val['order_status'] == 0){
            			$order[$key]['status'] = '未支付，';
            		}else{
            			$order[$key]['status'] = "<font color='red'>已支付，</font>";
            		}
            		if($val['shipping_status'] == 0){
            			$order[$key]['status'] .= "未发货";
            		}else{
            			$order[$key]['status'] .= "<font color='red'>已发货</font>";
            		}
            	}
            }else{
            	$order[$key]['status'] = "<font color='blue'>已确认</font>";
            }
                       
        }
        
        $this->assign('ur_here', '订单列表');
        $this->assign('order', $order);
        $this->assign('page_list', $page_list);
        $this->display();
     }
     
     /**
     * @desc   订单信息
     * @access public
     * @return void
     * @date   2-14-04-19
     */
     public function orderInfo(){
        $id = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : 0;
        
        if($id == 0){
            $this->error('订单id丢失');
            exit();
        }

        //查询订单信息
        $order_info = M('order_info');
        $field = 'id,order_sn,order_status,shipping_status,confirm_status,consignee,phone,province,city,district,';
        $field .= 'address,zipcode,email,shipping_id,pay_id,add_time,confirm_time,';
        $field .= 'pay_time,shipping_time,goods_amounts,shipping_fee,pay_fee,integral,is_cancel';
        $order = $order_info->field($field)->find($id);
  
        if(!$order){
            $this->error('您查看的订单不存在');
            exit();
        }
 
        //订单状态
        if($order['confirm_status'] == 0){
        	if($order['is_cancel'] == 1){
        		$order['status'] = "<font color='red'>已取消</font>";
        	}elseif($order['order_status'] == 1 && $order['shipping_status'] == 1){
        		$order['status'] = "<font color='red'>已发货</font>";
        	}else{
        		if($order['order_status'] == '0'){
        			$order['status'] = '未支付，';
        		}else{
        			$order['status'] = "<font color='red'>已支付，</font>";
        		}
        		if($order['shipping_status'] == 0){
        			$order['status'] .= "未发货";
        		}else{
        			$order['status'] .= "<font color='red'>已发货</font>";
        		}
        	}
        }else{
        	$order['status'] .= "<font color='blue'>已确认</font>";
        }
        
        
        $order['addTime'] = date('Y-m-d H:i:s', $order['add_time']);
        $order['confirmTime'] = date('Y-m-d H:i:s', $order['confirm_time']);
        $order['payTime'] = date('Y-m-d H:i:s', $order['pay_time']);
        $order['shippingTime'] = date('Y-m-d H:i:s',$order['shipping_time']);
       
        //配送方式
        $shipping = M('shipping');
        $ship = $shipping->field('shipping_name')->find($order['shipping_id']);
        $order['shipping_name'] = $ship['shipping_name'];

        //支付方式
        $pay = M('payment');
        $payment = $pay->field('pay_name')->find($order['pay_id']);
        $order['pay_name'] = $payment['pay_name'];
        
        //配送地区
        $order['province_region'] = $this->getRegionById($order['province']);
        $order['city_region'] = $this->getRegionById($order['city']);
        $order['district_region'] = $this->getRegionById($order['district']);

        //查询订单中的商品
        $order_goods = M('order_goods');
        $field = 'id,goods_id,goods_sn,goods_name,goods_number,market_price,shop_price';
        $where = array('order_id'=>$order['id']);
        $goods = $order_goods->field($field)->where($where)->select();
        foreach($goods as $key=>$val){
            $goods[$key]['money'] = number_format($val['shop_price']*$val['goods_number'], 2);
        }
        $order['goods'] = $goods;
        
        $action_link = array('href'=>"__APP__/Order", 'text'=>'订单列表');        
        $this->assign('order', $order);
        $this->assign('goods', $goods);
        $this->assign('action_link', $action_link);
        $this->assign('ur_here', '订单信息');        
        $this->display();
     }
     
     /**
     * @desc   订单发货处理
     * @access public
     * @return void
     * @date   2-14-04-19
     */
     public function shipments(){
         $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
         if($id == 0){
            echo 0;
            exit();
         }
         
         $m = M('order_info');
         $field = 'order_status,is_cancel';
         $order = $m->field($field)->find($id);
         
         if(empty($order)){
            echo 1;   //订单不存在
            exit();
         }
         
         if($order['is_cancel'] == 1){
            echo 5;    //订单已取消
            exit();
         }elseif($order['order_status'] == 0){
            echo 2;   //未付款
            exit();
         }
         
         $data['id'] = $id;
         $data['shipping_status'] = 1;
         $data['shipping_time'] = time();
         if($m->create($data)){
            if($m->save()){
                echo date('Y-m-d H:i:s', $data['shipping_time']);
            }else{
                echo 3;
            }
         }else{
            echo 3;
         }
     } 
}