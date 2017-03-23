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
function resolve_path($path) {
	global $phys_path;
	return $phys_path . $path;
}
//http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
function mime_types(){
    $s=array();
    foreach(explode("\n",file_get_contents('mimes.txt')) as $x)
        if(isset($x[0]) && $x[0] !== '#'
		&& preg_match_all('#([^\s]+)#',$x,$out) && isset($out[1]) && ($c=count($out[1])) > 1) {
            for($i=1; $i<$c; $i++) {
                $s[$out[1][$i]] = $out[1][0];
			}
		}
    return $s;
}
 ?>
