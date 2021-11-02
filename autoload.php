<?php

spl_autoload_register(function($className) {
	$path = ABSPATH.TV_PLUG_PATH.'src/';

	$file = $path.$className . '.php';

	if (file_exists($file)) {
		error_reporting(E_ALL);
		ini_set('display_errors', '1');

		include $file;
	}
});
