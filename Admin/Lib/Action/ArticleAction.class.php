<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     后台文章管理
 * @author   sunwanqing  
 +------------------------------------------------------------------------------
 */
class ArticleAction extends CommonAction{
	private $article_cat = array();  //文章分类信息 

	/*
	*  @desc     文章列表
	*  @access   public
	*  @return   void
	*  @date     2014-03-02
	*/
	public function index(){
	    $category_tree = $this->get_categories_tree(0,0);
		$this->get_category($category_tree);   //将分类树放在一级数组中
       
		import('ORG.Util.Page');// 导入分页类
		
		$cat_id = isset($_REQUEST['id']) ? intval($_REQUEST[id]) : 0;
		
		//每页显示数量，从后台调取，此处先定为10条
		$size = 10;
		$m = new Model('article');
		$field = "id,cat_id,title,content,author,authoremail,keywords,article_desc,abstract,sort_order,add_time,";
		$field .= "update_time,is_show";
		
		if($cat_id == 0){
			$where = array();
		}else{
			$where = array('cat_id'=>$cat_id);
		}
		
		$order = "id desc,sort_order desc";

		$count = $m->where($where)->count();
		$page = new Page($count, $size);        //声明分页对象
		$page->setConfig('header','篇文章');
		$page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
		
		$article = $m->where($where)->field($field)->order($order)->limit($page->firstRow.','.$page->listRows)->select();

		foreach($article as $key=>$val){
			$article[$key]['add_time'] = date("Y-m-d H:i:s", $val['add_time']);
			$article[$key]['update_time'] = date("Y-m-d H:i:s", $val['update_time']);
			
			$m2 = new Model('article_category');
			$field = "cat_name";
			$cat = $m2->field($field)->find($val['cat_id']);
			$article[$key]['cat_name'] = $cat['cat_name'];
		}
		
		$action_link = array('href'=>"__APP__/Article/articleInfo", 'text'=>'添加新文章');
		
		$this->assign('ur_here', '文章列表');
		$this->assign('action_link', $action_link);
		$this->assign('article', $article);
		$this->assign('page_list', $page_list);
		
		$this->display();
	}
	
	/*
	 *  @desc   添加、修改文章
	 *  @access public
	 *  @return void
	 *  @date   2014-03-02
	 */
	public function articleInfo(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		if($id == 0){  //添加文章
			$article['title'] = '';
			$article['content'] = '';
			$article['author'] = '';
			$article['authoremail'] = '';
			$article['keywords'] = '';
			$article['article_desc'] = '';
			$article['abstract'] = '';
			$article['sort_order'] = 50;
			$article['is_show'] = 1;
			$article['option'] = $this->option();		

			$ur_here = '添加新文章';
			$action = 'add';
		}else{  //编辑文章
			if($id == 0){
				$this->error('操作失败');
				exit();
			}
			
			$m = new Model('article');
			$field = "id,cat_id,title,content,author,authoremail,keywords,article_desc,abstract,sort_order,add_time,";
			$field .= "update_time,is_show";
			
			$article = $m->field($field)->find($id);

			$article['option'] = $this->option($article['cat_id']);			
			
			$ur_here = '编辑文章内容';
			$action = 'edit';
		}
		
		$action_link = array('href'=>"__APP__/Article/index", 'text'=>'文章列表');
		
		$this->assign('action_link', $action_link);
		$this->assign('action', $action);
		$this->assign('ur_here', $ur_here);
		$this->assign('article', $article);
		
		$this->display();
	}
	
	/*
	 *  @desc    删除文章
	 *  @access  public
	 *  @return  void
	 *  @date    2014-03-02
	 */
	public function delArticle(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		if($id == 0){
			echo 0;  //操作失败
			exit();
		}
		
		$m = new Model('article');
		$article = $m->find($id);
		if(!$article){ //商品不存在
			echo 0;
			exit();
		}

		$date['id'] = $id;
		if($m->delete($id)){
			$m2 =  new Model('comment');
			$where = array('comment_type'=>1, 'id_value'=>$id);
			
			$m2->where($where)->delete();
			echo 1;		
		}else{
			echo 0;
		}

	}
	
	/*
	 *   @desc    保存文章信息
	 *   @access  public
	 *   @return  void
	 *   @date    2014-03-02
	 */
	public function saveArticle(){
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
		if($action != 'edit' && $action != 'add'){
			$this->error('操作失败');
			exit();
		}
		
		$data['title'] = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
		$data['keywords'] = isset($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';
		$data['cat_id'] = isset($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0;
		$data['content'] = isset($_REQUEST['content']) ? trim($_REQUEST['content']) : '';
		$data['author'] = isset($_REQUEST['author']) ? trim($_REQUEST['author']) : '';
		$data['authoremail'] = isset($_REQUEST['authoremail']) ? trim($_REQUEST['authoremail']) : '';
		$data['article_desc'] = isset($_REQUEST['article_desc']) ? trim($_REQUEST['article_desc']) : '';
		$data['abstract'] = isset($_REQUEST['abstract']) ? trim($_REQUEST['abstract']) : '';
		$data['sort_order'] = isset($_REQUEST['sort_order']) ? intval($_REQUEST['sort_order']) : 50;
		$data['abstract'] = isset($_REQUEST['abstract']) ? trim($_REQUEST['abstract']) : '';
		$data['is_show'] = isset($_REQUEST['is_show']) ? trim($_REQUEST['is_show']) : 1;
		$data['update_time'] = time();
		
		if(!$data['title']){
			$this->error('文章标题不能为空');
			exit();
		}
		if(!$data['content']){
			$this->error('文章内容不能为空');
			exit();
		}
		
		$m = new Model('article');
		if($action == 'edit'){
			$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
			if($data['id'] == 0){
				$this->error('操作失败');
				exit();
			}
			
			if($m->create($data)){
				if($m->save()){
					$this->success('保存成功', '__APP__/Article/index');
				}else{
					$this->error('保存失败');
				}	
			}else{
				$this->error('保存失败');
			}			
		}else{
			$data['add_time'] = time();
			if($m->create($data)){
				if($m->add()){
					$this->success('添加成功', '__APP__/Article/index');
				}else{
					$this->error('添加失败');
				}
			}else{
				$this->error('添加失败');
			}
		}
	}
	
	/*
	 *  @desc    点击修改文章是否显示
	 *  @access  public
	 *  @return  void
	 *  @date    2014-03-02
	 */
	public function articleShowToggle(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  //要修改商品的id
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('article');
		$category = $m->find($id);
		
		if(empty($category)){ //分类不存在
			echo 1;
			exit();
		}else{
			$state = $category['is_show'];
			if($state == 1){
				$is_show = 0;
			}else{
				$is_show = 1;
			}
			$data['id'] = $id;
			$data['is_show'] = $is_show;
			if($m->create($data)){
				if($m->save()){
					echo 2;
				}else{
					echo 0;
				}
			}else{
				echo 0;
			}
		}
	}
	
	/*
	 *   @desc   文章分类列表
	 *   @access public
	 *   @return void
	 *   @date   2014-03-02
	 */
	public function category(){

		$action_link = array('href'=>'__APP__/Article/categoryInfo','text'=>'添加文章分类');
		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '分类列表');
		$this->assign('article_category', $this->article_cat);
		
		$this->display();
	}
	
	/*
	 *   @desc    添加、修改文章分类
	 *   @access  public 
	 *   @return  void
	 *   @date    2014-03-02
	 */
	public function categoryInfo(){
		$id= isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

		if($id == 0){ //添加分类
			$category['cat_name'] = '';
			$category['option'] = $this->option();
			$category['is_show'] = 1;
			$category['sort_order'] = 50;
			$category['keywords'] = '';
			$category['cat_desc'] = '';

			$ur_here = '添加文章分类';
			$action = 'add';
		}else{  //编辑分类
			$m = new Model("article_category");
			$field = "id,cat_name,keywords,cat_desc,parent_id,sort_order,show_in_nav,is_show";
			$category = $m->field($field)->find($id);
			$category['option'] = $this->option($category['parent_id']);

			$ur_here = '文章分类编辑';
			$action = 'edit';
		}
		
		$action_link = array('href'=>'__APP__/Article/category','text'=>'文章分类');
		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', $ur_here);
		$this->assign('action', $action);
		$this->assign('category', $category);
		
		$this->display();
	}
	
	/*
	 *  @desc    点击修改分类显示还是不显示
	 *  @access  public
	 *  @return  void
	 *  @date    2014-03-02
	 */
	public function categoryShowToggle(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  //要修改商品的id
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('article_category');
		$category = $m->find($id);
		
		if(empty($category)){ //分类不存在
			echo 1;
			exit();
		}else{
			$state = $category['is_show'];
			if($state == 1){
				$is_show = 0;
			}else{
				$is_show = 1;
			}
			$data['id'] = $id;
			$data['is_show'] = $is_show;
			if($m->create($data)){
				if($m->save()){
					echo 2;
				}else{
					echo 0;
				}
			}else{
				echo 0;
			}
		}
	}
	
	/*
	 *   @desc   保存分类信息
	 *   @access pulbic
	 *   @return void
	 *   @date   2014-03-02
	 */
	public function saveCategory(){
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';

		if($action != 'edit' && $action != 'add'){
			$this->error("操作失败");
			exit();
		}
		
		$data['cat_name'] = isset($_REQUEST['cat_name']) ? trim($_REQUEST['cat_name']) : '';
		$data['keywords'] = isset($_REQUEST['keywords']) ? trim($_REQUEST['keywords']) : '';
		$data['cat_desc'] = isset($_REQUEST['cat_desc']) ? trim($_REQUEST['cat_desc']) : '';
		$data['parent_id'] = isset($_REQUEST['parent_id']) ? intval($_REQUEST['parent_id']) : 0;
		$data['sort_order'] = isset($_REQUEST['sort_order']) ? intval($_REQUEST['sort_order']) : 50;
		$data['show_in_nav'] = isset($_REQUEST['show_in_nav']) ? intval($_REQUEST['show_in_nav']) : 0;
		$data['is_show'] = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 1;
		if(empty($data['cat_name'])){
			$this->error('分类名称不能为空');
			exit();
		}
		
		$m = new Model("article_category");
		if($action == 'edit'){
			$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
			if($data['id'] == 0){
				$this->error('保存失败');
				exit();
			}
			
			if($m->create($data)){
				if($m->save()){
					$this->success('保存成功', '__APP__/Article/category');
				}else{
					$this->error('保存失败');
				}	
			}else{
				$this->error('保存失败');
			}	
		}else{			
			if($m->create($data)){
				if($m->add()){
					$this->success('添加成功', '__APP__/Article/category');
				}else{
					$this->error('保存失败');
				}
			}else{
				$this->error('保存失败');
			}			
		}
	}
	
	/*
	 *  @desc    删除文章分类
	 *  @access  public
	 *  @return  void
	 *  @date    2014-03-02
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
		
		
		$m = new Model('article_category');
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
		$m2 = new Model('article');
		$goods_number = $m2->where(array('cat_id'=>$cat_id))->count();
		if($goods_number>0){
			$result['state'] =4;
			$result['info'] = '您要删除的分类下面有文章，请删除文章后再删除';
			echo json_encode($result);
			exit();
		}
		
		$result['state'] = 0;
		$result['info'] = $m->where(array('id'=>$cat_id))->delete();
		echo json_encode($result);
		exit();
	}
	
	
	/**
	 * @desc    获得指定分类同级的所有分类以及该分类下的子分类
	 * @access  protected
	 * @param   integer     $cat_id     分类编号
	 * @param   integer     $level      等级
	 * @return  array
	 * @date    2014-03-02
	 */
	protected function get_categories_tree($cat_id = 0, $level = 0){
		$level++;
		$m = new Model('article_category');
	
		$cat_arr = array();
		$count = $m->where()->count();

		if($count == 0){
			return $cat_arr;
			exit();
		}else{
			$field = 'id,cat_name,keywords,cat_desc,parent_id,sort_order,is_show';
			$where = array('parent_id'=>$cat_id);
			$res = $m->where($where)->field($field)->select();
			foreach($res as $row){
				$cat_arr[$row['id']]['id'] = $row['id'];
				$cat_arr[$row['id']]['cat_name'] = $row['cat_name'];
				$cat_arr[$row['id']]['keywords'] = $row['keywords'];
				$cat_arr[$row['id']]['cat_desc'] = $row['cat_desc'];
				$cat_arr[$row['id']]['parent_id'] = $row['parent_id'];
				$cat_arr[$row['id']]['sort_order'] = $row['sort_order'];
				$cat_arr[$row['id']]['is_show'] = $row['is_show'];
				$cat_arr[$row['id']]['level'] = $level;
	
				$where = array('cat_id'=>$row['id']);
	
				$m2= new Model('article');
				$article_number = $m2->where($where)->group('cat_id')->count();
				$cat_arr[$row['id']]['article_number'] = $article_number;
	
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
			$this->article_cat[] = $temp;
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
		foreach($this->article_cat as $row){
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
	
	
	
	
}