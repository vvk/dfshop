<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     后台会员管理
 * @author   sunwanqing  
 +------------------------------------------------------------------------------
 */
class UserAction extends CommonAction{
	
	/*
	 *   @desc     会员列表
	 *   @access   public
	 *   @return   void
	 *   @date     2014-02-23
	 */
	public function index(){
		import('ORG.Util.Page');// 导入分页类
		//每页显示数量，从后台调取，此处先定为10条
		$size = 10;
		
		$m = new Model("users");
		$field = "id,user_name,password,email,sex,birthday,question_id,answer,reg_time,reg_ip,last_login_time,";
		$field .= "last_login_ip,visit_count,salt,qq,phone,photo,integral,is_approve,province,city,district,enabled";
		$order = "last_login_time desc,id desc";
		$count = $m->count();
		
		$page = new Page($count, $size);        //声明分页对象
		$page->setConfig('header','个会员');
		$page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
		
		$users = $m->field($field)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
		
		foreach($users as $key=>$val){
			$users[$key]['last_login_time'] = date("Y-m-d H:i:s", $val['last_login_time']);
			$users[$key]['reg_time'] = date("Y-m-d H:i:s", $val['reg_time']);
			if($val['sex'] == 1){
				$users[$key]['sex'] = '男';
			}elseif($val['sex'] == 2){
				$users[$key]['sex'] = '女';
			}else{
				$users[$key]['sex'] = '保密';
			}
		}
		
		$action_link = array('href'=>'__APP__/User/userInfo','text'=>'添加会员');
		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '友情链接列表');
		$this->assign('users', $users);
		$this->assign('page_list', $page_list);
		$this->assign('ur_here', '会员列表');
		
		$this->display();
	}
	
	/*
	 *  @desc    添加会员
	 *  @access  public
	 *  @return  void 
	 *  @date    2014-02-23	 * 
	 */
	public function userInfo(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		if($id == 0){ //添加会员 
			$user['user_name'] = '';
			$user['email'] = '';
			$user['password'] = '';
			$user['sex'] = 0;
			$user['year'] = date('Y', time());
			$user['month'] = date('m', time());
			$user['day'] = date('d', time());
			$user['qq'] = '';
			$user['phone'] = '';
			$user['integral'] = 0;
			$user['enabled'] = 0;

			$action = 'add';
			$ur_here = '添加会员账号';
		}else{ //编辑会员
			$m = new Model('users');
			
			$field = "id,user_name,password,email,sex,birthday,question_id,answer,reg_time,reg_ip,last_login_time,";
			$field .= "last_login_ip,visit_count,salt,qq,phone,photo,integral,is_approve,province,city,district,enabled";
			$user = $m->field($field)->find($id);			
			
			$action = 'edit';
			$ur_here = '编辑会员账号';
			
			$birthday = $user['birthday'];
			$arr = explode('-', $birthday);
			$user['year'] = $arr[0];
			$user['month'] = $arr[1];
			$user['day'] = $arr[2];
		}		
		
		$year = $month = $day = '';
		for($i=$user['year']-50; $i<=$user['year']; $i++){
			if($i == $user['year']){
				$year .= "<option value='".$i."' selected>".$i."</option>";
			}else{
				$year .= "<option value='".$i."'>".$i."</option>";
			}			
		}
		for($i=1;$i<=12;$i++){
			if($i == $user['month']){
				$month .= "<option value='".$i."' selected>".$i."</option>";
			}else{
				$month .= "<option value='".$i."'>".$i."</option>";
			}
		}
		for($i=1;$i<=date('t', time());$i++){
			if($i == $user['day']){
				$day .= "<option value='".$i."' selected>".$i."</option>";
			}else{
				$day .= "<option value='".$i."'>".$i."</option>";
			}
		}
        		
		$action_link = array('href'=>'__APP__/User/index','text'=>'会员列表');
		
		$this->assign('year', $year);
		$this->assign('month', $month);
		$this->assign('day', $day);
		$this->assign('action', $action);
		$this->assign('ur_here', $ur_here);
		$this->assign('action_link', $action_link);
		$this->assign('user', $user);		
		$this->display();
	}
	
	/**
	*  @desc    启用或禁用会员
	*  @access  public
	*  @return  void
	*  @date    2014-05-10
	*/
	public function enabledUser(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;  //要修改商品的id
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('users');
		$user = $m->find($id);
		
		if(empty($user)){ //用户不存在
			echo 1;
			exit();
		}else{
			$state = $user['enabled'];
			if($state == 1){
				$enabled = 0;
			}else{
				$enabled = 1;
			}
			$data['id'] = $id;
			$data['enabled'] = $enabled;
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
	 *  @desc    删除会员
	 *  @access  public
	 *  @return  void
	 *  @date    2014-02-23
	 */
	public function delUser(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('users');
		$user = $m->find($id);
		
		//要删除的会员不存在，则不用删除
		if(!$user){
			echo 1;
			exit();
		}
		
		if($m->delete($id)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	/*
	 *   @desc    保存会员信息
	 *   @access  public
	 *   @return  void
	 *   @date    2014-02-23
	 */
	public function saveUser(){
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
		if($action != 'edit' && $action != 'add'){
			$this->error('操作失败!');
			exit();
		}
		
		$m = new Model('users');
		if($action == 'edit'){  //修改会员信息
			$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
			if($data['id'] == 0){
				$this->error('保存失败');
				exit();
			}
			$data['user_name'] = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
			if($_REQUEST['password']){
				$data['password'] = trim($_REQUEST['password']);
			}
			if($_REQUEST['repassword']){
				$repassword = trim($_REQUEST['repassword']);
			}
			$year = isset($_REQUEST['year']) ? intval($_REQUEST['year']) : date("Y", time());
			$month = isset($_REQUEST['month']) ? intval($_REQUEST['month']) : date("m", time());
			$day = isset($_REQUEST['day']) ? intval($_REQUEST['day']) : date("d", time());
			$data['sex'] = isset($_REQUEST['sex']) ? intval($_REQUEST['sex']) : 0;
			$data['qq'] = isset($_REQUEST['qq']) ? trim($_REQUEST['qq']) : '';
			$data['phone'] = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';
			$data['email'] = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
			$data['enabled'] = isset($_REQUEST['enabled']) ? intval($_REQUEST['enabled']) : 0;
			
			if(empty($data['user_name'])){
				$this->error('用户名不能为空');
				exit();
			}
			
			if(!empty($data['password'])){
				if(empty($repassword)){
					$this->error('确认密码不能为空');
					exit();
				}
				if($data['password'] != $repassword){
					$this->error('两次输入的密码不一致');
					exit();
				}
			}
			if(empty($data['email'])){
				$this->error('邮箱不能为空');
				exit();
			}

			if(strlen($month)<2){
				$month = "0".$month;
			}

			if(strlen($day)<2){
				$day = "0".$day;
			}
			
			$data['birthday'] = $year.'-'.$month.'-'.$day;
			echo $data['password'];
			if(!empty($data['password'])){
				$data['salt'] = rand(1000, 9999);
				$data['password'] = md5(md5($data['password']).$data['salt']);
			}

			if($m->create($data)){
				if($m->save()){
					$this->success("保存成功", '__APP__/User/index');
				}else{
					echo $m->getDbError();
					exit();
					$this->error("保存失败");
				}
			}else{
				echo $m->getDbError();
				exit();
				$this->error("保存失败");
			}			
		}else{  //添加会员
			$data['user_name'] = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
			$data['password'] = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
			$repassword = isset($_REQUEST['repassword']) ? trim($_REQUEST['repassword']) : '';
			$data['email'] = isset($_REQUEST['email']) ? trim($_REQUEST['email']) : '';
			$data['qq'] = isset($_REQUEST['qq']) ? trim($_REQUEST['qq']) : '';
			$year = isset($_REQUEST['year']) ? trim($_REQUEST['year']) : '0000';
			$data['phone'] = isset($_REQUEST['phone']) ? trim($_REQUEST['phone']) : '';
			$month = isset($_REQUEST['month']) ? trim($_REQUEST['month']) : '00';
			$day = isset($_REQUEST['day']) ? trim($_REQUEST['day']) : '00';
			
			if(!$data['user_name']){
				$this->error('用户名不能为空');
				exit();
			}
			if(!$data['password']){
				$this->error('密码不能为空');
				exit();
			}
			if(!$repassword){
				$this->error('重复不能为空');
				exit();
			}
			if(!$data['password'] && $repassword && ($data['password'] != $repassword)){
				$this->error('两次输入的密码不一致!');
				exit();
			}
			if(strlen($month)<2){
				$month ='0'.$month;
			}
			if(strlen($day)<2){
				$day ='0'.$day;
			}
			$data['birthday'] = $year.'-'.$month.'-'.$day;
			$data['salt'] = rand(1000,9999);
			$data['password'] = md5(md5($data['password']).$data['salt']);
			$data['reg_time'] = time();
			$data['answer'] = '';
			$data['last_login_time'] = '';
			$data['last_login_ip'] = '';
			$data['reg_ip'] = get_client_ip();
			$data['photo'] = '';
			
			$user = $m->field('email')->where(array('email'=>$data['email']))->select();
			if(count($user[0])>0){
				$this->error('该邮箱已被注册');
				exit();
			}
  			if($m->create($data)){
				if($m->add()){
					$this->success('添加成功', '__APP__/User/index');
				}else{
					echo $m->getError();
					echo $m->getDbError();
					exit();
					$this->error('添加失败 	');
				}
				$this->assign('添加失败');
			}
		}	

	}
	
	
}