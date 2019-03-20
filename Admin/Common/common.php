<?php
	
	/*
	 * 	后台公用函数
	 * 
	 */


	/**
	 * 获得服务器上的 GD 版本
	 *
	 * @access      public
	 * @return      int         可能的值为0，1，2
	 */
	function gd_version()
	{
		static $version = -1;
	
		if ($version >= 0)
		{
			return $version;
		}
	
		if (!extension_loaded('gd'))
		{
			$version = 0;
		}
		else
		{
			// 尝试使用gd_info函数
			if (PHP_VERSION >= '4.3')
			{
				if (function_exists('gd_info'))
				{
					$ver_info = gd_info();
					preg_match('/\d/', $ver_info['GD Version'], $match);
					$version = $match[0];
				}
				else
				{
					if (function_exists('imagecreatetruecolor'))
					{
						$version = 2;
					}
					elseif (function_exists('imagecreate'))
					{
						$version = 1;
					}
				}
			}
			else
			{
				if (preg_match('/phpinfo/', ini_get('disable_functions')))
				{
					/* 如果phpinfo被禁用，无法确定gd版本 */
					$version = 1;
				}
				else
				{
					// 使用phpinfo函数
					ob_start();
					phpinfo(8);
					$info = ob_get_contents();
					ob_end_clean();
					$info = stristr($info, 'gd version');
					preg_match('/\d/', $info, $match);
					$version = $match[0];
				}
			}
		}
	
		return $version;
	}
	

	/*
	 *    @description  创建目录
	 *    @date         2014-02-17
	 */
	function createFolder($path)
	{
		if (!file_exists($path))
		{
			createFolder(dirname($path));
	
			mkdir($path, 0777);
		}
	}

	/*
	 *	@desc   删除文件
	 *  @date   2014-03-02 
	 */
	function delDir($dir) {
		$dh=opendir($dir);
		while(@$file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					deldir($fullpath);
				}
			}
		}
		closedir($dh);
		if(rmdir($dir)) {
			return true;
		} else {
			return false;
		}
	}

	
	