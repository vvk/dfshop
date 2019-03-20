<?php
defined('DF_ROOT') or die('Hacking attempt');

import('@.Action.CommonAction');

class CategoryAction extends commonAction {
	
	/**
	 *   @desc    商品列表
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-26
	 */
    public function index(){
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	if($id == 0){
    		$this->referer('分类id丢失');
    		exit();
    	}

    	//产品分类id
    	$cat = M('category');
    	$catInfo = $cat->field('id,cat_name,keywords,cat_desc,parent_id')->find($id); 
    	$catInfo['keywords'] = $catInfo['keywords'] ? $catInfo['keywords'] : $this->_CFG['shop_keywords']['value'];
    	$catInfo['desc'] = $catInfo['desc'] ? $catInfo['desc'] : $this->_CFG['shop_desc']['value'];
    	$crumbs = $this->getCrumbs($catInfo['id'], 1);   

    	$in = '';
    	if($catInfo['parent_id'] == 0){
    		$cat_ids = $cat->field('id')->where(array('parent_id'=>$id))->select();    		 
    		foreach($cat_ids as $key=>$val){
    			$in .=$val['id'].',';
    		}
    		$in = substr($in, 0 ,-1);
    	}else{
    		$in = "{$id}";
    	}

    	import('ORG.Util.Page');// 导入分页类
    	$size = $this->_CFG['page_size']['value'];    //每页显示条数
    	
    	$m = M('goods');
    	$field = 'id,goods_name,shop_price,market_price,goods_thumb,sale_number';
    	if($this->_CFG['sort_order_method']['value'] == 0){
    		$order = 'sort_order desc,id desc';
    	}else{
    		$order = 'sort_order asc,id desc';
    	}
    	
     	
     	$where['cat_id'] = array('in', $in);
     	$where['is_delete'] = array('eq', 0);
     	$where['is_on_sale'] = array('eq', 1);
     	$count = $m->field($field)->where($where)->count();    //商品总数

     	$page = new Page($count, $size);   //声明分页对象     	
     	$page->setConfig('header','件商品');
     	$page->setConfig('theme', "共有 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
     	$page_list = $page->show();
     	$cat_goods = $m->field($field)->order($order)->where($where)->limit($page->firstRow.','.$page->listRows)->select();

     	$m2= M('comment');
     	foreach($cat_goods as $key=>$val){
     		$count = $m2->where(array('comment_type'=>0,'id_value'=>$val['id'],'status'=>1))->count();
     		$cat_goods[$key]['commentNum'] = $count;
     	}

        $par = '/\/index.php\/Category\/index\/id\/[\d]+\/p\/([\d]+)/';
        $rep = "category-$id-$1";
        $page_list = preg_replace($par, $rep, $page_list);

    	$this->assign('cat_goods', $cat_goods);
    	$this->assign('crumbs', $crumbs);
    	$this->assign('page_list', $page_list);
    	$this->assign('catInfo', $catInfo);
		$this->display();
    }
    
   
   
}