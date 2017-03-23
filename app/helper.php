<?php



$phys_path = str_replace('\\', '/', __DIR__) . '/';
function app_include($path) {
	global $phys_path;
	if($path[0] == '/' || $path[0] == '\\') {
		$path = substr($path, 1);
	}
	include($phys_path . $path);
}
function app_include_once($path) {
	global $phys_path;
	include_once($phys_path . $path);
}
function app_file_exists ($path) {
	global $phys_path;
	return file_exists($phys_path . $path);
}

 ?>
