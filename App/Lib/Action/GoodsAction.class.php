<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      商品
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class GoodsAction extends commonAction{
	
	/**
	 *   @desc    商品详情页
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-24
	 */
	public function index(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        
        if($id == 0){
        	$this->referer('产品id丢失');
        	exit();   
        }

        $m = M('goods');
        $field = 'id,goods_name,goods_sn,click_count,sale_number,goods_number,market_price,shop_price,';
        $field .= 'promote_price,keywords,goods_brief,goods_desc,goods_thumb,goods_bigThumb,add_time,';
        $field .= 'integral,click_count';
        $where = array('id'=>$id, 'is_delete'=>0, 'is_on_sale'=>1);
        $goods = $m->field($field)->where($where)->find();
      
        $crumbs = $this->getCrumbs($goods['id']); //面包屑导航  
        
        //商品不存在
        if(!$goods){
        	$this->referer('该商品不存在，可能已经删除!');
        	exit();
        }
        
        $goods['time'] = date($this->time_format, $goods['add_time']);        
        $goods['keywords'] = $goods['keywords'] ? $goods['keywords'] : $this->_CFG['shop_keywords']['value'];
        $goods['description'] = $goods['goods_brief'] ? $goods['goods_brief'] : $this->_CFG['shop_desc']['value'];
		
        //历史记录、点击量
        if(!in_array($id, $_SESSION['history'])){
        	$_SESSION['history'][] = $id;
        	$m->where(array('id'=>$id))->setInc("click_count",1);  //点击量加1
        }
        
        $m2 = M('comment');
        $field = 'id,email,content,comment_rank,comment_time,ip_address,user_id';
        $order = 'comment_time desc,id desc';
        $limit = $this->_CFG['comments_number']['value'];     //加分页
        $where = array('comment_type'=>0,'id_value'=>$id,'status'=>1);
        $goods['commentCount'] = $m2->field($field)->where($where)->count();      //评论总数     
      
        $allComment = $m2->field($field)->where($where)->order($order)->select();  //所有评论
        $rank = 0;
 		foreach($allComment as $key=>$val){
 			$rank += $val['comment_rank'];
 		}
 		
 		//用户评论等级
 		$goods['comment_rank'] = round($rank/$goods['commentCount']) ? round($rank/$goods['commentCount']) : 5;  
        
 		//当前页面要显示的评论
        $comment = $m2->field($field)->where($where)->order($order)->limit($limit)->select(); 
 		
 		$m3 = M('users');
 		foreach($comment as $key=>$val){
 			$comment[$key]['time'] = date($this->time_format, $val['comment_time']);
 			
 			//查询评论用户
 			if($val['user_id'] == 0){
 				$comment[$key]['user'] = '匿名用户';
 			}else{
 				$user = $m3->field('user_name')->find($val['user_id']);
 				$comment[$key]['user'] = $user['user_name'];
 			}	
 		}

        $this->assign('goods', $goods);
        $this->assign('crumbs', $crumbs);
		$this->assign('comment', $comment);
        $this->display();
	}
	
	/**
	*   @desc    商品评价
	*   @access  public
	*   @return  void
	*   @date    2014-04-26
	*/
	public function goodsComment(){
		$email = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
		$commont = isset($_REQUEST['commont']) ? trim($_REQUEST['commont']) : '';
		$rank = isset($_REQUEST['rank']) ? intval($_REQUEST['rank']) : 5;
		$verify = isset($_REQUEST['verify']) ? trim($_REQUEST['verify']) : '';
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		$result = array();
		if($id == 0){
			echo 1;   //没有接收到要评论商品的id
			exit();
		}		
		
		//验证码不正确则直接返回
		if($_SESSION['verify'] != md5($verify)){
			echo 2;  //验证码错误
			exit();
		}
		
		if(!$email){
			echo 3;   //邮箱为空
			exit();
		}elseif(!isEmail($email)){
			echo 4;    //邮箱格式不正确
			exit();
		}
		if(!$commont){
			echo 5;    //评论内容不能为空
			exit();
		}

		$data['comment_type'] = 0;   //  0：商品，1：文章
		$data['id_value'] = $id; 
		$data['email'] = $email;
		$data['content'] = $commont;
		$data['comment_rank'] = $rank;
		$data['comment_time'] = time();
		$data['ip_address'] = get_client_ip();
		$data['user_id'] = $_SESSION['user'] ? $_SESSION['user']['id'] : 0;
        
        //评论是否需要审核，如需要审核，刚设置不显示状态
        if($this->_CFG['comment_check']['value'] == 1){
            $data['status'] = 0;
        }else{
            $data['status'] = 1;
        }
		

		$m = M('comment');
		if($m->create($data)){
			if($m->add()){
				echo 0;
			}else{
				echo 6;
			}
		}else{
			echo 6;
		}
	}
	
	/**
	 *   @desc    收藏商品
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-26
	 */
	public function goodsCollect(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			echo 0;
			exit();   //没有接收到收藏商品的id
		}
		if(!$_SESSION['user']){
			echo 1;  //未登录
			exit();
		}
		$m = M('collect_goods');
		$collect = $m->where(array('goods_id'=>$id,'user_id'=>$_SESSION['user']['id']))->find();
		if(!empty($collect)){
			echo 2;    //已收藏
			exit();
		}		
		
		$data['user_id'] = $_SESSION['user']['id'];
		$data['goods_id'] = $id;
		$data['add_time'] = time();
		if($m->create($data)){
			if($m->add()){
				echo 4;
			}else{
				echo 3;
			}
		}else{
			echo 3;
		}
		
	}
}