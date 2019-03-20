<?php
return array(
    //左右定界符
    //'TMPL_L_DELIM'=>'<{',
    //'TMPL_R_DELIM'=>'}>',

    //连接数据库
    'DB_TYPE'=>'mysql',
    'DB_HOST'=>'',
    'DB_PORT'=>'',
    'DB_NAME'=>'',
    'DB_USER'=>'',
    'DB_PWD'=>'',
    'DB_PREFIX'=>'df_',
        

    //PDO方式连接数据库    
    'DB_TYPE'=>'pdo',
    'DB_USER'=>'root',
    'DB_PWD'=>'',
    'DB_PREFIX'=>'df_',
    'DB_DSN'=>'mysql:host=localhost;dbname=dongfang_shop;charset=utf8',
 
    'UrlRewriter'=>1,   //伪静态

    'TMPL_TEMPLATE_SUFFIX'=>'.html',   //修改模板后缀 
    
    'DEFAULT_THEME'=>'default',    //皮肤
    
    // 开启日志记录
    'LOG_RECORD' => false, 
    // 只记录EMERG ALERT CRIT ERR 错误
    //'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', 
        
    'URL_MODEL'=>1,
    'URL_HTML_SUFFIX'=>'html',
        
    //'SHOW_PAGE_TRACE' =>true,// 显示页面Trace信息       
    
    //"LOAD_EXT_FILE"=>"user",   //要自动加载的公共函数为中的文件
    
    //'URL_PATHINFO_DEPR'=>'-',     //参数之间的间隔符
    
    //'URL_ROUTER_ON'=>true,      //开起路由功能
    
    'URL_CASE_INSENSITIVE' =>true,//设置URL大小写不敏感
    
    //'TMPL_ENGINE_TYPE'=>'PHP',//设置模板引擎
    
    'TMPL_ACTION_ERROR' => TMPL_PATH.'default/Public/jump.html',   //默认错误跳转对应的模板文件  
    'TMPL_ACTION_SUCCESS' => TMPL_PATH.'default/Public/jump.html',  //默认成功跳转对应的模板文件
        
    /* 'TOKEN_ON'=>true,  // 是否开启令牌验证
    'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
    'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true */

    //'DB_FIELDTYPE_CHECK'=>true,  // 开启字段类型验证

    //'TMPL_EXCEPTION_FILE'=>'./App/Tpl/Public/error.html', // 定义公共错误模板

    //'DEFAULT_TIMEZONE'=>'Asia/Singapore',   //设置默认时区
        
    /* 开起多语言支持  */
    //'LANG_SWITCH_ON' => true,   // 开启语言包功能
    //'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    //'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    //'VAR_LANGUAGE'     => 'l', // 默认语言切换变量
        
    //统计代码
    'stats_code'=>'<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id=\'cnzz_stat_icon_1252971699\'%3E%3C/span%3E%3Cscript src=\'" + cnzz_protocol + "s19.cnzz.com/z_stat.php%3Fid%3D1252971699%26show%3Dpic\' type=\'text/javascript\'%3E%3C/script%3E"));</script>',
    //'stats_code'=>'qwe',

    /*******以下是支付配置************************************************/
    'alipay_email'=>'',          //支付宝帐号
    'alipay_partner'=>'',  //合作身份者id，以2088开头的16位纯数字
    'alipay_key'=>'',      //安全检验码，以数字和字母组成的32位字符
    'alipay_sign_type'=>strtoupper('MD5'), //签名方式 不需修改
    'apliay_input_charset'=>strtolower('utf-8'),//字符编码格式 目前支持 gbk 或 utf-8
    'apliay_cacert'=>DF_ROOT.'/Public/data/alipay/'.'cacert.pem',//ca证书路径地址，用于curl中ssl校验,请保证cacert.pem文件在当前文件夹目录中
    'apilay_transport'=>'http',//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    /*******以上是支付配置************************************************/

    //QQ登录
    'qq_app_id'=>'', 
    'qq_app_key'=>'',
    'qq_callback'=>'d',
    
    //微博登录
    'sina_app_key'=>'',
    'sina_app_secret'=>'',
    'sina_callback'=>'',
    'sina_accessTokenURL'=>'',
    'sina_authorizeURL'=>'',
        
);
?>