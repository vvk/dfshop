<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      文章管理
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class ArticleAction extends commonAction{

    /**
    *   @desc    文章列表
    *   @access  public
    *   @return  void
    *   @date    2014-05-05
    */     
    public function index(){
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	    	
    	import('ORG.Util.Page');// 导入分页类
    	$size = $this->_CFG['article_page_size']['value'];    //每页显示条数
 
    	$m = M('article');
    	$field = 'id,cat_id,title,content,author,add_time';
    	$where = array('is_show'=>1);
    	if($id != 0){
    		$where = array_merge($where, array('cat_id'=>$id));
    	}
    	$order = 'sort_order asc,update_time desc,id desc';
    	$count = $m->where($where)->count();
    	
    	$page = new Page($count, $size);   //声明分页对象
    	$page->setConfig('header','篇文章');
    	$page->setConfig('theme', "共有 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
        $page_list = $page->show();

        $article = $m->field($field)->order($order)->where($where)->limit($page->firstRow.','.$page->listRows)->select();
    	foreach($article as $key=>$val){
    		$article[$key]['time'] = date($this->time_format, $val['add_time']);
    	}

    	//面包屑导航
    	if($id != 0){
    		$crumbs = $this->getArticleCrumbs($id, 1);   
    	}else{
    		$crumbs = '<span>最新文章</span>';
    	}

    	$this->assign('page_list', $page_list);
    	$this->assign('article', $article);
        $this->assign('web_title', '最新文章');
        $this->assign('crumbs', $crumbs);
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);  //关键字
        $this->assign('description', $this->_CFG['shop_desc']['value']);   //描述
        $this->display();
    }
    
    /**
     *   @desc    文章内容
     *   @access  public
     *   @return  void
     *   @date    2014-05-05
     */
    public function article(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        if($id == 0){
        	$this->referer('文章id丢失');
        	exit();
        }
        
        $m = M('article');
        $field = 'id,title,content,author,keywords,article_desc,abstract,add_time';
    	$where = array('is_show'=>1);
    	$article = $m->field($field)->where($where)->find($id);
    	if(!$article){
    		$this->referer('您要查看的文章不存在或已删除');
    		exit();
    	}
    	
    	$article['time'] = date($this->time_format, $article['add_time']);
    	$article['keywords'] = $article['keywords'] ? $article['keywords'] : $this->_CFG['shop_keywords']['value'];
    	$article['article_desc'] = $article['article_desc'] ? $article['article_desc'] : $this->_CFG['shop_desc']['value'];    	
    	
    	$crumbs = $this->getArticleCrumbs($article['id'], 0);   //面包屑导航
    	
    	$order = 'sort_order asc';
    	$field = 'id,title';
    	
    	//上一篇
    	$where['id'] = array('gt',$id);
    	$prevPage = $m->field($field)->where($where)->order($order)->find();
    	
    	//下一篇
    	$where['id'] = array('lt',$id);
    	$nextPage = $m->field($field)->where($where)->order($order)->find();
	
    	$this->assign('article', $article);
    	$this->assign('prevPage', $prevPage);
    	$this->assign('nextPage', $nextPage);
    	$this->assign('crumbs', $crumbs);
        $this->display();
    }

    /**
     *   @desc    文章面包屑导航 
     *   @access  protected
     *   @param   int $id     文章或分类id
     *            int $type   0:文章，1：文章分类
     *   @return  string
     *   @date    2014-05-05
     */
    protected function getArticleCrumbs($id, $type = 0){
    	if($type == 0){
    		$m1 = M('article');
    		$field = 'cat_id,title';
    		$res = $m1->field($field)->find($id);
    		$crumbs = "<span>{$res['title']}</span>";
    		$id = $res['parent_id'];
    	}   	
		
    	$m2 = M('article_category');
    	$field = 'id,cat_name,parent_id';
    	$res = $m2->field($field)->find($id);
    	if($type == 0){
    		$crumbs = "<a href='/article-list-".$res['id'].".html'>{$res['cat_name']}</a>>".$crumbs;
    	}else{
    		$crumbs = "<span>{$res['cat_name']}</span>";
    	}
    	
    	while($res['parent_id'] != 0){
    		$res = $m2->field($field)->find($res['parent_id']);
    		$crumbs = "<a href='/article-list-".$res['id']."'.html>{$res['cat_name']}</a>>".$crumbs;
    	}
    	$crumbs = "<a href='/article.html'>最新文章</a>>".$crumbs;
    	return $crumbs;
    }
    
}