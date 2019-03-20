<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      商品搜索
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class SearchAction extends commonAction{	
	/*
	*   @desc    搜索页面
	*   @access  public
	*   @return  void
	*   @date    2014-03-23   2014-04-26 2014-04-27
	*/
	public function index(){
		$search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
		if(!$search){
			$this->referer('您搜索的商品不存在');
			exit();
		}
		
		//记录搜索商品
		if($_SESSION['search'] != $search){
			$s = M('search');
			$field = 'keyword';		
			$where = array('keyword'=>$search);
			$res = $s->field($field)->where($where)->find();
			if($res){  //关键字存在
				$s->where($where)->setInc('count', 1);				
			}else{ //关键字不存在    
				$data['keyword'] = $search;
				$data['count'] = 1;
				$s->create($data);
				$s->add();
			}
			$_SESSION['search'] = $search;
		}
		
		$crumbs = $search;  //面包屑导航
		
		import('ORG.Util.Page');// 导入分页类
		$size = $this->_CFG['page_size']['value'];    //每页显示条数，后台控制
		
		$m = M('goods');
		$field = 'id,goods_name,shop_price,market_price,goods_thumb,sale_number';
		$order = 'sort_order asc,id desc';
		$where['goods_name'] = array('like',"%{$search}%");
		$where['is_delete'] = array('eq', 0);
		$where['is_on_sale'] = array('eq', 1);
		$count = $m->field($field)->where($where)->count();    //商品总数
		
		$page = new Page($count, $size);   //声明分页对象
		$page->setConfig('header','件商品');
		$page->setConfig('theme', "共有 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
		$cat_goods = $m->field($field)->order($order)->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		
		//查询评论数量
		$m2 = M('comment');
		foreach($cat_goods as $key=>$val){
			$where = array('comment_type'=>1,'id_value'=>$val['id'],'status'=>1);
			$count = $m2->where($where)->count();
			$cat_goods[$key]['commentCount'] = $count;
		}

		$this->assign('cat_goods', $cat_goods);
		$this->assign('page_list', $page_list);
		$this->assign('crumbs', $crumbs);
		$this->assign('web_title', $search);		
		$this->display();
	}
	
	
	
	
}