<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     后台商品管理
 * @author   sunwanqing  
 +------------------------------------------------------------------------------
 */
class GoodsAction extends CommonAction{

	/**
	 *  @desc     商品列表、回收站商品列表
	 *  @access   public
	 *  @return   void
	 *  @date     2014-02-02	
	 *            2014-02-16修改，添加商品回收站  
	 */
	public function index(){
		import('ORG.Util.Page');// 导入分页类
		
		$cat_id = isset($_REQUEST['id']) ? intval($_REQUEST[id]) : 0;
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
		$type = isset($_REQUEST['type']) ? intval($_REQUEST['type']) : 0;    // 1:库存报警
		
		//每页显示数量
		$size = 10;		
		$m = new Model('goods');
		
		$where2 = '';
		
		//回收站商品
		if($action == 'trash'){
			$field = 'id,cat_id,goods_name,goods_sn,shop_price';				
			if($cat_id == 0){
				$where = array('is_delete'=>1);
			}else{
				$where = array('is_delete'=>1, 'cat_id'=>$cat_id);
			}
			$action_link = array('href'=>'__URL__/index','text'=>'商品列表');
			$title = '商品回收站';
			$tpl = "goods_trash";
			$where2 = 'is_delete=1';
		}else{
			$field = 'id,cat_id,goods_name,goods_sn,click_count,sale_number,brand_id,goods_number,market_price,shop_price,is_promote,'.
					'promote_start_time,promote_end_time,is_recommend,is_new,warn_number,keywords,goods_brief,goods_desc,'.
					'goods_thumb,goods_img,is_on_sale,is_delete,sort_order,add_time,update_time,integral';
			
			if($cat_id == 0){
				$where = array('is_delete'=>0);
			}else{
				$where = array('is_delete'=>0, 'cat_id'=>$cat_id);
			}
			
			//库存报警
			if($type == 1){
				$where2 = 'goods_number <= warn_number';				
			}elseif($type == 2){ //新品推荐
				$where2 = 'is_recommend=1';
			}elseif($type == 3){
				$where2 = 'is_new=1';
			}else{
				$where2 = 'is_delete = 0';
			}
			
			$action_link = array('href'=>'__URL__/addGoods','text'=>'添加新商品');
			$title = '商品列表';
			$tpl = "goods_list";
		}
	
		
		$order = "sort_order asc,id desc";		
		$count = $m->where($where)->where($where2)->count();

		$page = new Page($count, $size);        //声明分页对象
		$page->setConfig('header','种商品');
		$page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
	
		$goods_list = $m->where($where)->where($where2)->field($field)->order($order)->limit($page->firstRow.','.$page->listRows)->select();

		$this->assign('action_link', $action_link);     //顶部链接
		$this->assign('ur_here', $title);               //当前页标题
		$this->assign('goods_list', $goods_list);       //商品列表
		$this->assign('page_list', $page_list);         //分页
	
		$this->display($tpl);
	}	
	
	/**
	 *  @desc     显示添加商品模板
	 *  @access   public
	 *  @return   void
	 *  @date     2014-02-16
	 */
	public function addGoods(){
		
		$sn_prefix = $this->_CFG['sn_prefix']['value'];
		$goods_number = $this->_CFG['default_storage']['value'];
		$market_price_rate = $this->_CFG['market_price_rate']['value'];   //市场价格比例
		
		$goods['goods_sn'] = $sn_prefix.date("YmdHis").rand(100, 999);
		$goods['click_count'] = 0;
		$goods['sale_number'] = 0;
		$goods['sort_order'] = 100;
		$goods['shop_price'] = 0;
		$goods['market_price'] = 0;
		$goods['goods_number'] = $goods_number;
		$goods['integral'] = 0;
		$goods['is_new'] = 0;
		$goods['is_on_sale'] = 1;
		$goods['is_recommend'] = 0;
		$goods['is_promote'] = 0;
		$goods['promote_price'] = 0.00;
		$goods['goods_img'] = '';
		$goods['warn_number'] = 1;
		$goods['goods_desc'] = '';
		$goods['option'] = $this->option();
		$goods['market_price_rate'] = $market_price_rate;
		
		$this->assign('goods', $goods);
		$action_link = array('href'=>'__URL__/index','text'=>'商品列表');
		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '添加新商品');              //当前页标题
		$this->assign('action', 'add');
		$this->display('goodsInfo');
	}
	
	
	/**
	 *  @desc     编辑商品信息
	 *  @access   public
	 *  @return   boid
	 *  @date     2014-02-18 
	 */
	public function editGoods(){
		$id = isset($_REQUEST[id]) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			$this->error('操作错误', "__APP__/Goods/index");
			exit();
		}
		
		$m = new Model('goods');
		$goods = $m->find($id);
		
		if(!$goods){
			$this->error('该商品不存在!', '__APP__/Goods/index');
			exit();
		}else{
			$goods['market_price_rate'] = $this->_CFG['market_price_rate']['value'];   //市场价格比例
			$goods['option'] = $this->option($goods['cat_id']);
			$action_link = array('href'=>'__URL__/index','text'=>'商品列表');
			
			$this->assign('goods', $goods);
			$this->assign('action_link', $action_link);
			$this->assign('ur_here', '编辑商品');              //当前页标题
			$this->assign('action', 'update');
			$this->display('goodsInfo');
		}		
	}
	
	/**
	*  @desc    保存商品信息
	*  @access  public
	*  @return  void
	*  @date    2014-02-16
	*/
	public function saveGoods(){
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : ''; 
		
		//当是添加商品时
		if($action == 'add'){ 
			$data['goods_name'] = isset($_REQUEST['goods_name']) ?  trim($_REQUEST['goods_name']) : '';
			$data['goods_sn'] = isset($_REQUEST['goods_sn']) ?  trim($_REQUEST['goods_sn']) : '';
			$data['cat_id'] = isset($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;
			$data['sort_order'] = isset($_REQUEST['sort_order']) ? intval($_REQUEST['sort_order']) : 100;
			$data['market_price'] = isset($_REQUEST['market_price']) ? floatval($_REQUEST['market_price']) : 0.00;
			$data['shop_price'] = isset($_REQUEST['shop_price']) ? floatval($_REQUEST['shop_price']) : 0.00;
			$data['click_count'] = isset($_REQUEST['click_count']) ? intval($_REQUEST['click_count']) : 0;
			$data['sale_number'] = isset($_REQUEST['sale_number']) ? intval($_REQUEST['sale_number']) : 0;
			$data['goods_number'] = isset($_REQUEST['goods_number']) ? intval($_REQUEST['goods_number']) : 0;
			$data['warn_number'] = isset($_REQUEST['warn_number']) ? intval($_REQUEST['warn_number']) : 1;
			$data['integral'] = isset($_REQUEST['integral']) ? intval($_REQUEST['integral']) : 0;
			$data['is_new'] = isset($_REQUEST['is_new']) ? intval($_REQUEST['is_new']) : 0;
			$data['is_on_sale'] = isset($_REQUEST['is_on_sale']) ? intval($_REQUEST['is_on_sale']) : 1;
			$data['is_recommend'] = isset($_REQUEST['is_recommend']) ? intval($_REQUEST['is_recommend']) : 0;
			$data['is_promote'] = isset($_REQUEST['is_promote']) ? intval($_REQUEST['is_promote']) : 0;
			$data['keywords'] = isset($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';
			$data['goods_brief'] = isset($_REQUEST['goods_brief']) ? trim($_REQUEST['goods_brief']) : '';
			$data['goods_desc'] = isset($_REQUEST['goods_desc']) ? trim($_REQUEST['goods_desc']) : '';
			
			//促销
			if($data['is_promote'] == 1){
				$data['promote_price'] = isset($_REQUEST['promote_price']) ? floatval($_REQUEST['promote_price']) : 0.00;
				$data['promote_start_time'] = isset($_REQUEST['promote_start_time']) ? trim($_REQUEST['promote_start_time']) : '';
				$data['promote_end_time'] = isset($_REQUEST['promote_end_time']) ? trim($_REQUEST['promote_end_time']) : '';
			}else{
				$data['promote_price'] = 0.00;
				$data['promote_start_time'] = '';
				$data['promote_end_time'] = '';
			}
		}elseif($action == 'update'){ //更新商品
			$data['id'] = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
			if($data['id'] == 0){
				$this->error('保存失败');
				exit();
			}
			
			$data['goods_name'] = trim($_REQUEST['goods_name']);
			$data['cat_id'] = intval($_REQUEST['cat_id']);
			$data['sort_order'] = intval($_REQUEST['sort_order']);
			$data['market_price'] = floatval($_REQUEST['market_price']);
			$data['shop_price'] = floatval($_REQUEST['shop_price']);
			$data['click_count'] = intval($_REQUEST['click_count']);
			$data['sale_number'] = intval($_REQUEST['sale_number']);
			$data['goods_number'] = intval($_REQUEST['goods_number']);
			$data['warn_number'] = intval($_REQUEST['warn_number']);
			$data['integral'] = intval($_REQUEST['integral']);
			$data['is_new'] = intval($_REQUEST['is_new']);
			$data['is_on_sale'] = intval($_REQUEST['is_on_sale']);
			$data['is_recommend'] = intval($_REQUEST['is_recommend']);			
			$data['is_promote'] = intval($_REQUEST['is_promote']);
			$data['keywords'] = trim($_REQUEST['keywords']);
			$data['goods_brief'] = trim($_REQUEST['goods_brief']);
			$data['goods_desc'] = trim($_REQUEST['goods_desc']);
			
			if($data['is_promote'] == 1){
				$data['promote_price'] = floatval($_REQUEST['promote_price']);
				$data['promote_start_time'] = trim($_REQUEST['promote_start_time']);
				$data['promote_end_time'] = trim($_REQUEST['promote_end_time']);
			}else{
				$data['promote_price'] = 0;
				$data['promote_start_time'] = '';
				$data['promote_end_time'] = '';
			}
		}else{
			$this->error('操作失败');
			exit();
		}

		//有图片上传 
		if($_FILES['goods_img']['error'] == 0){
			$thumbWidth = $this->_CFG['thumb_width']['value'];    //缩略图的宽度
			$thumbHeight = $this->_CFG['thumb_height']['value'];  //缩略图的高度
			$imgDir = './Public/Uploads/goods/'.date('Ymd').'/';    //图片保存目录
			$thumbImgDir = './Public/Uploads/goods/'.date('Ymd').'/thumb/';   //缩略图保存目录
			$thumb = 'thumb_';    //缩略图的文件前缀
			$goods_bigThumb = "goods_bigThumb_"; //大缩略图前缀
			
			//图片目录不存在则创建
			if(!file_exists($imgDir)){
				createFolder($imgDir);
			}
				
			//缩略图保存目录不慧则创建
			if(!file_exists($thumbImgDir)){
				createFolder($thumbImgDir);
			}
			
			import('ORG.Net.UploadFile');    //文件上传类
			$upload = new UploadFile();      // 实例化上传类
			$upload->savePath = $imgDir;     //图片上传目录
			$upload->allowExts = $this->imgAllowExts;   //允许图片上传的格式
			$upload->thumb = true;  //对图片进行缩略
			$upload->thumbMaxWidth = $thumbWidth.',800';   //缩略图的宽度
			$upload->thumbMaxHeight = $thumbHeight.',800'; //缩略图的高度
			$upload->thumbPath = $thumbImgDir;       //缩略图的上传目录
			$upload->thumbPrefix = $thumb.",".$goods_bigThumb;           //缩略图的文件前缀
			
			if(!$upload->upload()) {   //上传错误提示错误信息
				$this->error($upload->getErrorMsg());
			}else{// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$info = $info[0];	
			}
		}else{  //没有图片上传
			$imgDir = $thumbImgDir = '';
			$info['savename'] = '';
			$thumb = '';
		}
		
		$data['update_time'] = time();				
		$data['brand_id'] = 0;  //暂时未起作用

		$m =new Model('goods');
		
		//添加商品
		if($action == 'add'){
 			$data['goods_img'] = $imgDir.$info['savename'];
			$data['goods_thumb'] = $thumbImgDir.$thumb.$info['savename'];
			$data['goods_bigThumb'] = $thumbImgDir.$goods_bigThumb.$info['savename'];
 			$data['add_time'] = time();
 			$data['is_on_sale'] = 1;
 			$data['is_delete'] = 0;
			
			$data['add_time'] = time();
			if($m->create($data)){
				if($m->add()){					
					$this->success('上传成功', '__APP__/Goods/index');
					exit();
				}else{
					$this->error('添加商品失败');
				}
			}else{
				$this->error('添加商品失败');
				exit();
			}			
		}elseif($action == 'update'){  //更新商品
			//有图片上传时更新商品图片信息
			if($_FILES['goods_img']['error'] == 0){
				$data['goods_img'] = $imgDir.$info['savename'];
				$data['goods_thumb'] = $thumbImgDir.$thumb.$info['savename'];
				$data['goods_bigThumb'] = $thumbImgDir.$goods_bigThumb.$info['savename'];
			}
			if($m->create($data)){
				if($m->save()){
					$this->success('保存成功', '__APP__/Goods/index');
				}else{
					$this->error('保存失败');
				}
			}else{
				$this->error('保存失败');
			}
		}else{
			$this->error('操作失败','__URL__/Goods/index');
			exit();
		}
	}

	/**
	 *  @desc     将商品放入回收站、还原，从回收站里删除
	 *  @access   public
	 *  @return   void
	 *  @date     2014-02-16
	 */
	public function remove(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		/*
		 *   trash:放入回收站        delete:从回收站中删除        reduction:从中回收站中删除商品
		 */
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

		if($id == 0 || empty($action)){
			echo 0;  //操作失败
			exit();
		}
		
		$m = new Model('goods');
		$goods = $m->find($id);
		if(!$goods){ //商品不存在
			echo 0;
			exit();
		}
		
		if($action == 'reduction'){  //从回收站中还原商品
			$data['id'] = $id;
			$data['is_delete'] = 0;
			if($m->create($data)){
				$m->save();
				echo 1;
				exit();
			}else{
				echo 0;
				exit();
			}
		}elseif($action  == 'trash'){ //将商品放入回收站
			$data['id'] = $id;
			$data['is_delete'] = 1;
			if($m->create($data)){
				$m->save();
				echo 1;
				exit();
			}else{
				echo 0;
				exit();
			}
 		}elseif($action == 'delete'){  //从回收站中删除商品
			if($m->delete($id)){
				if(file_exists($goods['goods_img'])){
					unlink($goods['goods_img']);
				}
				if(file_exists($goods['goods_thumb'])){
					unlink($goods['goods_thumb']);
				}
				
				$m2 =  new Model('comment');
				$where = array('comment_type'=>0, 'id_value'=>$id);
				
				$m2->where($where)->delete();
				echo 1;	
			}else{
				echo 0;
			}
			exit();
		}else{  //操作失败
			echo 0;
			exit();
		}
	}
	
	/**
	 *  @desc    商品如果是上架则改为下架，如果是下架，刚改为下架
	 *  @access  public
	 *  @return  void
	 *  @date    2014-02-18
	 */
	public function onSaleToggle(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  //要修改商品的id
		$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';   //要修改的字段  is_on_sale   is_new   is_recommend
		if($id == 0 || empty($type)){  
			echo 0;
			exit();
		}

		$m = new Model('goods');
		$goods = $m->find($id);
		
		if(empty($goods)){ //商品不存在
			echo 1;
			exit();
		}else{
			$state = $goods[$type];
			if($state == 1){
				$state = 0;
			}else{
				$state = 1;
			}
			$data['id'] = $id;
			$data[$type] = $state;
			$m->save($data);
			echo 2;  //修改成功
		}
	}
	
	/**
	 *   @desc     商品评论
	 *   @access   public
	 *   @return   void
	 *   @date     2014-02-19
	 */
	function goodsComments(){		
		$this->comment(0);
	}
	
}