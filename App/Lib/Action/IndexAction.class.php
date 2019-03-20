<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      首页
 * @author    suning920@qq.com
 +------------------------------------------------------------------------------
 */
class IndexAction extends commonAction {
	/**
	 *   @desc    首页
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-24
	 */
    public function index(){
        $m = M('goods');

        //手机
        $sql = 'select g.`id`,g.`goods_name`,g.`market_price`,g.`shop_price`,g.`goods_thumb` from df_goods ';
        $sql .= 'as g,df_category as c where g.`is_delete`=0 and g.`is_on_sale`  and g.`cat_id`=c.`id` ';
        $sql .= 'and c.`parent_id`=1 order by g.`sort_order` asc,g.`id` desc limit 8';
        $phone = $m->query($sql);
        
        //电脑
        $sql = 'select g.`id`,g.`goods_name`,g.`market_price`,g.`shop_price`,g.`goods_thumb` from df_goods ';
        $sql .= 'as g,df_category as c where g.`is_delete`=0 and g.`is_on_sale`  and g.`cat_id`=c.`id` ';
        $sql .= 'and c.`parent_id`=2 order by g.`sort_order` asc,g.`id` desc limit 8';
        $computer = $m->query($sql);
        
        //相机
        $sql = 'select g.`id`,g.`goods_name`,g.`market_price`,g.`shop_price`,g.`goods_thumb` from df_goods ';
        $sql .= 'as g,df_category as c where g.`is_delete`=0 and g.`is_on_sale`  and g.`cat_id`=c.`id` ';
        $sql .= 'and c.`parent_id`=3 order by g.`sort_order` asc,g.`id` desc limit 8';
        $camera = $m->query($sql);

    	$this->assign('newGoods', $this->getNewGoods());                   //新品上架
    	$this->assign('recommendedGoods', $this->getRecommendedGoods());   //推荐商品
    	$this->assign('hotGoods', $this->getHotGoods());                   //热销商品
   		$this->assign('phone', $phone);                                    //手机
   		$this->assign('computer', $computer);                              //电脑
   		$this->assign('camera', $camera);                                  //相机
    	$this->assign('banner', $this->getBanner());
    	$this->assign('web_title', $this->_CFG['shop_name']['value'].'--致力于打造最给力的B2C电子商城');
        $this->assign('keywords', $this->_CFG['shop_keywords']['value']);  //关键字
        $this->assign('description', $this->_CFG['shop_desc']['value']);   //描述
        $this->assign('shop_notice', $this->_CFG['shop_notice']['value']); //商店公告

		$this->display();
    }
    
    /**
    *   @desc    banner图片
    *   @access  protected
    *   @return  void
    *   @date    2014-03-25
    */   
    protected function getBanner(){
        $m = M('banner');
        $field = 'id,banner_name,banner_url';
        $order = 'sort_order asc';
        $where = array('is_show'=>1);
        $banner = $m->where($where)->field($field)->order($order)->select();
        return $banner;
    }
    
    
   
}