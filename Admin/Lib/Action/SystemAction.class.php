<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     后台系统设置
 * @author   sunwanqing  
 +------------------------------------------------------------------------------
 */
class SystemAction extends CommonAction{
	
	/**
	 *   @desc     自定义导航
	 *   @access   function
	 *   @return   void
	 *   @date     2014-02-22
	 */
	public function nav(){
		$m = new Model('nav');
		
		$field = 'id,name,title,keyword,desc,opennew,is_show,sort_order,url';
		$nav = $m->field($field)->order('sort_order asc,id desc')->select();

		$action_link = array('href'=>'__APP__/System/editNav','text'=>'添加导航');
				
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '自定义导航栏');
		$this->assign('nav', $nav);
		
		$this->display();
	}
	
	/**
	 *   @desc    添加、编辑导航
	 *   @access  public
	 *   @return  void
	 *   @date    2014-02-22
     */
	public function editNav(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		
		$m = new Model('nav');
		if($id == 0){ //添加导航
			$nav['name'] = '';
			$nav['title'] = '';
			$nav['url'] = '';
			$nav['desc'] = '';
			$nav['keyword'] = '';
			$nav['sort_order'] = 20;
			$nav['opennew'] = 0;
			$nav['is_show'] = 1;
						
			$action = 'add';
		}else{ //编辑导航
			$field = "id,name,title,url,desc,keyword,sort_order,opennew,is_show";
			$nav = $m->find($id);
			
			$action = 'edit';
		}
		$action_link = array('href'=>'__APP__/System/nav','text'=>'返回列表');
		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '自定义导航栏');
		$this->assign('action', $action);
		$this->assign('nav', $nav);
		
		$this->display('navInfo');		
	}
	
	/**
	 *  @desc     删除导航
	 *  @access   public
	 *  @return   void
	 *  @date     2014-02-22
	 */
	function delNav(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('nav');
		$nav = $m->find($id);
		
		if(empty($nav)){
			echo 0;
			exit();
		}
		
		if($m->delete($id)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	/**
	 *   @desc    保存导航信息
	 *   @access  public
	 *   @return  void
	 *   @date    2014-02-22
	 */
	public function saveNav(){
		$action = isset($_REQUEST['action']) ?	trim($_REQUEST['action']) : '';		
		$m = new Model('nav');
		
		if($action == 'edit'){  //编辑导航
			$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
			if($data['id'] == 0){
				$this->error('保存失败');
				exit();
			}
				
			$data['name'] = trim($_REQUEST['name']);
			$data['url'] = trim($_REQUEST['url']);
			$data['title'] = trim($_REQUEST['title']);
			$data['keyword'] = trim($_REQUEST['keyword']);
			$data['namdesce'] = trim($_REQUEST['desc']);
			$data['sort_order'] = intval($_REQUEST['sort_order']);
			$data['opennew'] = intval($_REQUEST['opennew']);
			$data['is_show'] = intval($_REQUEST['is_show']);
			
			if($m->create($data)){
				if($m->save()){
					$this->success('保存成功', '__APP__/System/nav');
				}else{
					$this->error('保存失败');
				}
			}else{
				$this->error('保存失败');
			}
			
		}else{  //添加导航
			$data['name'] = isset($_REQUEST['name']) ? trim($_REQUEST['name']) : '';
			$data['url'] = isset($_REQUEST['url']) ? trim($_REQUEST['url']) : '';
			$data['title'] = isset($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
			$data['keyword'] = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
			$data['desc'] = isset($_REQUEST['desc']) ? trim($_REQUEST['desc']) : '';
			$data['sort_order'] = isset($_REQUEST['sort_order']) ? intval($_REQUEST['sort_order']) : 20;
			$data['opennew'] = isset($_REQUEST['opennew']) ? intval($_REQUEST['opennew']) : 0;
			$data['is_show'] = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 1;
			
			if($m->create($data)){
				if($m->add()){
					$this->success('添加成功','__APP__/System/nav');
				}else{
					$this->error('添加失败1');
				}
			}else{
				$this->error('添加失败2');
			}			
		}
		
	}
	
	/**
	 *  @desc    点击更改导航的状态
	 *  @access  public
	 *  @void    void
	 *  @date    20141-02-22
	 */
	function navStatusToggle(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		$field = isset($_REQUEST['field']) ? trim($_REQUEST['field']) : '';
		if($id == 0 || !$field){
			echo 0;
			exit();
		}
		
		$m = new Model('nav');
		$data['id'] = $id;
		$nav = $m->field($field)->find($id);
		
		if(!$nav){
			echo 0;
			exit();
		}
		
		if($nav[$field] == 0){
			$data[$field] = 1;
		}else{
			$data[$field] = 0;
		}
		
		if($m->create($data)){
			if($m->save()){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
	
	/**
	 *  @desc    导航列表
	 *  @access  public
	 *  @return  void
	 *  @data    2014-02-22 
	 */
	public function flink(){
		$m = new Model('friend_link');
		$field = 'id,link_name,link_logo,link_url,sort_order,is_show';
		$order = 'id desc';
		$count = $m->count();
	
		import('ORG.Util.Page');// 导入分页类
		//每页显示数量，从后台调取，此处先定为10条
		$size = 10;
		
		$page = new Page($count, $size);        //声明分页对象
		$page->setConfig('header','个友情链接');
		$page->setConfig('theme', "共计 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
		$page_list = $page->show();
		
		$flink = $m->field($field)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
		
		$action_link = array('href'=>'__APP__/System/flinkInfo','text'=>'添加链接');
		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '友情链接列表');
		$this->assign('flink', $flink);
		$this->assign('page_list', $page_list);	
		$this->display();		
	}
	
	/**
	 *   @desc    添加、编辑友情链接
	 *   @access  public
	 *   @return  void
	 *   @date    2014-2=02-
	 */
	function flinkInfo(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;		
		
		if($id == 0){  //添加友情链接
			$flink['name'] = '';
			$flink['link_url'] = '';
			$flink['link_logo'] = '';
			$flink['is_show'] = 1;
			$flink['sort_order'] = 20;			
			$action = 'add';
			$ur_here = '添加新链接';			
		}else{  //编辑友情链接
			$m = new Model('friend_link');
			$field = "id,link_name,link_url,link_logo,is_show,sort_order";
			$flink = $m->field($field)->find($id);			
			$action = 'edit';
			$ur_here = '编辑链接';
		}
		
		$action_link = array('href'=>'__APP__/System/flink','text'=>'友情链接列表');
		
		$this->assign('action', $action);
		$this->assign('ur_here', $ur_here);
		$this->assign('action_link', $action_link);
		$this->assign('flink', $flink);
		
		$this->display();
	}
	
	/**
	 *  @desc   保存友情链接
	 *  @access public
	 *  @return void
	 *  @date   2014-02-22
	 */
	public function saveFlink(){
		$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : '';
		if($action != 'add' && $action != 'edit'){
			$this->error('操作失败');
			exit();
		}
		
		if($action == 'edit'){  //编辑友情链接
			$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
			if($data['id'] == 0){
				$this->error('保存失败');
				exit();
			}
			$data['link_name'] = trim($_REQUEST['link_name']);
			$data['link_url'] = trim($_REQUEST['link_url']);
			$data['sort_order'] = intval($_REQUEST['sort_order']);
			$data['is_show'] = intval($_REQUEST['is_show']);			
		}else{  //添加友情链接
			$data['link_name'] = isset($_REQUEST['link_name']) ? trim($_REQUEST['link_name']) : '';
			$data['link_url'] = isset($_REQUEST['link_url']) ? trim($_REQUEST['link_url']) : '';
			$data['sort_order'] = isset($_REQUEST['sort_order']) ? intval($_REQUEST['sort_order']) : 0;
			$data['is_show'] = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : '';
		}
		
		//有图片上传 
		if($_FILES['link_logo']['error'] == 0){
			$imgDir = './Public/Uploads/';    //图片保存目录
			
			//图片目录不存在则创建
			if(!file_exists($imgDir)){
				createFolder($imgDir);
			}
			
			import("ORG.net.UploadFile");		
			$upload = new UploadFile();// 实例化上传类
			$upload->allowExts = $this->imgAllowExts;
			$upload->savePath = $imgDir;
			
			if(!$upload->upload()) {// 上传错误提示错误信息
				$this->error($upload->getErrorMsg());
			}else{// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				$res = $info[0];
			}		
		}
		
		$m = new Model('friend_link');
		if($action == 'edit'){ //编辑导航
			//有图片上传
			if($_FILES['link_logo']['error'] == 0){
				$data['link_logo'] = $imgDir.$res['savename'];
			}
			if($m->create($data)){
				if($m->save()){
					$this->success('保存成功', '__APP__/System/flink');
				}else{
					$this->error('添加失败');
				}
			}else{
				$this->error('添加失败');
			}			
		}else{  //添加导航
			$data['link_logo'] = $imgDir.$res['savename'];
			if($m->create($data)){
				if($m->add()){
					$this->success('添加成功', '__APP__/System/flink');
				}else{
					$this->error('添加失败');
				}
			}else{
				$this->error('添加失败');
			}
		}	
	}
	
	/**
	 *   @desc    友情链接显示或隐藏	
	 *   @access  public
	 *   @return  void
	 *   @date    2014-02-23
	 */
	function flinkStatusToggle(){
		$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($data['id'] == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('friend_link');
		$flink = $m->field('is_show')->find($data['id']); 
		
		if($flink['is_show'] == 1){
			$data['is_show'] = 0;
		}else{
			$data['is_show'] = 1;
		}
		
		if($m->create($data)){
			if($m->save()){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
	
	/**
	 *   @desc   删除友情链接 
	 *   @access public
	 *   @return void
	 *   @date   2014-02-23
	 */
	public function delFlink(){
		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = new Model('friend_link');
		$flink = $m->find($id);
		if(!$flink){
			echo 0;
			exit();
		}
		
		if($m->delete($id)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	/**
	 *   @desc    支付方式列表 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-19
	 */
     public function pay(){
        $m = M("payment");
        $field = 'id,pay_code,pay_fee,pay_name,pay_desc,sort_order,enabled';
        $pay = $m->field($field)->select();
        
        $action_link = array('href'=>'__APP__/System/editPay','text'=>'添加支付方式');			
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '支付方式');
		$this->assign('pay', $pay);		
		$this->display();
     }
     
     /**
	 *   @desc    添加或修改支付方式
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function editPay(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        
        if($id == 0){ //添加
            $pay['pay_name'] = '';
            $pay['sort_order'] = 20;
            $pay['enabled'] = 1;
            
            $action = 'add';
            $ur_here = '添加支付方式';
        }else{ //保存 
           $m = M('payment');
           $field = 'id,pay_code,pay_fee,pay_name,pay_desc,sort_order,pay_config,enabled';
           $pay = $m->field($field)->find($id);
           $action = 'edit';
           $ur_here = '编辑支付方式信息'; 
        }        

        $action_link = array('href'=>'__APP__/System/pay','text'=>'返回支付方式列表');		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', $ur_here);
        $this->assign('action', $action);
		$this->assign('pay', $pay);		
		$this->display();
     }
     
     
    /**
	 *   @desc    保存支付方式信息
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function savePay(){
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'add';
        
        $data['pay_name'] = trim($_REQUEST['pay_name']);
        $data['pay_desc'] = trim($_REQUEST['pay_desc']);
        $data['sort_order'] = trim($_REQUEST['sort_order']);
        $data['enabled'] = intval($_REQUEST['enabled']);
        $data['pay_fee'] = number_format((float)$_REQUEST['pay_fee'], 2);
        $data['pay_code'] = '';
        $data['pay_config'] = '';
        
        $m = M('payment');
        if($action == 'edit'){ //编辑支付方式信息
            $data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
            if($data['id'] == 0){
                $this->error('系统错误');
                exit();
            }
            if($m->create($data)){
                if($m->save()){
                    $this->success('保存成功', __APP__.'/System/pay');
                }else{
                    $this->error('保存失败');
                }
            }else{
                $this->error('保存失败');
            }
        }else{  //添加支付方式信息
            if($m->create($data)){
                if($m->add()){
                    $this->success('添加成功', __APP__.'/System/pay');
                }else{
                    $this->error('添加失败');
                }
            }else{
                $this->error('添加失败');
            }
        }
        
     }
     
     /**
	 *   @desc    删除支付方式
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function delPay(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        if($id == 0){
            echo '您要删除的支付方式id丢失';
            exit();
        }
        
        $m = M('payment');
        if($m->delete($id)){
            echo 'success';
        }else{
            echo '删除失败';
        }        
     }
	
    /**
	 *   @desc    修改支付方式启用状态 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
    public function payToggle(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
        if($id == 0){
            echo 0;
            exit();
        }
        
        $m = M('payment');
        $pay = $m->field('enabled')->find($id);
        if(!$pay){
            echo 2;  //没有查询到支付方式
            exit();
        }
        
        if($pay['enabled'] == 0){
            $data['enabled'] = 1;
        }else{
            $data['enabled'] = 0;
        }
        $data['id'] = $id;
        
        if($m->create($data)){
            if($m->save()){
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 4;
        }
    }
    
    /**
	 *   @desc    配送方式 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function shipping(){
        $m = M("shipping");
        $field = 'id,shipping_name,	enabled,sort_order,shipping_fee';
        $order = 'id desc';
        $shipping = $m->field($field)->order($order)->select();
        $action_link = array('href'=>'__APP__/System/editShipping','text'=>'添加配送方式');			
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '配送方式');
		$this->assign('shipping', $shipping);		
		$this->display();        
     }
     
     /**
	 *   @desc    添加或修改配送方式
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function editShipping(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        
        if($id == 0){ //添加
            $shipping['shipping_name'] = '';
            $shipping['sort_order'] = 5;
            $shipping['enabled'] = 1;            
            $action = 'add';
            $ur_here = '添加配送方式';
        }else{ //保存 
           $m = M('shipping');
           $field = 'id,shipping_fee,shipping_name,shipping_desc,sort_order,enabled';
           $shipping = $m->field($field)->find($id);
           $action = 'edit';
           $ur_here = '编辑配送方式信息'; 
        }        

        $action_link = array('href'=>'__APP__/System/shipping','text'=>'返回配送方式列表');		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', $ur_here);
        $this->assign('action', $action);
		$this->assign('shipping', $shipping);		
		$this->display();
     }
     
     /**
	 *   @desc    保存配送方式信息
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function saveShipping(){
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'add';
        
        $data['shipping_name'] = trim($_REQUEST['shipping_name']);
        $data['shipping_desc'] = trim($_REQUEST['shipping_desc']);
        $data['sort_order'] = trim($_REQUEST['sort_order']);
        $data['enabled'] = intval($_REQUEST['enabled']);
        $data['shipping_fee'] = number_format((float)$_REQUEST['shipping_fee'], 2);
        
        $m = M('shipping');
        if($action == 'edit'){ //编辑支付方式信息
            $data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
            if($data['id'] == 0){
                $this->error('系统错误');
                exit();
            }
            if($m->create($data)){
                if($m->save()){
                    $this->success('保存成功', __APP__.'/System/shipping');
                }else{
                    $this->error('保存失败');
                }
            }else{
                $this->error('保存失败');
            }
        }else{  //添加支付方式信息
            if($m->create($data)){
                if($m->add()){
                    $this->success('添加成功', __APP__.'/System/shipping');
                }else{
                    $this->error('添加失败');
                }
            }else{
                $this->error('添加失败');
            }
        }
        
     }
     
     /**
	 *   @desc    修改配送方式启用状态 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
    public function shippingToggle(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
        if($id == 0){
            echo 0;
            exit();
        }
        
        $m = M('shipping');
        $pay = $m->field('enabled')->find($id);
        if(!$pay){
            echo 2;  //没有查询到支付方式
            exit();
        }
        
        if($pay['enabled'] == 0){
            $data['enabled'] = 1;
        }else{
            $data['enabled'] = 0;
        }
        $data['id'] = $id;
        
        if($m->create($data)){
            if($m->save()){
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 4;
        }
    }
    
    /**
	 *   @desc    删除配送方式
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function delshipping(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        if($id == 0){
            echo '您要删除的配送方式id丢失';
            exit();
        }
        
        $m = M('shipping');
        if($m->delete($id)){
            echo 'success';
        }else{
            echo '删除失败';
        }        
     }
    
	/**
	 *   @desc    首页banner 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function banner(){
        $m = M("banner");
        $field = 'id,banner_name,banner_url,is_show,sort_order';
        $order = 'id desc';
        $banner = $m->field($field)->order($order)->select();
        $action_link = array('href'=>'__APP__/System/editBanner','text'=>'添加Banner图片');		

		$this->assign('action_link', $action_link);
		$this->assign('ur_here', '首页Banner图片');
		$this->assign('banner', $banner);		
		$this->display(); 
     }
	
	/**
	 *   @desc    编辑或添加首页banner图片 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
     public function editBanner(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
        
        if($id == 0){ //添加
            $banner['banner_name'] = '';
            $banner['sort_order'] = 10;
            $banner['is_show'] = 1;            
            $action = 'add';
            $ur_here = '添加首页Banner图片';
        }else{ //保存 
           $m = M('banner');
           $field = 'id,banner_name,banner_url,is_show,sort_order';
           $banner = $m->field($field)->find($id);
           $action = 'edit';
           $ur_here = '编辑首页Banner图片信息'; 
        }        
        
        $action_link = array('href'=>'__APP__/System/banner','text'=>'返回首页Banner图片列表');		
		$this->assign('action_link', $action_link);
		$this->assign('ur_here', $ur_here);
        $this->assign('action', $action);
		$this->assign('banner', $banner);		
		$this->display();
     }
	
    /**
	 *   @desc    保存首页banner图片 
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
    public function saveBanner(){
        $action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'add'; 
       
        if($action == 'edit'){   //编辑banner图
            $data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
			if($data['id'] == 0){
				$this->error('保存失败');
				exit();
			}
        }else{
            if(!$_FILES['banner']['name']){  //添加时没有上传图片
                $this->error('未选择图片');
                exit();
            }  
        }
        
        $data['banner_name'] = trim($_REQUEST['banner_name']);
        $data['is_show'] = intval($_REQUEST['is_show']);
        $data['sort_order'] = intval($_REQUEST['sort_order']);
        
        //上传图片
        if($_FILES['banner']['name']){
            if($_FILES['banner']['error'] == 0){
                $imgDir = './Public/Uploads/';    //图片保存目录
                //图片目录不存在则创建
		    	if(!file_exists($imgDir)){
			     	createFolder($imgDir);
		    	}
                
                import("ORG.net.UploadFile");		
    			$upload = new UploadFile();// 实例化上传类
    			$upload->allowExts = $this->imgAllowExts;
    			$upload->savePath = $imgDir;
                
                if(!$upload->upload()) {// 上传错误提示错误信息
    				$this->error($upload->getErrorMsg());
    			}else{// 上传成功 获取上传文件信息
    				$info =  $upload->getUploadFileInfo();
    				$res = $info[0];
    			} 
                $data['banner_url'] = $imgDir.$res['savename'];               
            }else{
                $this->error('上传图片时出错');
                exit();
            }
        }
        
        $m = M('banner');
        if($action == 'edit'){
            if($m->create($data)){
                if($m->save()){
                    $this->success('保存成功', __APP__.'/System/banner');
                }else{
                    $this->error('保存失败');
                }
            }else{
                $this->error('保存失败');
            }
        }else{
            if($m->create($data)){
                if($m->add()){
                    $this->success('添加成功', __APP__.'/System/banner');
                }else{
                    $this->error('添加失败');
                }
            }else{
                $this->error('添加失败');
            }
        }
    }
    
    /**
	 *   @desc    修改banner图片显示状态
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
    public function bannerStatusToggle(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
        if($id == 0){
            echo 0;
            exit();
        }
        
        $m = M('banner');
        $pay = $m->field('is_show')->find($id);
        if(!$pay){
            echo 2;  //没有查询到支付方式
            exit();
        }
        
        if($pay['is_show'] == 0){
            $data['is_show'] = 1;
        }else{
            $data['is_show'] = 0;
        }
        $data['id'] = $id;
        
        if($m->create($data)){
            if($m->save()){
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 4;
        }
    }
    
     /**
	 *   @desc    删除修改banner图片
	 *   @access  public
	 *   @return  void
	 *   @date    2014-04-20
	 */
    public function delBanner(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
		if($id == 0){
			echo 0;
			exit();
		}
		
		$m = M('banner');
		$banner = $m->find($id);
		if(!$banner){
			echo 0;
			exit();
		}
		
		if($m->delete($id)){
			echo 1;
		}else{
			echo 0;
		}
    }
    
    /**
	 *   @desc     热门搜索
	 *   @access   public
	 *   @return   void
	 *   @date     2014-05-07
	 */
    public function hotSearch(){
       import('ORG.Util.Page');// 导入分页类
	   $size = 10;    //每页显示条数
       
       $m = M('search');
       $field = 'id,keyword,count,is_show';
       $order = 'count desc';
       $count = $m->field($field)->count();
      
       $page = new Page($count, $size);   //声明分页对象
	   $page->setConfig('header','个热门搜索关键字');
	   $page->setConfig('theme', "共有 %totalRow% %header%&nbsp;&nbsp;&nbsp;%nowPage%/%totalPage% 页   %first% %prePage% %linkPage% %nextPage% %end%");
       
       $page_list = $page->show();
       $search = $m->field($field)->order($order)->limit($page->firstRow.','.$page->listRows)->select();
              
       $action_link = array('href'=>'__APP__/System/editHostSearch','text'=>'添加热门搜索关键字');
       
       $this->assign('page_list', $page_list);
       $this->assign('search', $search);
       $this->assign('ur_here', '热门搜索关键字');
       $this->assign('action_link', $action_link);
       $this->display();
    }
    
    /**
     *   @desc    编辑或添加热门搜索关键字显示状态
     *   @access  public
     *   @return  void
     *   @date    2014-05-07
     */
    public function editHostSearch(){
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	
    	if($id == 0){
    		$search['keyword'] = '';
    		$search['is_show'] = 1;
    		$search['count'] = 0;
    		$action = 'add';
    	}else{
    		$m = m('search');
    		$field = 'id,keyword,count,is_show';
    		$search = $m->field($field)->find($id);
    		$action = 'edit';
    	}
    	
    	$action_link = array('href'=>'__APP__/System/hotSearch','text'=>'返回热门搜索关键字列表');
    	
    	$this->assign('search', $search);
    	$this->assign('action', $action);
    	$this->assign('action_link', $action_link);
    	$this->display();
    }
    
    /**
     *   @desc    保存热门搜索关键字信息 
     *   @access  public
     *   @return  void
     *   @date    2014-05-07
     */
    public function saveHotSearch(){
    	$action = isset($_REQUEST['action']) ? trim($_REQUEST['action']) : 'add';
    	
    	$data['keyword'] = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    	$data['count'] = isset($_REQUEST['count']) ? intval($_REQUEST['count']) : 1;
    	$data['is_show'] = isset($_REQUEST['is_show']) ? intval($_REQUEST['is_show']) : 1;
    	
   		$m = M('search');
    	if($action == 'edit'){
    		$data['id'] = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    		if($m->create($data)){
    			if($m->save()){
    				$this->success('保存成功', __APP__.'/System/hotSearch');
    			}else{
    				$this->error('保存失败');
    			}
    		}else{
    			$this->error('保存失败');
    		}
    	}else{
    		if($m->create($data)){
    			if($m->add()){
    				$this->success('添加成功', __APP__.'/System/hotSearch');
    			}else{
    				$this->error('添加失败');
    			}
    		}else{
    			$this->error('添加失败');
    		}
    	}    	
    }

    /**
     *   @desc    删除热门搜索关键字
     *   @access  public
     *   @return  void
     *   @date    2014-05-07
     */
    public function delHotSearch(){
    	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    	if($id == 0){
    		echo '您要删除的热门搜索关键字id丢失';
    		exit();
    	}
    
    	$m = M('search');
    	if($m->delete($id)){
    		echo 'success';
    	}else{
    		echo '删除失败';
    	}
    }
    
    /**
	 *   @desc    修改热门搜索关键字显示状态
	 *   @access  public
	 *   @return  void
	 *   @date    2014-05-07
	 */
    public function hotSearchToggle(){
        $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
        if($id == 0){
            echo 0;
            exit();
        }

        $m = M('search');
        $search = $m->field('is_show')->find($id);
        if(!$search){
            echo 2;  //关键字没有搜索到
            exit();
        }
        
        if($search['is_show'] == 0){
            $data['is_show'] = 1;
        }else{
            $data['is_show'] = 0;
        }
        $data['id'] = $id;
        
        if($m->create($data)){
            if($m->save()){
                echo 1;
            }else{
                echo 3;
            }
        }else{
            echo 4;
        }
    }
    
    /**
     *   @desc    修改管理员密码
     *   @access  public
     *   @return  void
     *   @date    2014-05-09
     */
    public function editPassword(){
    	$this->assign('ur_here', '修改密码');
    	$this->display();
    }
    
    /**
     *   @desc    保存管理员密码
     *   @access  public
     *   @return  void
     *   @date    2014-05-09
     */
    public function savePassword(){
    	$password = isset($_REQUEST['password']) ? strtolower(trim($_REQUEST['password'])) : '';
    	$newPassword = isset($_REQUEST['newPassword']) ? strtolower(trim($_REQUEST['newPassword'])) : '';
    	$reNewPassword = isset($_REQUEST['reNewPassword']) ? strtolower(trim($_REQUEST['reNewPassword'])) : '';
    	
    	if(!$password){
    		$this->error('原密码不能为空');
    		exit();
    	}
    	if(!$newPassword){
    		$this->error('新密码不能为空');
    		exit();
    	}elseif(strlen($newPassword)<6){
    		$this->error('密码长度不能小于6位');
    		exit();
    	}elseif(!preg_match("/^[0-9a-zA-Z]+$/",$newPassword)){
    		$this->error('密码只能为字母或数字');
    		exit();
    	}
    	if(!$reNewPassword){
    		$this->error('确认不能为空');
    		exit();
    	}elseif($reNewPassword && $reNewPassword != $newPassword){
    		$this->error('两次密码不一致');
    		exit();
    	}
    	$m = M('admin_user');
    	$field = 'password,salt';
    	$admin = $m->field($field)->find($_SESSION['admin']['id']);
    	
    	if(md5(md5($password).$admin['salt']) != $admin['password']){
    		$this->error('原密码错误');
    		exit();
    	}
   
    	$data['id'] = $_SESSION['admin']['id'];
    	$data['salt'] = rand(1000, 9999);
    	$data['password'] = md5(md5($newPassword).$data['salt']);
    	
    	if($m->create($data)){
    		if($m->save()){
    			$this->success('保存成功');
    		}else{
    			$this->error('保存失败');
    		}
    	}else{
    		$this->error('保存失败');
    	}
    }
}