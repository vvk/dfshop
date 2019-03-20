<?php
header('content-type:text/html;charset=utf-8');
define('DF_ROOT', str_replace('\\', '/', dirname(__FILE__)));  
define('THINK_PATH','./ThinkPHP/');
define('APP_NAME','App');
define('APP_PATH','./App/');
define('APP_DEBUG',false);
//define('SAE_RUNTIME',true);
// define('ENGINE_NAME','sae');
require THINK_PATH.'ThinkPHP.php';
