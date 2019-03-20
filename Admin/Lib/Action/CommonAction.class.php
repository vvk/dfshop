<?php
defined('DF_ROOT') or die('Hacking attempt');

/**
 +------------------------------------------------------------------------------
 + @desc    后台公共控制器
 + @author  sunwanqing
 +------------------------------------------------------------------------------
 */
class CommonAction extends  Action{
	protected  $_CFG = array();  //全局配置
	protected  $imgAllowExts = array('jpg', 'gif', 'png', 'jpeg');  //上传图片允许类型
	protected  $cat = array();    //分类一维数组
	protected  $host = ''; //当前域名
 	
	public function _initialize(){
	    // if(!$_SESSION['admin']){
	    //    header('Location:'.__APP__.'/Privilege/login');
     //       exit();
	    // }       
       
		$m = new Model('config');
		
		$field = "id,parent_id,code,name,type,store_range,store_dir,value";
		$configs = $m->field($field)->select();
		
		$config = array();
		foreach($configs as $key=>$val){
			$config[$val['code']]['id'] = $val['id'];
			$config[$val['code']]['name'] = $val['name'];
			$config[$val['code']]['code'] = $val['code'];
			$config[$val['code']]['value'] = $val['value'];
			//$config[$val['code']]['store_range'] = $val['store_range'];			
		}
		$this->_CFG= $config;     //配置信息
		$this->host =  'http://'.$_SERVER['SERVER_NAME'].'/';
        $this->assign('host', $this->host);
		
		$category_tree = $this->get_categories_tree(0,0);
		$this->get_category($category_tree);   //将分类树放在一级数组中
	}
	
	/**
	 * @desc    获得指定分类同级的所有分类以及该分类下的子分类
	 * @access  protected
	 * @param   integer     $cat_id     分类编号
	 * @param   integer     $level      等级
	 * @return  array
	 * @date    2014-02-11
	 */
	
	
	/**
	 */
	protected function get_categories_tree($cat_id = 0, $level = 0){
		$level++;
		$m = new model('category');
	
		$cat_arr = array();
		$count = $m->where(array('parent_id'=>$cat_id))->count();
		if($count == 0){
			return $cat_arr;
			exit();
		}else{
			$field = 'id,cat_name,parent_id,sort_order,is_show';
			$where = array('parent_id'=>$cat_id);
			$res = $m->where($where)->field($field)->select();
			foreach($res as $row){
				$cat_arr[$row['id']]['id'] = $row['id'];
				$cat_arr[$row['id']]['cat_name'] = $row['cat_name'];
				$cat_arr[$row['id']]['parent_id'] = $row['parent_id'];
				$cat_arr[$row['id']]['sort_order'] = $row['sort_order'];
				$cat_arr[$row['id']]['is_show'] = $row['is_show'];
				$cat_arr[$row['id']]['level'] = $level;
	
				$where = array('cat_id'=>$row['id'],'is_delete'=>0,'is_on_sale'=>1);
	
				$m2= new Model('goods');
				$goods_number = $m2->where($where)->group('cat_id')->count();
				$cat_arr[$row['id']]['goods_number'] = $goods_number;
	
				$count = $m->where(array('parent_id'=>$row['id']))->count();
				if($count>0){
					$cat_arr[$row['id']]['children'] = $this->get_categories_tree($row['id'], $level);
				}
			}
			return $cat_arr;
		}	
	}
	
	/**
	*   @desc     将分类树放在一数组中
	*   @access   protected
	*   @param    array    分类树
	*   @return   void
	*   @date     2014-02-13
	*/
	protected function get_category($tree = array()){
		foreach($tree as $row){
			$temp = $row;
			if($temp['children']){
				unset($temp['children']);
			}
			$this->cat[] = $temp;
			if($row['children']){
				$this->get_category($row['children']);
			}
		}
	
	}
	
	/**
	 *   @desc   将商品分类组装成option数组
	 *   @access protected  
	 *   @param  int         $id   当前分类id   
	 *   @return void
	 *   @date   2014-02-16
	 */
	protected function option($id = 0){
		//$category_tree = $this->get_categories_tree(0,0);
		//$this->get_category($category_tree);   //将分类树放在一级数组中
		$option = '';
		foreach($this->cat as $row){
			$space = '';
			for($i=0; $i<$row['level']-1; $i++){
				$space .= "&nbsp;&nbsp;&nbsp";
			}
			if($row['id'] == $id){
				$option .= "<option value='".$row['id']."' selected>".$space.$row['cat_name']."</option>";
			}else{
				$option .= "<option value='".$row['id']."'>".$space.$row['cat_name']."</option>";
			}
		}
		return $option;
	}
	
	/** 
	 * @desc    对商品或文章的评论
	 * @access  protected
	 * @param   integer     $type  评论类型  0:商品评论，1:文章评论
	 * @return  array
	 * @date    2014-02-20
	 */
	protected function comment($type = 0){
		import('ORG.Util.Page');// 导入分页类
		$size = 10;
		$m = new Model('comment');

		$field = "id,comment_type,id_value,email,content,comment_rank,comment_time,ip_address,user_id,status";
		$where = array('comment_type'=>$type);
		$order = "comment_time desc,id desc";
		$count = $m->where($where)->count();   //评论总数
		
		$page = new Page($count, $size);        //声明分页对象
		$page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		
		$page_list = $page->show();
		
		$comment_list = $m->where($where)->field($field)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
		
		$m2 = new Model('goods');
		$m3 = new model('article');
		$m4 = new Model('users');
		foreach($comment_list as $key=>$val){
			if($val['user_id'] == 0){
				$comment_list[$key]['username'] = '匿名用户';
			}else{ //查询评论用户
				$user = $m4->find($val['user_id']);
				$comment_list[$key]['username'] = $user['user_name'];
			}

			$comment_list[$key]['comment_time'] = date("Y-m-d H:i:s", $val['comment_time']);
			
			if($val['status'] == 0){ //评论不显示
				$comment_list[$key]['status'] = '不显示';
			}else{  //评论显示
				$comment_list[$key]['status'] = '显示';
			}

			if($type == 0){ //评论主商品，查询商品名称				
				$goods = $m2->find($val['id_value']);
				$comment_list[$key]['name'] = $goods['goods_name'];	
			}else{ //评论为文章，查询文章名称
				$article = $m3->find($val['id_value']);
				$comment_list[$key]['name'] = $article['title'];
			}			
		}

		if($type == 0){
			$title = '商品评论';
		}else{
			$title = '文章评论';
		}

		$this->assign('ur_here', $title);               //当前页标题
		$this->assign('page_list', $page_list);         //分页
		$this->assign('comment_list', $comment_list);
		
		$this->display('Public:comment');
	}
	
	/**
	 *  @desc     删除评论
	 *  @access   public
	 *  @return   void
	 *  @date     2014-02-20
	 */
	function delComment(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			echo 1;  //没有接收到要删除评论的id
			exit();
		}
		
		$m = new Model('comment');
		$comment = $m->find($id);
		
		if(!$comment){
			echo 2;   //要删除的评论不存在
			exit();
		}
		
		$m->delete($id);		
		echo 0;		
	}
	
	/**
	 *  @desc     评论详情
	 *  @access   public
	 *  @return   void
	 *  @date     2014-02-20 
	 */
	function commentInfo(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$comment_type = isset($_REQUEST['comment_type']) ? intval($_REQUEST['comment_type']) : 0;
		
		if($id == 0){
			$this->error('系统出现错误');
			exit();
		}
		
		$m1 = new Model('comment');
		$m2 = new Model('users');

		$field = 'id,comment_type,id_value,email,content,comment_rank,comment_time,ip_address,user_id,status';
		$comment = $m1->field($field)->find($id);
		$comment['comment_time'] = date("Y-m-d H:i:s", $comment['comment_time']);
		
		//判断是否为匿名用户
		if($comment['user_id'] == 0){
			$comment['user_name'] = '匿名用户';
		}else{
			$user = $m2->field('user_name')->find($comment['user_id']);
			$comment['user_name'] = $user['user_name'];
		}
		
		//判断商品评论还是文章评论
		if($comment['comment_type'] == 0){  //商品评论
			$m3 = new Model('goods');
			$goods = $m3->field('goods_name')->find($comment['id_value']);
			$comment['name'] = $goods['goods_name'];
		}else{  //文章评论
			$m4 = new Model('article');
			$article = $m4->field('title')->find($comment['id_value']);
			$comment['name'] = $article['title'];
		}
		
		if($comment['status'] == 0){
			$comment['show_status'] = '不显示';
		}else{
			$comment['show_status'] = '显示';
		}
		
		if($comment_type == 0){
			$action_link = array('href'=>'__APP__/Goods/goodsComments','text'=>'商品评论');
		}else{
			$action_link = array('href'=>'__APP__/Goods/acticleComments','text'=>'文章评论');
		}
		
		$this->assign('ur_here', '评论详情');
		$this->assign('action_link', $action_link);
		$this->assign('comment', $comment);
		
		$this->display('Public:commentInfo');
	}
	
	/**
	*  @desc     显示隐藏评论
	*  @access   public
	*  @return   void
	*  @date     2014-02-21
	*/
	function commentToggle(){
		$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($data['id'] == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('comment');
		$comment = $m->field('status')->find($data['id']);
		
		if($comment['status'] == 0){
			$data['status'] = 1;
		}else{
			$data['status'] = 0;
		}
		
		if($m->create($data)){
			if($m->save()){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
    
    /**
	*  @desc     根据id获取地区
	*  @access   public
    *  @param    intval   $id   地区id  
	*  @return   string
	*  @date     2014-04-19
	*/
    public function getRegionById($id = 0){
        $m = M('region');
        $region = $m->field('region_name')->find($id);
        return $region['region_name'];
    }
    
    
    
    
    
    
    
    
    
    
    
	
}