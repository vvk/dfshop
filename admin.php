<?php
    /*
     *  后台入口文件 
     * 
     */
    header('content-type:text/html;charset=utf-8');
    define('DF_ROOT', str_replace('\\', '/', dirname(__FILE__)));  
    define('THINK_PATH','./ThinkPHP/');
    define('APP_NAME','Admin');
    define('APP_PATH','./Admin/');
    define('APP_DEBUG',true);
    //define('SAE_RUNTIME',true);
    define('ENGINE_NAME','sae');
    require THINK_PATH.'ThinkPHP.php';


