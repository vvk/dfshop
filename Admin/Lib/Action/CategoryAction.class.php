<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc      后台商品分类管理
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */

class CategoryAction extends CommonAction{
	
	/*
	 *   分类列表
	 *   @access  public
	 *   @return  void
	 *   @date    2014-02-10
	 */
	public function index(){
		$action_link = array('href'=>'__URL__/addCategory','text'=>'添加分类');
	
		$this->assign('category',$this->cat);
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '商品分类');              //当前页标题		
		$this->display('category_list');
	}
	
	/*
	 *   添加分类显示页面
	 *   @access public 
	 *   @return void
	 *   @date   2014-02-10
	 */
	public function addCategory(){		
			
		$category['option'] = $this->option();
		$category['cat_name'] = '';
		$category['sort_order'] = 50;
		$category['is_show'] = 1;
		$category['keywords'] = '';
		$category['cat_desc'] = '';
		
		$action_link = array('href'=>'__URL__/index','text'=>'商品列表');

		$this->assign('action', 'add');
		$this->assign('category', $category);
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '添加分类');              //当前页标题
		$this->display('category_info');
	}
	
	/*
	 *   显示分类信息
	 *   @access public
	 *   @return void
	 *   @date   2014-02-13 
	 */
	public function categoryInfo(){
		$cat_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($cat_id == 0){
			header('Location:index');
			exit();
		}
		$m = new Model('category');
		$where = array('id'=>$cat_id);
		$field = "id,cat_name,keywords,cat_desc,parent_id,sort_order,show_in_nav,is_show";
		$category = $m->field($field)->where($where)->find();
	
		$category['option'] = $this->option($category['parent_id']);			
		$action_link = array('href'=>'__URL__/index','text'=>'商品分类列表');
		
		$this->assign('category', $category);
		$this->assign('action', 'update');
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '编辑商品分类');              //当前页标题
		$this->display('category_info');
	}
	
	/*
	 *   保存分类信息
	 *   #access public
	 *   @return void
	 *   @date   2014-02-15
	 */
	public function saveCategory(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
		$data['cat_name'] = isset($_REQUEST['cat_name']) ? trim($_REQUEST['cat_name']) : '';
		$data['parent_id'] = isset($_REQUEST['parent_id']) ? intval($_REQUEST['parent_id']) : 0;
		$data['sort_order'] = isset($_REQUEST['sort_order']) ? intval($_REQUEST['sort_order']) : 50;
		$data['is_show'] = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 1;
		$data['keywords'] = isset($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';
		$data['cat_desc'] = isset($_REQUEST['cat_desc']) ? trim($_REQUEST['cat_desc']) : '';

		$m = new Model('category');
		if($action == 'add'){ //添加
			if($m->create()){
				$m->add();
				$this->success('添加成功',__URL__.'/index');
			}else{
				$this->error($m->getError());
			}
		}elseif($action == 'update' && $id != 0){ //更新
			$data['id'] = $id;
			if($m->create()){
				$m->save();
				$this->success('保存成功',__URL__.'/index');
			}else{
				$this->error($m->getError());
			}
		}else{
			$this->error('操作失败', __URL__.'/index');
		}
		
	}
	
	/*
	 * 通过AJAX请求删除分类信息
	 * @access  public
	 * @return  boid
	 * @date    2014-02-15
	 */
	public function delCategory(){
		$result = array();
		$cat_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		//要删除的为顶级分类
		if($cat_id == 0){
			$result['state'] = 1;
			$result['info'] = '顶级不能删除！';			
			echo json_encode($result);
			exit();
		}

		
		$m = new Model('category');
		$cat = $m->find($cat_id);
		
		//判断当前分类是否存在,如果不存在
		if(!$cat){
			$result['state'] = 2;
			$result['info'] = '您要删除的分类不存在';
			echo json_encode($result);
			exit();
		}
		
		//判断当前分类下是否存在子分类
		$count = $m->where(array('parent_id'=>$cat_id))->count();
		if($count>0){
			$result['state'] =3;
			$result['info'] = $cat['cat_name'].'不是末级分类，您不能删除！';			
			echo json_encode($result);
			exit();
		}
		
		//判断当前分类下是否存在商品
		$m2 = new Model('goods');
		$goods_number = $m2->where(array('cat_id'=>$cat_id))->count();
		if($goods_number>0){
			$result['state'] =4;
			$result['info'] = '不是末级分类，您不能删除！';
			echo json_encode($result);
			exit();
		}
		
		$result['state'] = 0;
		$result['info'] = $m->where(array('id'=>$cat_id))->delete();
		echo json_encode($result);
		exit();
	}	

	
	
}