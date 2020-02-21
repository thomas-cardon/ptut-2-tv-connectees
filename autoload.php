<?php

//include 'src/controllers/Controller.php';
//include 'src/controllers/UserController.php';
//include 'src/models/Model.php';
//include 'src/models/Entity.php';
//include 'src/views/View.php';
//include 'src/views/UserView.php';
//
//foreach (glob(ABSPATH.TV_PLUG_PATH.'src/controllers/*.php') as $filename) {
//	include_once $filename;
//}
//
//foreach (glob(ABSPATH.TV_PLUG_PATH.'src/models/*.php') as $filename) {
//	include_once $filename;
//}
//
//foreach (glob(ABSPATH.TV_PLUG_PATH.'src/views/*.php') as $filename) {
//	include_once $filename;
//}

spl_autoload_register(function($className) {
	$path = ABSPATH.TV_PLUG_PATH.'src/';

	$file = $path.$className . '.php';

	if (file_exists($file)) {
		include $file;
	}
});