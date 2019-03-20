<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     后台首页
 * @author   sunwanqing
 +------------------------------------------------------------------------------
 */

class IndexAction extends CommonAction {
	/**
     * @desc   后台首页
     * @access public
     * @return void
     */
    public function index(){
		
    	$this->assign('web_title', '东方商城后台管理中心');
    	$this->display();
    }
    
    /**
     * @desc    后台顶部
     * @access  public
     * @return  void
     */
    public function top(){    
    	$this->display();
    }
    
    /**
     * @desc   后台左部菜单
     * @access public
     * @return void
     */
    public function menu(){    	
    	$this->display();
    }
    
    /**
     * @desc   后台左部拖动
     * @access public
     * @return void
     */
    public function drag(){
    	$this->display();
    }
    
    /**
     * @desc   后台内容部分
     * @access public
     * @return void
     */
    public function main(){
    	$gd = gd_version();    //GD库版本
    	
    	/* 订单信息 */
 		$o = new Model('order_info');
        
        //未付款
    	$where = array('order_status'=>0);
    	$order['no_pay'] = $o->where($where)->count();
    	
        //已付款
    	$where = array('order_status'=>1);
    	$order['pay'] = $o->where($where)->count();
    	
        //未发货
    	$where = array('shipping_status'=>1, 'shipping_status'=>0);
    	$order['no_ship'] = $o->where($where)->count();
    	
        //已发货
    	$where = array('shipping_status'=>1, 'shipping_status'=>1);
    	$order['ship'] = $o->where($where)->count();
    	
    	/* 商品信息 */
 		$g = new Model('goods');
 		$where = array('is_on_sale'=>1, 'is_delete'=>0);
 		$goods['total'] = $g->where($where)->count();
 		
        //推荐数量
 		$where = array('is_recommend'=>1);
 		$goods['recommend'] = $g->where($where)->count();
 		
        //促销数量
 		$where = array('is_new'=>1);
 		$goods['new'] = $g->where($where)->count();
        
        //库存报警数量
        $sql = "select count(*) as warn_number from df_goods where goods_number<=warn_number";
        $res = $g->query($sql);
        $goods['warn_number'] = $res[0]['warn_number'];

 		$m = new Model();
 		$sql = "select VERSION()";
 		$re = $m->query($sql);
		$sys_info['mysql_ver'] = $re[0]['VERSION()'];  //mysql版本

    	/* 系统信息 */
    	$sys_info['os']            = PHP_OS;
   		$sys_info['ip']            = $_SERVER['SERVER_ADDR'];
        $sys_info['web_server']    = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['php_ver']       = PHP_VERSION;
        $sys_info['zlib']          = function_exists('gzclose') ? '是':'否';
        $sys_info['safe_mode']     = (boolean) ini_get('safe_mode') ?  '是':'否';
        $sys_info['safe_mode_gid'] = (boolean) ini_get('safe_mode_gid') ? '是' : '否';
        $sys_info['timezone']      = function_exists("date_default_timezone_get") ? date_default_timezone_get() : '未设置';
        $sys_info['socket']        = function_exists('fsockopen') ? '是' : '否';
    
        if ($gd == 0){
        	$sys_info['gd'] = 'N/A';
        }else{
        	if ($gd == 1){
        		$sys_info['gd'] = 'GD1';
        	}
        	else{
        		$sys_info['gd'] = 'GD2';
        	}
        
        	$sys_info['gd'] .= ' (';
        
        	/* 检查系统支持的图片类型 */
        	if ($gd && (imagetypes() & IMG_JPG) > 0){
        		$sys_info['gd'] .= ' JPEG';
        	}
        
        	if ($gd && (imagetypes() & IMG_GIF) > 0){
        		$sys_info['gd'] .= ' GIF';
        	}
        
        	if ($gd && (imagetypes() & IMG_PNG) > 0){
        		$sys_info['gd'] .= ' PNG';
        	}
        
        	$sys_info['gd'] .= ')';
        }
        /* 允许上传的最大文件大小 */
        $sys_info['max_filesize'] = ini_get('upload_max_filesize');
        $sys_info['lang'] = C('DEFAULT_LANG');
        $sys_info['DEFAULT_CHARSET'] = C('DEFAULT_CHARSET');
        
        $this->assign('order', $order);            //订单信息
        $this->assign('goods', $goods);            //商品信息
        $this->assign('sys_info', $sys_info);      //系统信息
        
    	$this->display('start');
    }
    
    function box(){
    	$this->display();
    }
    
    /*
     *   @desc    显示计算器
     *   @access  public
     *   @return  void
     *   @date    2014-03-02
     */
    public function calculator(){
    	$this->display();
    }
    
    /*
     *  @desc     删除缓存
     *  @access   public
     *  @return   void
     *  @date     2014-03-02
     */
    public function clearCache(){
    	if(file_exists(RUNTIME_PATH)){
    		if(delDir(RUNTIME_PATH)){
    			$admin = 1;
    		}else{
    			$admin = 0;
    		}
    	}else{
    		$admin = 1;
    	}
    	if(file_exists(DF_ROOT.'/App/Runtime/')){
    		if(delDir(DF_ROOT.'/App/Runtime/')){
    			$app = 1;
    		}else{
    			$app = 0;
    		}
    	}else{
    		$app = 1;
    	}
    	
    	
		if($admin == 1 && $app == 1){
			$this->success('清除缓存成功');
		}else{
			$this->error('清除缓存失败');
		}
    } 

}