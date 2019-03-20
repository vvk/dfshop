<?php
    /*
     *   自定义公用函数,该文件会在执行过程中自动加载，并且合并到项目编译统一缓存 
     */

    /*
    *   @desc    检查邮箱是否正确
    *   @access  public
    *   @return  void
    *   @date    2014-03-29
    */ 
    function isEmail($email){
       $reg = '/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/';
       return preg_match($reg, strtolower($email));    
    }
    
    /*
    *   @desc    检验是否只为字母或数字
    *   @access  public
    *   @return  bool
    *   @date    2014-03-29
    */ 
    function isLetNum($str){
        $par = '/^[A-Za-z0-9]*$/';
        return preg_match($par, $str);
    }
    
    /*
    *   @desc    判断是否为手机号
    *   @access  public
    *   @return  bool
    *   @date    2014-04-08
    */
    function isPhone($phone){ 
         $par = '/^13[0-9]{9}|15[012356789][0-9]{8}|18[02356789][0-9]{8}|147[0-9]{8}$/';
         return preg_match($par, $phone);
    }
    
    
    /*
    *   @desc    判断是否为QQ
    *   @access  public
    *   @return  bool
    *   @date    2014-04-08
    */
    function isQQ($num){
        $par = '/^[1-9]\d{4,10}$/';
        return preg_match($par, $num);
    }