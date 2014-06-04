<?php
$path  = ''; // It should be end with a trailing slash    
if (!defined('WP_LOAD_PATH')) {
	$classic_root = dirname(dirname(dirname(dirname(dirname(__FILE__))))).DIRECTORY_SEPARATOR;	
	if (file_exists($classic_root.'wp-load.php') ) {
		define('WP_LOAD_PATH', $classic_root);
	} else {
		if (file_exists($path.'wp-load.php')) {
			define('WP_LOAD_PATH', $path);
		} else {
			exit("Could not find wp-load.php");
		}
	}
}

//Load wp-load.php
require_once(WP_LOAD_PATH.'wp-load.php');


$WP_Yumpu_Admin_Editor = new WP_Yumpu_Admin_Editor(WP_Yumpu::$PLUGIN_PATH);
$WP_Yumpu_Admin_Editor->run();