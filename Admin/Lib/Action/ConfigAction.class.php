<?php
defined('DF_ROOT') or die('Hacking attempt');
import('@.Action.CommonAction');
/**
 +------------------------------------------------------------------------------
 * @desc     商店设置
 * @author   sunwanqing  
 +------------------------------------------------------------------------------
 */

 class ConfigAction extends CommonAction{
 	
 	/*
 	*  @desc    商店设置
 	*  @access  public
 	*  @return  void	
 	*  @date    2014-02-03 
 	*/
 	public function index(){
 		$m = new Model('region');
 		$where = array('parent_id'=>1);
 		$province = $m->where($where)->select();

 		$where = array('parent_id'=>$this->_CFG['shop_province']['value']);
 		$city = $m->where($where)->select();
 		
 		$where = array('parent_id'=>$this->_CFG['shop_city']['value']);
 		$district = $m->where($where)->select();

 		$this->assign('province', $province);           //商店所在省 
 		$this->assign('city', $city);           //商店所在省 
 		$this->assign('district', $district);           //商店所在省 
 		$this->assign('config', $this->_CFG);           //配置信息
 		$this->assign('ur_here', '商店设置');              //当前页标题
 		$this->display();
 	}
 	
 	/*
 	 * @desc   地区更改
 	*  @access public
 	*  @return void
 	*  @date   2014-02-03
 	*/
 	public function regionChange(){
 		$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
 		$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
 		if($id == 0 || empty($type)){
 			exit();
 		}
 			
 		$m = new Model('region');
 		$region = $temp = array();
 		$where = array('parent_id'=>$id);
 		$temp = $m->where($where)->select();
 		if($type == 'province'){
 			$where = array('parent_id'=>$temp[0]['id']);
 			$region['district'] = $m->where($where)->select();
 			$region['city'] = $temp;
 		}else{
 			$region = $temp;
 		}
 			
 		echo json_encode($region);
 		exit();
 	}
 
 	/*
 	 *   @desc   删除商店设置中的图片
 	 *   @access public 
 	 *   @return void
 	 *   2014-02-02
 	 */
 	
 	public function delImg(){
 		$code = isset($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
 		
 		if(!$code){
 			$this->error('删除失败');
 			exit();
 		}
 		
 		$m = new Model('config');
 		$where = array('code'=>$code);
 		$result = $m->field('value')->where($where)->select();
 		
 		//数据或图片不存在
 		if((count($result) <= 0) || !$result[0]['value']){
 			$this->error('删除失败');
 			exit();
 		}
 		$pic = $result[0]['value'];
 		$dir = "./Public/Uploads/";
 		
 		if(file_exists($dir.$pic)){
 			if(!unlink($dir.$pic)){
 				$this->error('删除失败');
 				exit();
 			}
 		}
		
 		$sql = "update df_config set value = '' where code='".$code."'";
		if($m->execute($sql)){
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
 		
 	}
 	
 	
 	/*
 	 * @desc   保存配置
 	 * @access public
 	 * @return void
 	 * 2014-02-05
 	 */
 	public function saveConfig(){
 		vendor('My.image');   //图片上传类
 	
 		$dir = './Public/Uploads/';
 		$image = new image();    //实例化上传类

 		/* 网店信息 */
 		$data['shop_name'] = $_POST['shop_name'];
 		$data['shop_title'] = $_POST['shop_title'];
 		$data['shop_desc'] = $_POST['shop_desc'];
 		$data['shop_keywords'] = $_POST['shop_keywords'];
 		$data['shop_province'] = $_POST['shop_province'];
 		$data['shop_city'] = $_POST['shop_city'];
 		$data['shop_district'] = $_POST['shop_district'];
 		$data['shop_address'] = $_POST['shop_address'];
 		$data['ww'] = $_POST['ww'];
 		$data['qq'] = $_POST['qq'];
 		$data['skype'] = $_POST['skype'];
 		$data['ym'] = $_POST['ym'];
 		$data['msn'] = $_POST['msn'];
 		$data['service_email'] = $_POST['service_email'];
 		$data['service_phone'] = $_POST['service_phone'];
 		$data['shop_closed'] = $_POST['shop_closed'];
 		$data['close_comment'] = $_POST['close_comment'];
 		$shop_logo = $_FILES['shop_logo'] ;
 		if($shop_logo['error'] == 0){
 			$type = $shop_logo['type'];
 			$tmp = explode('/', $type);
 			$ext = $tmp[count($tmp)-1];
 			$data['shop_logo'] = 'shop_logo.'.$ext;
 				
 			if(!$image->upload_image($shop_logo, $dir, $data['shop_logo'])){
 				$this->error($image->error_msg());
 				exit();
 			}
 		}		
 		$data['licensed'] = $_POST['licensed']; 		
 		$data['user_notice'] = $_POST['user_notice'];
 		$data['shop_notice'] = trim($_POST['shop_notice']);
 		$data['shop_reg_closed'] = $_POST['shop_reg_closed'];
 		/* 网店信息 */
 		
 		/* 基本信息 */
 		$data['lang'] = $_POST['lang'];
 		$data['icp_number'] = $_POST['icp_number'];
 		
 		$icp_file = $_FILES['icp_file'] ;      //ICP 备案证书文件
 		if($icp_file['error'] == 0){
 			$type = $icp_file['type'];
 			$tmp = explode('/', $type);
 			$ext = $tmp[count($tmp)-1];
 			$data['icp_file'] = 'icp_file.'.$ext;
 				
 			if(!$image->upload_image($icp_file, $dir, $data['icp_file'])){
 				$this->error($image->error_msg());
 				exit();
 			}
 		}
 		
 		$watermark = $_FILES['watermark'] ;      //水印文件
 		if($watermark['error'] == 0){
 			$type = $watermark['type'];
 			$tmp = explode('/', $type);
 			$ext = $tmp[count($tmp)-1];
 			$data['watermark'] = 'watermark.'.$ext;
 				
 			if(!$image->upload_image($watermark, $dir, $data['watermark'])){
 				$this->error($image->error_msg());
 				exit();
 			}
 		}
 		
 		$data['watermark_place'] = $_POST['watermark_place'];
 		$data['watermark_alpha'] = $_POST['watermark_alpha'];
 		$data['use_storage'] = $_POST['use_storage'];
 		$data['market_price_rate'] = $_POST['market_price_rate'];
 		$data['rewrite'] = $_POST['rewrite'];
 		$data['integral_name'] = $_POST['integral_name'];
 		$data['integral_scale'] = $_POST['integral_scale'];
 		$data['integral_percent'] = $_POST['integral_percent'];
 		$data['sn_prefix'] = $_POST['sn_prefix'];
 		$data['comment_check'] = $_POST['comment_check'];
 		$data['comment_factor'] = $_POST['comment_factor'];
 		$data['enable_order_check'] = $_POST['enable_order_check'];
 		$data['default_storage'] = $_POST['default_storage'];
 		$data['bgcolor'] = $_POST['bgcolor'];
 		$data['visit_stats'] = $_POST['visit_stats'];
 		$data['send_mail_on'] = $_POST['send_mail_on'];
 		$data['member_email_validate'] = $_POST['member_email_validate'];
 		$data['message_board'] = $_POST['message_board'];
 		$data['certi'] = $_POST['certi'];
 		$data['message_check'] = $_POST['message_check'];

 		$no_picture = $_FILES['no_picture'] ;      //水印文件
 		if($no_picture['error'] == 0){
 			$type = $no_picture['type'];
 			$tmp = explode('/', $type);
 			$ext = $tmp[count($tmp)-1];
 			$data['no_picture'] = 'no_picture.'.$ext;
 				
 			if(!$image->upload_image($no_picture, $dir, $data['no_picture'])){
 				$this->error($image->error_msg());
 				exit();
 			}
 		}
 		
 		$data['stats_code'] = $_POST['stats_code'];
 		$data['cache_time'] = $_POST['cache_time'];
 		$data['register_points'] = $_POST['register_points'];
 		$data['enable_gzip'] = $_POST['enable_gzip'];
 		$data['top10_time'] = $_POST['top10_time'];
 		$data['timezone'] = $_POST['timezone'];
 		$data['upload_size_limit'] = $_POST['upload_size_limit'];
 		$data['cron_method'] = $_POST['cron_method'];
 		$data['auto_generate_gallery'] = $_POST['auto_generate_gallery'];
 		$data['retain_original_img'] = $_POST['retain_original_img'];
 		/*  基本信息   */
		
 		/* 显示设置 */
 		$data['search_keywords'] = $_POST['search_keywords'];
 		$data['time_format'] = $_POST['time_format'];
 		$data['currency_format'] = $_POST['currency_format'];
 		$data['thumb_width'] = $_POST['thumb_width'];
 		$data['thumb_height'] = $_POST['thumb_height'];
 		$data['image_width'] = $_POST['image_width'];
 		$data['image_height'] = $_POST['image_height'];
 		$data['top_number'] = $_POST['top_number'];
 		$data['history_number'] = $_POST['history_number'];
 		$data['comments_number'] = $_POST['comments_number'];
 		$data['bought_goods'] = $_POST['bought_goods'];
 		$data['article_number'] = $_POST['article_number'];
 		$data['goods_name_length'] = $_POST['goods_name_length'];
 		$data['price_format'] = $_POST['price_format'];
 		$data['page_size'] = $_POST['page_size'];
 		$data['sort_order_type'] = $_POST['sort_order_type'];
 		$data['sort_order_method'] = $_POST['sort_order_method'];
 		$data['show_order_type'] = $_POST['show_order_type'];
 		$data['attr_related_number'] = $_POST['attr_related_number'];
 		$data['goods_gallery_number'] = $_POST['goods_gallery_number'];
 		$data['article_title_length'] = $_POST['article_title_length'];
 		$data['name_of_region_1'] = $_POST['name_of_region_1'];
 		$data['name_of_region_2'] = $_POST['name_of_region_2'];
 		$data['name_of_region_3'] = $_POST['name_of_region_3'];
 		$data['name_of_region_4'] = $_POST['name_of_region_4'];
 		$data['related_goods_number'] = $_POST['related_goods_number'];
 		$data['help_open'] = $_POST['help_open'];
 		$data['article_page_size'] = $_POST['article_page_size'];
 		$data['page_style'] = $_POST['page_style'];
 		$data['recommend_order'] = $_POST['recommend_order'];
 		/* 显示设置 */
 		
 		/* 购物流程 */
 		$data['can_invoice'] = $_POST['can_invoice'];
 		$data['use_integral'] = $_POST['use_integral'];
 		$data['use_bonus'] = $_POST['use_bonus'];
 		$data['use_surplus'] = $_POST['use_surplus'];
 		$data['use_how_oos'] = $_POST['use_how_oos'];
 		$data['send_confirm_email'] = $_POST['send_confirm_email'];
 		$data['send_ship_email'] = $_POST['send_ship_email'];
 		$data['send_cancel_email'] = $_POST['send_cancel_email'];
 		$data['send_invalid_email'] = $_POST['send_invalid_email'];
 		$data['order_pay_note'] = $_POST['order_pay_note'];
 		$data['order_unpay_note'] = $_POST['order_unpay_note'];
 		$data['order_ship_note'] = $_POST['order_ship_note'];
 		$data['order_receive_note'] = $_POST['order_receive_note'];
 		$data['order_unship_note'] = $_POST['order_unship_note'];
 		$data['order_return_note'] = $_POST['order_return_note'];
 		$data['order_invalid_note'] = $_POST['order_invalid_note'];
 		$data['order_cancel_note'] = $_POST['order_cancel_note'];
 		$data['invoice_content'] = $_POST['invoice_content'];
 		$data['anonymous_buy'] = $_POST['anonymous_buy'];
 		$data['min_goods_amount'] = $_POST['min_goods_amount'];
 		$data['one_step_buy'] = $_POST['one_step_buy'];
 		$data['stock_dec_time'] = $_POST['stock_dec_time'];
 		$data['cart_confirm'] = $_POST['cart_confirm'];
 		$data['show_goods_in_cart'] = $_POST['show_goods_in_cart'];
 		$data['show_attr_in_cart'] = $_POST['show_attr_in_cart'];
 		/* 购物流程 */
 		
 		/* 商品显示设置  */
 		$data['show_goodssn'] = $_POST['show_goodssn'];
 		$data['show_brand'] = $_POST['show_brand'];
 		$data['show_goodsweight'] = $_POST['show_goodsweight'];
 		$data['show_goodsnumber'] = $_POST['show_goodsnumber'];
 		$data['show_addtime'] = $_POST['show_addtime'];
 		$data['goodsattr_style'] = $_POST['goodsattr_style'];
 		$data['show_marketprice'] = $_POST['show_marketprice'];
 		/* 商品显示设置  */
 		
 		/* 短信设置 */
 		$data['sms_shop_mobile'] = $_POST['sms_shop_mobile'];
 		$data['sms_order_placed'] = $_POST['sms_order_placed'];
 		$data['sms_order_payed'] = $_POST['sms_order_payed'];
 		$data['sms_order_shipped'] = $_POST['sms_order_shipped'];
 		/* 短信设置 */
 		
 		/* WAP设置  */
 		$data['wap_config'] = $_POST['wap_config'];
 		
 		$wap_logo = $_FILES['wap_logo'] ;      //WAP LOGO
 		if($wap_logo['error'] == 0){
 			$type = $wap_logo['type'];
 			$tmp = explode('/', $type);
 			$ext = $tmp[count($tmp)-1];
 			$data['wap_logo'] = 'wap_logo.'.$ext;
 				
 			if(!$image->upload_image($wap_logo, $dir, $data['wap_logo'])){
 				$this->error($image->error_msg());
 				exit();
 			}
 		}
 		/* WAP设置  */ 		
 				
 		/* 网店信息 */
		$m = new Model('config');
 		//$sql = "update df_config set value='".$data['shop_name']."' where id=101";
        $config['id'] = 101;
        $config['value'] = $data['shop_name'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['shop_title']."' where id=102";
        $config['id'] = 102;
        $config['value'] = $data['shop_title'];
        $m->save($config);
 		
 		//$sql = "update df_config set value='".$data['shop_desc']."' where id=103";
        $config['id'] = 103;
        $config['value'] = $data['shop_desc'];
        $m->save($config);
 		
 		//$sql = "update df_config set value='".$data['shop_keywords']."' where id=104";
        $config['id'] = 104;
        $config['value'] = $data['shop_keywords'];
        $m->save($config);
 		
 		//$sql = "update df_config set value=".$data['shop_province']." where id=106";
        $config['id'] = 106;
        $config['value'] = $data['shop_province'];
        $m->save($config);
 		
        //$sql = "update df_config set value=".$data['shop_city']." where id=107";
        $config['id'] = 107;
        $config['value'] = $data['shop_city'];
 		$m->save($config);
 		
        //$sql = "update df_config set value=".$data['shop_district']." where id=123";
        $config['id'] = 123;
        $config['value'] = $data['shop_district'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['shop_address']."' where id=108";
        $config['id'] = 108;
        $config['value'] = $data['shop_address'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['qq']."' where id=109";
        $config['id'] = 109;
        $config['value'] = $data['qq'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['ww']."' where id=110";
        $config['id'] = 110;
        $config['value'] = $data['ww'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['skype']."' where id=111";
 		$config['id'] = 111;
        $config['value'] = $data['skype'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['ym']."' where id=112";
 		$config['id'] = 112;
        $config['value'] = $data['ym'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['msn']."' where id=113";
 		$config['id'] = 113;
        $config['value'] = $data['msn'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['service_email']."' where id=114";
        $config['id'] = 114;
        $config['value'] = $data['service_email'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['service_phone']."' where id=115";
 		$config['id'] = 115;
        $config['value'] = $data['service_phone'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['shop_closed']."' where id=116";
 		$config['id'] = 116;
        $config['value'] = $data['shop_closed'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['close_comment']."' where id=117";
		$config['id'] = 117;
        $config['value'] = $data['close_comment'];
 		$m->save($config);
 		
 		if($shop_logo['error'] == 0){
 			//$sql = "update df_config set value='".$data['shop_logo']."' where id=118";
 			$config['id'] = 118;
            $config['value'] = $data['shop_logo'];
     		$m->save($config);
 		} 		
 		
 		//$sql = "update df_config set value='".$data['licensed']."' where id=119";
 		$config['id'] = 119;
        $config['value'] = $data['licensed'];
 		$m->save($config);		
 	
        $config['id'] = 120;
        $config['value'] = $data['user_notice'];
        $m->save($config);

 		//$sql = "update df_config set value='".$data['shop_notice']."' where id=121";
 		$config['id'] = 121;
        $config['value'] = $data['shop_notice'];
 		$m->save($config);	
 		
 		//$sql = "update df_config set value=".$data['shop_reg_closed']." where id=122";
 		$config['id'] = 122;
        $config['value'] = $data['shop_reg_closed'];
 		$m->save($config);	
 		/* 网店信息 */
 		
 		/* 基本信息 */
 		//$sql = "update df_config set value='".$data['lang']."' where id=201";
 		$config['id'] = 201;
        $config['value'] = $data['lang'];
 		$m->save($config);	
 		
 		//$sql = "update df_config set value='".$data['icp_number']."' where id=202";
 		$config['id'] = 202;
        $config['value'] = $data['icp_number'];
 		$m->save($config);	
 		
 		if($icp_file['error'] == 0){
 			//$sql = "update df_config set value='".$data['icp_file']."' where id=203";
 			$config['id'] = 203;
            $config['value'] = $data['icp_file'];
     		$m->save($config);
 		}
 		
 		if($watermark['error'] == 0){
 			//$sql = "update df_config set value='".$data['watermark']."' where id=204";
 			$config['id'] = 204;
            $config['value'] = $data['watermark'];
     		$m->save($config);
 		}
 		
 		//$sql = "update df_config set value='".$data['watermark_place']."' where id=205";
 		$config['id'] = 205;
        $config['value'] = $data['watermark_place'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['watermark_alpha']."' where id=206";
 		$config['id'] = 206;
        $config['value'] = $data['watermark_alpha'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['use_storage']."' where id=207";
 		$config['id'] = 207;
        $config['value'] = $data['use_storage'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['market_price_rate']."' where id=208";
 		$config['id'] = 208;
        $config['value'] = $data['market_price_rate'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['rewrite']."' where id=209";
 		$config['id'] = 209;
        $config['value'] = $data['rewrite'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['integral_name']."' where id=210";
 		$config['id'] = 210;
        $config['value'] = $data['integral_name'];
 		$m->save($config);

 		//$sql = "update df_config set value='".$data['integral_scale']."' where id=211";
 		$config['id'] = 211;
        $config['value'] = $data['integral_scale'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['integral_percent']."' where id=212";
 		$config['id'] = 212;
        $config['value'] = $data['integral_percent'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['sn_prefix']."' where id=213";
 		$config['id'] = 213;
        $config['value'] = $data['sn_prefix'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['comment_check']."' where id=214";
 		$config['id'] = 214;
        $config['value'] = $data['comment_check'];
 		$m->save($config);
 		
 		if($no_picture['error'] == 0){
 			//$sql = "update df_config set value='".$data['no_picture']."' where id=215";
 			$config['id'] = 215;
            $config['value'] = $data['no_picture'];
     		$m->save($config);
 		}
 		
 		//$sql = "update df_config set value='".$data['stats_code']."' where id=216";
 		$config['id'] = 216;
        $config['value'] = $data['stats_code'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['cache_time']."' where id=217";
 		$config['id'] = 217;
        $config['value'] = $data['cache_time'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['register_points']."' where id=218";
 		$config['id'] = 218;
        $config['value'] = $data['register_points'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['enable_gzip']."' where id=219";
 		$config['id'] = 219;
        $config['value'] = $data['enable_gzip'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['top10_time']."' where id=220";
 		$config['id'] = 220;
        $config['value'] = $data['top10_time'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['timezone']."' where id=221";
 		$config['id'] = 221;
        $config['value'] = $data['timezone'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['upload_size_limit']."' where id=222";
 		$config['id'] = 222;
        $config['value'] = $data['upload_size_limit'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['cron_method']."' where id=223";
 		$config['id'] = 223;
        $config['value'] = $data['cron_method'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['comment_factor']."' where id=224";
 		$config['id'] = 224;
        $config['value'] = $data['comment_factor'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['enable_order_check']."' where id=225";
 		$config['id'] = 225;
        $config['value'] = $data['enable_order_check'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['default_storage']."' where id=226";
 		$config['id'] = 226;
        $config['value'] = $data['default_storage'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['bgcolor']."' where id=227";
 		$config['id'] = 227;
        $config['value'] = $data['bgcolor'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['visit_stats']."' where id=228";
 		$config['id'] = 228;
        $config['value'] = $data['visit_stats'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['send_mail_on']."' where id=229";
 		$config['id'] = 229;
        $config['value'] = $data['send_mail_on'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['auto_generate_gallery']."' where id=230";
 		$config['id'] = 230;
        $config['value'] = $data['auto_generate_gallery'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['retain_original_img']."' where id=231";
 		$config['id'] = 231;
        $config['value'] = $data['retain_original_img'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['member_email_validate']."' where id=232";
 		$config['id'] = 232;
        $config['value'] = $data['member_email_validate'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['message_board']."' where id=233";
 		$config['id'] = 233;
        $config['value'] = $data['message_board'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['certi']."' where id=234";
 		$config['id'] = 234;
        $config['value'] = $data['certi'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['message_check']."' where id=235";
 		$config['id'] = 235;
        $config['value'] = $data['message_check'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['search_keywords']."' where id=326";
 		$config['id'] = 326;
        $config['value'] = $data['search_keywords'];
 		$m->save($config);		
 		/* 基本信息 */
 		
 		/* 显示设置 */
 		//$sql = "update df_config set value='".$data['time_format']."' where id=302";
 		$config['id'] = 302;
        $config['value'] = $data['time_format'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['currency_format']."' where id=303";
 		$config['id'] = 303;
        $config['value'] = $data['currency_format'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['thumb_width']."' where id=304";
 		$config['id'] = 304;
        $config['value'] = $data['thumb_width'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['thumb_height']."' where id=305";
 		$config['id'] = 305;
        $config['value'] = $data['thumb_height'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['image_width']."' where id=309";
 		$config['id'] = 309;
        $config['value'] = $data['image_width'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['image_height']."' where id=306";
 		$config['id'] = 306;
        $config['value'] = $data['image_height'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['top_number']."' where id=307";
 		$config['id'] = 307;
        $config['value'] = $data['top_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['history_number']."' where id=308";
 		$config['id'] = 308;
        $config['value'] = $data['history_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['comments_number']."' where id=310";
 		$config['id'] = 310;
        $config['value'] = $data['comments_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['bought_goods']."' where id=311";
 		$config['id'] = 311;
        $config['value'] = $data['bought_goods'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['article_number']."' where id=312";
 		$config['id'] = 312;
        $config['value'] = $data['article_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['goods_name_length']."' where id=313";
 		$config['id'] = 313;
        $config['value'] = $data['goods_name_length'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['price_format']."' where id=314";
 		$config['id'] = 314;
        $config['value'] = $data['price_format'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['page_size']."' where id=315";
 		$config['id'] = 314;
        $config['value'] = $data['page_size'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['sort_order_type']."' where id=316";
 		$config['id'] = 316;
        $config['value'] = $data['sort_order_type'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['sort_order_method']."' where id=317";
 		$config['id'] = 317;
        $config['value'] = $data['sort_order_method'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_order_type']."' where id=318";
 		$config['id'] = 318;
        $config['value'] = $data['show_order_type'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['attr_related_number']."' where id=319";
 		$config['id'] = 319;
        $config['value'] = $data['attr_related_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['goods_gallery_number']."' where id=320";
 		$config['id'] = 320;
        $config['value'] = $data['goods_gallery_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['article_title_length']."' where id=321";
 		$config['id'] = 321;
        $config['value'] = $data['article_title_length'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['name_of_region_1']."' where id=322";
 		$config['id'] = 322;
        $config['value'] = $data['name_of_region_1'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['name_of_region_2']."' where id=323";
 		$config['id'] = 323;
        $config['value'] = $data['name_of_region_2'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['name_of_region_3']."' where id=324";
 		$config['id'] = 324;
        $config['value'] = $data['name_of_region_3'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['name_of_region_4']."' where id=325";
 		$config['id'] = 325;
        $config['value'] = $data['name_of_region_4'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['related_goods_number']."' where id=327";
 		$config['id'] = 327;
        $config['value'] = $data['related_goods_number'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['help_open']."' where id=328";
 		$config['id'] = 328;
        $config['value'] = $data['help_open'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['article_page_size']."' where id=329";
 		$config['id'] = 329;
        $config['value'] = $data['article_page_size'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['page_style']."' where id=330";
 		$config['id'] = 330;
        $config['value'] = $data['page_style'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['recommend_order']."' where id=331";
 		$config['id'] = 331;
        $config['value'] = $data['recommend_order'];
 		$m->save($config);
 		/* 显示设置 */
 		
 		/* 购物流程 */
 		//$sql = "update df_config set value='".$data['can_invoice']."' where id=401";
 		$config['id'] = 401;
        $config['value'] = $data['can_invoice'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['use_integral']."' where id=402";
 		$config['id'] = 402;
        $config['value'] = $data['use_integral'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['use_bonus']."' where id=403";
 		$config['id'] = 403;
        $config['value'] = $data['use_bonus'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['use_surplus']."' where id=404";
 		$config['id'] = 404;
        $config['value'] = $data['use_surplus'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['use_how_oos']."' where id=405";
 		$config['id'] = 405;
        $config['value'] = $data['use_how_oos'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['send_confirm_email']."' where id=406";
 		$config['id'] = 406;
        $config['value'] = $data['send_confirm_email'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['send_ship_email']."' where id=407";
 		$config['id'] = 407;
        $config['value'] = $data['send_ship_email'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['send_cancel_email']."' where id=408";
 		$config['id'] = 408;
        $config['value'] = $data['send_cancel_email'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['send_invalid_email']."' where id=409";
 		$config['id'] = 409;
        $config['value'] = $data['send_invalid_email'];
 		$m->save($config);
 			
 		//$sql = "update df_config set value='".$data['order_pay_note']."' where id=410";
 		$config['id'] = 410;
        $config['value'] = $data['order_pay_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_unpay_note']."' where id=411";
 		$config['id'] = 411;
        $config['value'] = $data['order_unpay_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_ship_note']."' where id=412";
 		$config['id'] = 412;
        $config['value'] = $data['order_ship_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_receive_note']."' where id=413";
 		$config['id'] = 413;
        $config['value'] = $data['order_receive_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_unship_note']."' where id=414";
 		$config['id'] = 414;
        $config['value'] = $data['order_unship_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_return_note']."' where id=415";
 		$config['id'] = 415;
        $config['value'] = $data['order_return_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_invalid_note']."' where id=416";
 		$config['id'] = 416;
        $config['value'] = $data['order_invalid_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['order_cancel_note']."' where id=417";
 		$config['id'] = 417;
        $config['value'] = $data['order_cancel_note'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['invoice_content']."' where id=418";
 		$config['id'] = 418;
        $config['value'] = $data['invoice_content'];
 		$m->save($config);		
 		
 		//$sql = "update df_config set value='".$data['anonymous_buy']."' where id=419";
 		$config['id'] = 419;
        $config['value'] = $data['anonymous_buy'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['min_goods_amount']."' where id=420";
 		$config['id'] = 420;
        $config['value'] = $data['min_goods_amount'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['one_step_buy']."' where id=421";
 		$config['id'] = 421;
        $config['value'] = $data['one_step_buy'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['stock_dec_time']."' where id=423";
 		$config['id'] = 423;
        $config['value'] = $data['stock_dec_time'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['cart_confirm']."' where id=424";
 		$config['id'] = 424;
        $config['value'] = $data['cart_confirm'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_goods_in_cart']."' where id=425";
 		$config['id'] = 425;
        $config['value'] = $data['show_goods_in_cart'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_attr_in_cart']."' where id=426";
 		$config['id'] = 426;
        $config['value'] = $data['show_attr_in_cart'];
 		$m->save($config);
 		/* 购物流程 */
 		
 		/* 商品显示设置 */
 		//$sql = "update df_config set value='".$data['show_goodssn']."' where id=701";
 		$config['id'] = 701;
        $config['value'] = $data['show_goodssn'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_brand']."' where id=702";
 		$config['id'] = 702;
        $config['value'] = $data['show_brand'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_goodsweight']."' where id=703";
 		$config['id'] = 703;
        $config['value'] = $data['show_goodsweight'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_goodsnumber']."' where id=704";
 		$config['id'] = 704;
        $config['value'] = $data['show_goodsnumber'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_addtime']."' where id=705";
 		$config['id'] = 705;
        $config['value'] = $data['show_addtime'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['goodsattr_style']."' where id=706";
 		$config['id'] = 706;
        $config['value'] = $data['goodsattr_style'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['show_marketprice']."' where id=707";
 		$config['id'] = 707;
        $config['value'] = $data['show_marketprice'];
 		$m->save($config); 		
 		/* 商品显示设置 */
 		
 		/* 短信设置 */
 		//$sql = "update df_config set value='".$data['sms_shop_mobile']."' where id=801";
 		$config['id'] = 801;
        $config['value'] = $data['sms_shop_mobile'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['sms_order_placed']."' where id=802";
 		$config['id'] = 802;
        $config['value'] = $data['sms_order_placed'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['sms_order_payed']."' where id=803";
 		$config['id'] = 803;
        $config['value'] = $data['sms_order_payed'];
 		$m->save($config);
 		
 		//$sql = "update df_config set value='".$data['sms_order_shipped']."' where id=804";
 		$config['id'] = 804;
        $config['value'] = $data['sms_order_shipped'];
 		$m->save($config);
 		/* 短信设置 */
 		
 		/* WAP设置 */
 		//$sql = "update df_config set value='".$data['wap_config']."' where id=901";
 		$config['id'] = 901;
        $config['value'] = $data['wap_config'];
 		$m->save($config);
 		
 		if($wap_logo['error'] == 0){
 			//$sql = "update df_config set value='".$data['wap_logo']."' where id=902";
 			$config['id'] = 902;
            $config['value'] = $data['wap_logo'];
     		$m->save($config);
 		}
 		/* WAP设置 */
 		
		$this->success('保存成功');
 	}

 	

 	
 	
 	
 }