<?php
defined('DF_ROOT') or die('Hacking attempt');

/**
 +------------------------------------------------------------------------------
 * @desc     后台管理员登录、退出、权限等管理
 * @author   sunwanqing
 +------------------------------------------------------------------------------
 */

class PrivilegeAction extends Action{
	protected $host = '';
	
	public function _initialize(){
		$this->host =  'http://'.$_SERVER['SERVER_NAME'].'/';
		$this->assign('host', $this->host);
	}
	
	
	/**
	 *  @desc   管理员登录
	 *  @access public
	 *  @return void
     *  @date   2014-05-06
	 */
	function login(){
		
		//sunwq
		//sunwq123
        
        //echo time().'<br>';
        
 		//$password = 'sunwq123';
 		//$salt = rand(1000, 9999);
 		//echo $password.'<br>';
 		//echo $salt.'<br>';
 		//echo md5($password).'<br>';
 		//echo md5(md5($password).$salt);
	
	   	$this->display();
	}
	
	/**
	 *  @desc   管理员登录处理
	 *  @access public
	 *  @return void
	 *  @date   2014-05-06
	 */
	public function loginAct(){
		$username = isset($_REQUEST['username']) ? strtolower(trim($_REQUEST['username'])) : '';
        $password = isset($_REQUEST['password']) ? strtolower(trim($_REQUEST['password'])) : '';
        
        if(!$username){
            echo 1;   //用户名为空
            exit();
        }
        if(!$password){
            echo 2;   //密码为空 
            exit();
        }
        
        $m = M('admin_user');
        $field = 'id,user_name,password,salt,last_login_time,last_login_ip';    
        $where = array('user_name'=>$username);
        $admin = $m->field($field)->where($where)->find();
        if(!$admin){
            echo 3;   //帐号不存在 
            exit();
        }
        
        if($admin['password'] != md5(md5($password).$admin['salt'])){
            echo 4;   //用户名或密码错误
            exit();
        }
        
        $_SESSION['admin'] = $admin;
      
        //更新用户信息
        $data['id'] = $admin['id'];
        $data['salt'] = rand(1000, 9999);
        $data['password'] = md5(md5($password).$data['salt']);
        $data['last_login_time'] = time();
        $data['last_login_ip'] = get_client_ip();
        if($m->save($data)){
            echo 0;
        }else{
            echo 5;
        }        
	}
	
	/**
	*  管理员退出
	*  @access public
	*  @return void
    *  @param  2014-05-06
	*/
	function logout(){
	   unset($_SESSION['admin']);
       echo 1;
	}
	
	/**
	 * @desc     后台登录验证码
	 * @access   public
	 * @return   void
	 * @date     2014-05-06
	 */
	function verify(){
		$length = $_REQUEST['length'] ? $_REQUEST['length'] : 4;  //默认为四位
		$model = $_REQUEST['model'] ?  $_REQUEST['model'] : 1; //默认为数字        0 字母 1 数字 2 大写字母 3 小写字母 4中文 5混合
		$width = $_REQUEST['width'] ? $_REQUEST['width'] : 60 ;
		$height = $_REQUEST['height'] ? $_REQUEST['height'] : 20 ;
	
		import('ORG.Util.Image');
		Image::buildImageVerify($length, $model,'jpeg', $width, $height);
	}
	
}