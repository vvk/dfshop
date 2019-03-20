<?php

/**
 *  qq登录接口
 *  @author   sunwq
 *  @date     2014-07-31
 */ 

class QC{
    private $appid = '';
    private $appkey = '';
    private $callback = '';
    private $access_token = '';
    private $openid = '';
    private $responseInfo = array();  //返回人信息
    private $errorNo = 0;
    private $errorMsg = '';
    private $auth_code_url = 'https://graph.qq.com/oauth2.0/authorize';
    private $access_token_url = 'https://graph.qq.com/oauth2.0/token';
    private $openid_url = 'https://graph.qq.com/oauth2.0/me';
    static  $_instance;
    
    /**
     *  静态方法初始化
     *  @access public
     *  @param  string  $appid    qq登录appid
     *  @param  string  $appkey   qq登录appkey
     *  @param  string  $callback qq登录回调地址
     */ 
    public static function instance($appid, $appkey, $callback){
        if(!(self::$_instance instanceof self)){
            self::$_instance = new self($appid, $appkey, $callback); 
         }
         return self::$_instance;
    }
    
    /**
     *  @access private
     *  @param  string  $appid    qq登录appid
     *  @param  string  $appkey   qq登录appkey
     *  @param  string  $callback qq登录回调地址
     */ 
    private function __construct($appid, $appkey, $callback){
        $this->appid = $appid;
        $this->appkey = $appkey;
        $this->callback = $callback;
        session_start();
    }
    
    /**
     * 获取登录时的URL
     * @access public
     * @param  string  $scope 请求用户授权时向用户显示的可进行授权的列表。
     * 可填写的值是API文档中列出的接口，以及一些动作型的授权（目前仅有：do_like），
     * 如果要填写多个接口名称，请用逗号隔开。
     * 例如：scope=get_user_info,list_album,upload_pic,do_like
     * 不传则默认请求对接口get_user_info进行授权。
     * 建议控制授权项的数量，只传入必要的接口名称，因为授权项越多，
     * 用户越可能拒绝进行任何授权。
     * @return string  返回登录时的地址的字符串
     */ 
    public function getLoginUrl($scope = 'get_user_info'){
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION['qq_state'] = $state;
        
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $this->appid,
            "redirect_uri" => $this->callback,
            "state" => $state,
            "scope" => $scope
        );
        $url = $this->combineURL($this->auth_code_url, $keysArr);
        return $url;
    }
    
    /**
     *  获取access_token
     *  @return   mixed  出错时返回获取false，成功是返回access_token   
     */ 
    public function qqCallback(){
        $state = $_SESSION['qq_state'];
        if($_REQUEST['state'] != $state){  //出错
            $this->errorMsg = 'The state does not match. You may be a victim of CSRF';
            $this->errorNo = '30001';
            return false;
        }
        
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $this->appid,
            "redirect_uri" => $this->callback,
            "client_secret" => $this->appkey,
            "code" => $_GET['code']
        );
        
        $token_url = $this->combineURL($this->access_token_url, $keysArr);
        $response = $this->get_contents($token_url);
        //$response = access_token=BAA02F17AD3A3665F1353CB3FD3F4F89&expires_in=7776000&refresh_token=94546918566FF3D01C6812AF8E0B7BDE
           
        if(strpos($response, "callback") !== false){
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                $this->errorNo = $msg->error;
                $this->errorMsg = $msg->error_description;
                return false;
            }
        }

        $params = array();
        parse_str($response, $params);

        $this->access_token = $params["access_token"];
        return $params["access_token"];
    }
    
    /**
     *  获取openid
     *  @return  string   32位字符串
     */ 
    public function getOpenId(){
        $keysArr = array(
            "access_token" => $this->access_token
        );
        
        $graph_url = $this->combineURL($this->openid_url, $keysArr);
        $response = $this->get_contents($graph_url);

        //检测错误是否发生
        if(strpos($response, "callback") !== false){
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }
        
        $user = json_decode($response);
        if (isset($user->error)){
            $this->errorNo = $user->error;
            $this->errorMsg = $user->error_description;
            return false;
        }
        $this->openid = $user->openid;
        return $this->openid;
    }
    
    /**
     *  获取用户信息
     *  @return  array   返回用户信息
     */ 
    public function getUserInfo(){
        $url = 'https://graph.qq.com/user/get_user_info';
        
        $keysArr = array(
            "access_token" => $this->access_token,
            'oauth_consumer_key'=>$this->appid,
            'openid'=>$this->openid,
            'format'=>'json'
        );
        
        $url = $this->combineURL($url, $keysArr);
        $response = $this->get_contents($url);
        $response = json_decode($response, true);
        $this->responseInfo = $response;
        return $response;
    }
    
    public function getAppId(){
        return $this->appid;
    }
    
    public function getAppKey(){
        return $this->appkey;
    }
    
    public function getAccess_token(){
        return $this->access_token;
    }
    
    public function getErrorNo(){
        return $this->errorNo;
    }
    
    public function getErrorMsg(){
        return $this->errorMsg;
    }
    
    public function getResponseInfo(){
        return $this->responseInfo;
    }
    
    /**
     * combineURL
     * 拼接url
     * @param string $baseURL   基于的url
     * @param array  $keysArr   参数列表数组
     * @return string           返回拼接的url
     */
    public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=$val";
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        
        return $combined;
    }
    
    /**
     * get_contents
     * 服务器通过get请求获得内容
     * @param string $url       请求的url,拼接后的
     * @return string           请求返回的内容
     */
    public function get_contents($url){
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        }

        return $response;
    }
    
    /**
     * 返回错误消息
     * @access  private
     * @param   int    $errNo   错误码
     * @return  string 错误信息
     */ 
    private function setError($errNo){
        $error = array(
            
        );
        return $error[$errNo];
    }
}

