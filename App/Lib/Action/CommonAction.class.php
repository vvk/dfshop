<?php
header("Content-type:text/html;charset=utf-8");
defined('DF_ROOT') or die('Hacking attempt');

/**
 +------------------------------------------------------------------------------
 * @desc      公共控制器
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class commonAction extends Action{
	protected $_CFG = array();    //系统配置
	public $host = '';            //当前域名
	protected $province = '';     //所有省
    protected $time_format = '';  //时间显示格式
    protected $qc;                //qq登录
    protected $sina;             //新浪微博获取授权信息对象
    protected $weibo;             //新浪微博对象
	
	public function _initialize(){      
		$m = new Model('config');
		$field = "id,parent_id,code,name,type,store_range,store_dir,value";
		$configs = $m->field($field)->select();
		$config = array();
		foreach($configs as $key=>$val){
			$config[$val['code']]['id'] = $val['id'];
			$config[$val['code']]['name'] = $val['name'];
			$config[$val['code']]['code'] = $val['code'];
			$config[$val['code']]['value'] = $val['value'];
		}
		$this->_CFG= $config;     //配置信息
		$this->host =  'http://'.$_SERVER['SERVER_NAME'].'/';
        $this->assign('host', $this->host);
        $this->time_format = $this->_CFG['time_format']['value'];
        
        //判断网店是否关闭
        if($this->_CFG['shop_closed']['value'] == 1){
            echo "<div style='margin: 150px; text-align: center; font-size: 14px'>".$this->_CFG['close_comment']['value']."</p><p></p></div>";
            exit();
        }  
        
        session_start();

        //引入新浪微博登录接口
        import('@.ORG.sinaWeibo');
        $sina_app_key = C('sina_app_key');
        $sina_app_secret = C('sina_app_secret');
        $this->sina = new Oauth($sina_app_key, $sina_app_secret);

        //引入qq登录接口
        import('@.ORG.qqLogin');
        
        $appId = C('qq_app_id');        //qq登录appid
        $appKey = C('qq_app_key');      //qq登录appkey
        $qqCallback = C('qq_callback'); //qq登录回调地址

        $this->qc = QC::instance($appId, $appKey, $qqCallback);
    
        if($_REQUEST['state']){
            $access_token = $this->qc->qqCallback();
            if(!$access_token){
                $error = $this->qc->getErrorMsg();
                $error = $error ? $error : '登录失败';
                $this->error($error, __APP__.'/login.html');
            }
            
            $openid = $this->qc->getOpenId();
            if(!$openid){
                $error = $this->qc->getErrorMsg();
                $error = $error ? $error : '登录失败';
                $this->error($error, __APP__.'/login.html');
            }
            $user = $this->qc->getUserInfo();
            $this->qqLogin($openid, $user);
        }
        
        //支付宝
        import('@.ORG.alipay.alipay_submit');
     
        //查询地区
        $region = M("region");
        $field = "id,parent_id,region_name";
        $where = array("parent_id"=>1);
        $this->province = $region->field($field)->where($where)->select();    

        $search_keywords = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : $this->_CFG['search_keywords']['value'];
        
        $this->assign('logo', $this->_CFG['shop_logo']['value']);  //logo
        $this->assign('flink', $this->getFlink());                 //友情链接
        $this->assign('nav', $this->getNav());                     //导航
        $this->assign('category', $this->getCategory());           //分类树
        $this->assign('shop_name', $this->_CFG['shop_name']['value']); //商城名称
        $this->assign('rankGoods', $this->getHotGoods($this->_CFG['top_number']['value']));    //销量排名
        $this->assign('historyGoods', $this->history());           //历史记录
        $this->assign('search_keywords', $search_keywords);        //搜索关键字
        $this->assign('hotSearch', $this->getSearchGoods());       //把门搜索商品
        $this->assign('bottomLink', $this->getBottomLink());       //底部文章链接
        $this->assign('defaultGoodsImg', $this->_CFG['no_picture']['value']); //商品默认图片
        $this->assign('serviceQQ', $this->_CFG['qq']['value']);               //在线客服QQ
        $this->assign('stats_code', C('stats_code'));                         //站长统计
        $this->assign('serviceEmail', $this->_CFG['service_email']['value']); //服务邮箱
	}
    
    /**
	 * @desc      导航树
	 * @access    protected
	 * @return    array
	 * @date      2014-04-21  2014-04-23
	 */
     protected function getCategory(){
        $m1 = M('category');
        $sql = "select c1.`id`,c1.`cat_name`,c1.`keywords`,c1.`cat_desc`,c1.`parent_id` from df_category as c1";
        $sql .= " left join df_category as c2 on c1.`parent_id`=c2.`id` where c1.`is_show`=1 ";
        $sql .= "and c2.`parent_id`=0 order by c1.`sort_order` asc,c1.`id` desc";
        $res = $m1->query($sql);
        
        $category = array();
        $k = $i = 0;
        $m2 = M('goods');
        $field = 'id,goods_name';
        $order = 'sort_order asc,id desc';
        $limit = 5;
        foreach($res as $key=>$val){
            if($i > 1){
                $i = 0;
                $k++;
            }
            $category[$k]['cat'][] = $val;
            $where = array('cat_id'=>$val['id']);
            $goods = $m2->field($field)->where($where)->order($order)->limit($limit)->select();
            if(!$category[$k]['goods']){
                $category[$k]['goods'] = array();
            }
            $category[$k]['goods'] = array_merge($category[$k]['goods'], $goods);
            $i++;
        }
        return $category;
     }

    /**
	 * @desc      导航
	 * @access    protected
	 * @return    array
	 * @date      2014-04-21
	 */
     protected function getNav(){
        $m = M('nav');
        $field = 'id,name,title,keyword,desc,opennew,url';
        $order = 'sort_order asc,id asc';
        $where = array('is_show'=>1); 
        $nav = $m->field($field)->where($where)->order($order)->select();
        return $nav;        
     }
     
     /**
      * @desc      新品上架
      * @access    protected
      * @param     int   $limit   新品数量 
      * @return    array
      * @date      2014-04-23
      */
     protected function getNewGoods($limit = 4){
     	$m = M('goods');
     	$field = 'id,goods_name,shop_price,market_price,goods_thumb';
     	$order = 'sort_order asc,id desc';
     	$where = array('is_new'=>1,'is_delete'=>0,'is_on_sale'=>1);
     	$goods = $m->field($field)->where($where)->order($order)->select();
     	return $goods;
     }
     
     /**
      * @desc      推荐商品
      * @access    protected
      * @param     int   $limit   推荐商品数量
      * @return    array
      * @date      2014-04-23
      */
     protected function getRecommendedGoods($limit = 4){
     	$m = M('goods');
     	$field = 'id,goods_name,shop_price,market_price,goods_thumb';
     	$order = 'sort_order asc,id desc';
     	$where = array('is_recommend'=>1,'is_delete'=>0,'is_on_sale'=>1);
     	$goods = $m->field($field)->where($where)->order($order)->select();
     	return $goods;
     }
     
     /**
      * @desc      热销商品
      * @access    protected
      * @param     int   $limit   热销商品数量
      * @return    array
      * @date      2014-04-23
      */
     protected function getHotGoods($limit = 4){
     	$m = M('goods');
     	$field = 'id,goods_name,shop_price,market_price,goods_thumb,sale_number';
     	$order = 'sale_number desc,sort_order asc,id desc';
     	$where = array('is_delete'=>0,'is_on_sale'=>1);
     	$goods = $m->field($field)->where($where)->order($order)->limit($limit)->select();
     	return $goods;
     }
    
	/**
	 * @desc      空操作
	 * @access    public
	 * @return    void
	 * @date      2014-02-02
	 */
	public function _empty(){
		header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码 
        $this->display("Public:404"); 
	}	
	
	/**
	 * @desc     验证码
	 * @access   public
	 * @return   void
	 * @date     2014-02-02   
	 */
	function verify(){
		$length = $_REQUEST['length'] ? $_REQUEST['length'] : 4;  //默认为四位
		$model = $_REQUEST['model'] ?  $_REQUEST['model'] : 1; //默认为数字        0 字母 1 数字 2 大写字母 3 小写字母 4中文 5混合
		$width = $_REQUEST['width'] ? $_REQUEST['width'] : 60 ;
		$height = $_REQUEST['height'] ? $_REQUEST['height'] : 20 ;		
		
		import('ORG.Util.Image');
		Image::buildImageVerify($length, $model,'jpeg', $width, $height);
	}
    
    /**
	 * @desc     判断是否登录
	 * @access   public
	 * @return   bool
	 * @date     2014-03-30   
	 */     
     public function isLogin(){
        if($_SESSION['user']){
            return true;
        }else{
            return false;
        }
     }
     
     /**
	 * @desc     地区改变
	 * @access   public
	 * @return   bool
	 * @date     2014-04-06  
	 */  
     function regionChange(){
        $val = isset($_REQUEST['val']) ? intval($_REQUEST['val']) :4 ;
        $leval  = isset($_REQUEST['leval']) ? intval($_REQUEST['leval']) : 0;
        
        if($leval == 0){
            echo 'error';
            exit();
        }
        
        $region = M('region');
        $field = "id,region_name";
        $where = array("parent_id"=>$val);
        $result = $region->field($field)->where($where)->select();
        
        $zone = $this->regionOption($result);
         
        if($leval == 1){
            $where = array("parent_id"=>$result[0]['id']);
            $result = $region->field($field)->where($where)->select();
            $zone = $zone.','.$this->regionOption($result);
        }
        echo $zone;
     }
     
     /**
	 * @desc     地区拼接成字符串
	 * @access   protected
     * @param    array    $region  地区数组   
     *           int      $id      当前地区id          
	 * @return   string
	 * @date     2014-04-06  
	 */      
     protected function regionOption($region, $id = 0){
        $option = '';
        foreach($region as $row){
            if($id == $row['id']){
                $option .= "<option value='".$row['id']."' selected='selected'>".$row['region_name']."</option>";   
            }else{
                $option .= "<option value='".$row['id']."'>".$row['region_name']."</option>";
            }   
        }        
        return $option; 
     }
     
     /**
	 * @desc     根据父id得到下一级地区
	 * @access   protected
     * @param    int    $id  父id    
	 * @return   array
	 * @date     2014-04-15  
     *
     *    此处应传入两个参数  1、父级地区id  2、当前地区id
     *
	 */ 
     protected function getSubRegion($id = 1){
         $m = M("region");
         return $m->where(array("parent_id"=>$id))->select();
     } 
     
     /**
      * @desc     得到指定id地区
      * @access   protected
      * @param    int   $id   要查询地区的id
      * @return   array
      * @date     2014-04-20
      */
     protected  function getRegion($id = 0){
     	$m = M("region");
     	return $m->field('id,parent_id,region_name')->where(array("id"=>$id))->find();
     }
     
     /**
	 * @desc     友情链接
	 * @access   protected         
	 * @return   array
	 * @date     2014-04-20 
	 */  
     protected function getFlink(){
        $m= M('friend_link');
        $field = 'link_name,link_url,link_logo';
        $order = 'sort_order asc,id desc';
        $where = array('is_show'=>1);
        $flink = $m->field($field)->where($where)->order($order)->select();
        return $flink;        
     }
     
     /**
	 * @desc     查询热门搜索商品
	 * @access   protected         
	 * @return   array
	 * @date     2014-04-27 
	 */  
     protected function getSearchGoods($limit = 10){
     	$m = M('search');
     	$order = 'count desc';
     	$field = 'keyword';
        $where = array('is_show'=>1);
     	$res = $m->field($field)->where($where)->order($order)->limit($limit)->select();
     	return $res;
     }
     
     /**
      * @desc     跳转到前一页面
      * @access   protected
      * @return   void
      * @date     2014-04-24
      */
     protected function referer($msg = 'error'){
     	$referer = $_SERVER['HTTP_REFERER'];
     	if(!$referer){
     		$referer = './';
     	}
     	$this->error($msg, $referer);
     }
     
     /**
      * @desc     历史记录
      * @access   protected
      * @return   array
      * @date     2014-04-26
      */
     protected  function history(){
     	$history_number = $this->_CFG['history_number']['value'];  //浏览历史显示数量
     	if($history_number == 0){
     		return array();
     	}
     	$ids = array_reverse($_SESSION['history']);   //倒序取出历史记录
     	if(empty($ids)){
     		return array();   //没有历史记录
     	}
     	
     	$in = '';
     	for($i = 0; $i<$history_number; $i++){
     		if(!$ids[$i]){
     			//break;
     		}
     		$in .=$ids[$i].',';
     	}
     	$in = substr($in, 0, -1);
     	
     	$m = M('goods');
     	$field = 'id,goods_name,goods_thumb,shop_price';
     	$where['id'] = array('in', $in);
     	$goods = $m->field($field)->where($where)->select();
		return $goods;
     }
     
     /**
      * @desc     清空历史记录
      * @access   protected
      * @return   void
      * @date     2014-04-26
      */
     public function clearHistory(){
     	unset($_SESSION['history']);    	
     }
     
     /**
      * @desc     查询收货人信息
      * @access   protected
      * @return   array
      * @date     2014-05-02
      */
     protected  function getUserAddress(){
     	$m = M('users_address');
     	$field = 'consignee,email,province,city,district,address,zipcode,phone';
     	$address = $m->field($field)->where(array('user_id'=>$_SESSION['user']['id']))->find();
     	if(!$address){
     		$this->error('请填写收货地址', __APP__.'./address.html');
     		exit();
     	}elseif(!$address['consignee'] || !$address['email'] || !$address['province'] || !$address['city'] || !$address['district'] || !$address['address'] || !$address['zipcode'] || !$address['phone']){
     		$this->error('请将地址信息填写完整', __APP__.'./address.html');
     		exit();
     	}
     	return $address;
     }
     
     /**
      * @desc     支持方式
      * @access   protected
      * @param    int   支付方式id，默认为0，表示查询所有支付方式 
      * @return   array
      * @date     2014-05-02
      */
     protected function getPayment($id = 0){
     	$m = M('payment');
     	$field = 'id,pay_fee,pay_name,pay_code,pay_desc,pay_config';
     	$order = 'sort_order asc,id asc';
     	$where = array('enabled'=>1);
     	if($id != 0){
     		$where = array_merge($where, array('id'=>$id));
     	}
     	$payment = $m->field($field)->where($where)->order($order)->select();
     	return $payment;
     }
     
     /**
      * @desc     底部文章链接
      * @access   protected
      * @return   array
      * @date     2014-05-05
      */
     protected function getBottomLink(){
     	$m1 = M('article_category');
     	$field = 'id,cat_name';
     	$order = 'sort_order asc,id asc';
     	$where = array('is_show'=>1);
     	$res = $m1->field($field)->where($where)->order($order)->select();

     	$m2 = M('article');
     	$field = 'id,title';
     	$order = 'sort_order asc,add_time desc';
     	foreach($res as $key=>$val){
     		$article = $m2->field($field)->where(array('cat_id'=>$val['id'],'is_show'=>1))->order($order)->select();
     		$res[$key]['article'] = $article;
     	}	
  		return $res;
     }
     
     /**
      * @desc     配送方式
      * @access   protected
      * @param    int   配送方式id，默认为0，表示查询所有配送方式
      * @return   array
      * @date     2014-05-02
      */
     protected function getShipping($id = 0){
     	$m = M('shipping');
     	$field = 'id,shipping_name,shipping_desc,shipping_fee';
     	$order = 'sort_order asc,id asc';
     	$where = array('enabled'=>1);
     	if($id != 0){
     		$where = array_merge($where, array('id'=>$id));
     	}
     	$shipping = $m->field($field)->where($where)->order($order)->select();
     	return $shipping;
     }
     
     /**
      * @desc     获得某件商品的信息
      * @access   protected
      * @param    int   商品id
      * @return   array
      * @date     2014-05-03
      */
     protected function getGoodsById($id = 0){
     	$m = M('goods');
     	$field = 'id,cat_id,goods_name,goods_sn,click_count,sale_number,goods_number,market_price,shop_price,';
     	$field .= 'promote_price,warn_number,is_promote,promote_start_time,promote_end_time,is_recommend,';
     	$field .= 'is_new,keywords,goods_brief,goods_desc,goods_thumb,goods_bigThumb,goods_img,is_on_sale,';
     	$field .= 'is_delete,sort_order,add_time,update_time,integral';
     	$goods = $m->field($field)->find($id);
     	return $goods;
     }
     
     /**
      * @desc     根据id查询用户信息
      * @access   protected
      * @param    int   用户id
      * @return   array
      * @date     2014-05-03
      */
     protected function getUserById($id = 0){
     	$m = M('users');
     	$field = 'id,user_name,password,email,sex,birthday,question_id,answer,reg_time,reg_ip,last_login_time,';
     	$field .= 'last_login_ip,visit_count,salt,qq,phone,photo,integral,is_approve,province,city,district,enabled';
     	$user = $m->field($field)->find($id);
     	return $user;
     }
     
     /**
      * @desc     面包屑导航
      * @access   protected
      * @param    $id   int   商品或分类id
      *           $type int   面包屑导航类型   0:商品   1：分类
      * @return   string
      * @date     2014-05-03
      */
     protected function getCrumbs($id, $type = 0){
     	  $crumbs = '';
     	  if($type == 0){
     	  	    $m1 = M('goods');
     	        $field = 'id,cat_id,goods_name';
     	  	    $goods = $m1->field($field)->find($id);
     	  	    $crumbs .= "<span>{$goods['goods_name']}</span>";
     	  	    $id = $goods['cat_id'];
     	  }
     	  
     	  $m2 = M('category');
     	  $field = 'id,cat_name,parent_id';
    	  $cat = $m2->field($field)->find($id); 
    	  if($type == 0){
    	  	$crumbs = "<a href='/category-".$cat['id'].".html'>{$cat['cat_name']}</a>>".$crumbs;
    	  }else{
    	  	$crumbs = "<span>{$cat['cat_name']}</span>".$crumbs;
    	  }
    	  
		  while($cat['parent_id'] != 0){
		  	$cat = $m2->field($field)->where(array('id'=>$cat['parent_id']))->find();
		  	$crumbs = "<a href='/category-".$cat['id'].".html'>{$cat['cat_name']}</a>>".$crumbs;
		  }
     	  return $crumbs;
     }
     
     /**
      * @desc     获取指定id的顶级分类id
      * @access   public
      * @date     2014-05-08
      */
     public function getCategoryById($id = 0){
     	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : $id;
     	$m = M('category');
     	$field = 'id,parent_id';
     	$res = $m->field($field)->find($id);
     	while($res['parent_id'] != 0){
     		$res = $m->field($field)->find($res['parent_id']);
     	}
     	echo $res['id'];     	
     }

     /**
     *  @desc   qq登录
     *  @param  $openid   qq登录openid
     *  @param  $userInfo qq登录时获取的用户信息
     *  @date   2014-08--2
     */ 
    public function qqLogin($openid, $userInfo){
        $m = M('users');
        $user = $this->isQqUser($openid);
        if(!empty($user)){//用户存在 
            //判断用户是否被禁用
            if($user['enabled'] == 1){
              $this->error('您的帐号已被禁用', './login.html');
              exit();
            }
            
            $data['user_name'] = $userInfo['nickname'];
            if($userInfo['gender'] == '男'){
                $data['sex'] = 1;  
            }elseif($userInfo['gender'] == '女'){
                $data['sex'] = 2;  
            }else{
                $data['sex'] = 0;  
            }
            $data['last_login_time'] = time();
            $data['last_login_ip'] = get_client_ip();
            
            $where = array('open_id'=>$openid);
            $m->where($where)->setInc('visit_count', 1);  //登录次数加1
            $m->where($where)->save($data);
            $user['openid'] = $openid;
            $_SESSION['user'] = $user;
            $_SESSION['user']['visit_count']++;            
            $this->success('登录成功', '/profile.html');
        }else{//用户不存在
            $data['user_name'] = $userInfo['nickname'];
            $data['email'] = $openid.'.@qq.com';
            $data['salt'] = '';
            $data['password'] = '';
            if($userInfo['gender'] == '男'){
                $data['sex'] = 1;  
            }elseif($userInfo['gender'] == '女'){
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
            $data['open_id'] = $openid; 
            
            if($m->create($data)){
                if($m->add()){
                    $where = array('open_id'=>$data['open_id']);
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
     *  @desc    检查是是否为qq用户
     *  @param   $openid     
     *  @date    2014-08-02
     */ 
    public function isQqUser($openid){
        $m = M('users');
        $where = array('open_id'=>$openid);
        $res  =$m->where($where)->find();
        if(!empty($res)){
            return $res;
        }else{
            return array();
        }
    }
     
    /**
     *  @desc   支付宝支付
     *  @param  string    $order_sn   订单号
     *  @date   2014-08-06
     */ 
    public function alipay($data){
        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = $this->host.'index.phg/Order/alipay_notify';
        //需http://格式的完整路径，不能加?id=123这类自定义参数

        //页面跳转同步通知页面路径
        $return_url = $this->host.'index.php/Order/alipay_return';
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

        //卖家支付宝帐户
        $seller_email = C('alipay_email');
        //必填

        //商户订单号
        $out_trade_no = $data['order_sn'];
        //商户网站订单系统中唯一订单号，必填

        //订单名称
        $subject = $data['order_sn'];
        //必填

        //付款金额
        $price = $data['pay_fee'];
        //必填

        //商品数量
        $quantity = '1';
        //必填，建议默认为1，不改变值，把一次交易看成是一次下订单而非购买一件商品
        //物流费用
        $logistics_fee = $data['shipping_fee'];
        //必填，即运费
        //物流类型
        $logistics_type = "EXPRESS";
        //必填，三个值可选：EXPRESS（快递）、POST（平邮）、EMS（EMS）
        //物流支付方式
        $logistics_payment = "SELLER_PAY";
        //必填，两个值可选：SELLER_PAY（卖家承担运费）、BUYER_PAY（买家承担运费）
        //订单描述

        $body = '';
        //商品展示地址
        $show_url = '';
        //需以http://开头的完整路径，如：http://www.xxx.com/myorder.html

        //收货人姓名
        $receive_name = $data['consignee'];
        //如：张三

        //收货人地址
        $receive_address = $data['address'];
        //如：XX省XXX市XXX区XXX路XXX小区XXX栋XXX单元XXX号

        //收货人邮编
        $receive_zip = $data['zipcode'];
        //如：123456

        //收货人电话号码
        //$receive_phone = '';
        //如：0571-88158090

        //收货人手机号码
        $receive_mobile = $data['phone'];
        //如：13312341234
        
        //构造要请求的参数数组，无需改动
        $parameter = array(
        		"service" => "create_partner_trade_by_buyer",
        		"partner" => trim(C('alipay_partner')),  //合作身份者ID
        		"payment_type"	=> $payment_type,              //收款类型，不可空
        		"notify_url"	=> $notify_url,
        		"return_url"	=> $return_url,
        		"seller_email"	=> $seller_email,   //卖家支付宝账号，不可空
        		"out_trade_no"	=> $out_trade_no,  //商户网站唯一订单号，不可空
        		"subject"	=> $subject,           //商品名称，不可空
        		"price"	=> $price,
        		"quantity"	=> $quantity,
        		"logistics_fee"	=> $logistics_fee,        //物流费用，不可空
        		"logistics_type"	=> $logistics_type,   //物流类型，不可空
        		"logistics_payment"	=> $logistics_payment,   //物流支付类型，不可空
        		"body"	=> $body,
        		"show_url"	=> $show_url,
        		"receive_name"	=> $receive_name,
        		"receive_address"	=> $receive_address,
        		"receive_zip"	=> $receive_zip,
        		//"receive_phone"	=> $receive_phone,
        		"receive_mobile"	=> $receive_mobile,
        		"_input_charset"	=> trim(strtolower(C('apliay_input_charset'))) //参数编码字符集，不可空
        );
        
        $alipay_config['partner'] = C('alipay_partner');
        $alipay_config['key'] = C('alipay_key');
        $alipay_config['sign_type'] = C('alipay_sign_type');
        $alipay_config['input_charset'] = C('apliay_input_charset');
        $alipay_config['cacert'] = C('apliay_cacert');
        $alipay_config['transport'] = C('apilay_transport');
        
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        return $html_text;
    }

 
}