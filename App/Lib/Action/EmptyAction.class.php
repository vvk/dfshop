<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');

/**
 +------------------------------------------------------------------------------
 * @desc      空操作
 * @author    sunwanqing
 +------------------------------------------------------------------------------
 */
class EmptyAction extends commonAction {
	/**
	 *   @desc    空操作
	 *   @access  public
	 *   @return  void
	 *   @date    2014-05-05
	 */
	public function index(){
		header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码 
        $this->display("Public:404"); 
	}
}