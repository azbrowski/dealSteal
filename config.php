<?php
	$config = array(
		"db" => array (
			'host' => 'localhost',
			'name' => 'deals',
			'user' => 'root',
			'pass' => ''
		)
	);
	
	defined("ROOT_PATH")
    or define("ROOT_PATH", __DIR__);

	defined("ROOT_URL")
    or define("ROOT_URL", 'http://localhost/');
	
	defined("BACK_PATH")
    or define("BACK_PATH", ROOT_PATH . '/backend');
		
	defined("BACK_URL")
    or define("BACK_URL", ROOT_URL . 'backend/');		

	defined("CSS_PATH")
    or define("CSS_PATH", ROOT_URL . 'css/');	

	defined("FONTS_PATH")
    or define("FONTS_PATH", ROOT_URL . 'fonts/');	
	
	defined("LIB_PATH")
    or define("LIB_PATH", ROOT_URL . 'lib/');	
	
	defined("RESOURCE_PATH")
    or define("RESOURCE_PATH", ROOT_PATH . '/resource');

	defined("MODELS_PATH")
    or define("MODELS_PATH", ROOT_PATH . '/models');
		
	defined("PHP_PATH")
    or define("PHP_PATH", ROOT_PATH . '/php');
		
	$siteName = 'Logo';
	$pageTitle = 'Blank';
		
?>