<?php

//0 is 404, 1 is handled by mvc 2 is file exists
function check_route($request_uri) {
	//check if controller and action exists
	$question_pos = strpos($request_uri, '?');
	$query_string = substr($request_uri, $question_pos);
	$request_uri = substr($request_uri, 0, $question_pos);
	$arr = array_values(array_filter(explode('/', $request_uri)));

	if(sizeof($arr) == 0 || sizeof($arr) > 0) {

		if(sizeof($arr) == 0) {
			$arr[0] = 'content';
		}
		$controller_path = '/controllers/' . $arr[0] . 'controller.php';
		if(app_file_exists($controller_path)) {
			if(sizeof($arr) == 1) {
				$arr[1] = 'index';
			}
			app_include_once($controller_path);
			$methods = get_class_methods($arr[0] . 'controller');
			foreach($methods as $key => $var) {
				if($var == $arr[1]){
					return 1;
				}
			}
		}
	}

	//check if a file with that path exists
	$file = $arr[sizeof($arr)-1];
	if(preg_match('/.*\.php(\?.*)?', $file)){
		return 0;
	}
	return app_file_exists($request_uri);
}
 ?>
