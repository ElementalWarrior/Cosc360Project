<?php
session_start();

require_once('helper.php');
require_once('html_helper.php');
require_once('config.php');
app_include('routing.php');

$path = $_SERVER['REQUEST_URI'];

$action = 'index';
$controller = 'content';
$params = array();

$routing_info = check_route($_SERVER['REQUEST_URI']);
switch($routing_info['code']) {

	//404
	case 0:
	$action = 'file_not_found';
	$controller = 'error';
	break;

	//mvc route
	case 1:
	$action = $routing_info['action'];
	$controller = $routing_info['controller'];
	$params = $routing_info['params'];
	break;

	//file found
	case 2:
	$path = resolve_path($routing_info['path']);
	$ext = substr($path, strrpos($path, '.')+1);
	$mime = mime_types()[$ext];
	header("Content-Type:$mime");
	readfile($path);
	break;
}
$user = null;
if(!empty($_SESSION['account_id'])) {
	$user = array(
		'account_id' => $_SESSION['account_id'],
		'username' => $_SESSION['username']
	);
}
$page_body = '';
function render_body(){
	global $page_body;
	echo $page_body;
}
if($routing_info['code'] != 2) {
	app_include_once("/controllers/$controller" . '_controller.php');
	$class = $controller . "_controller";
	$controller_object = new $class();
	$action_result = call_user_func_array(array($controller_object, $action), $params);
	ob_start();
		if(is_array($action_result)) {
			$view_data = $action_result['view_data'];
			Html::render_view($action_result['action'], $action_result['controller']);
		} else if(is_string($action_result) || is_numeric($action_result)) {
			echo $action_result;
		} else if(is_bool($action_result)){
			echo $action_result ? 'true' : 'false';
		}
	$page_body = ob_get_clean();
	ob_end_flush();
	// print_r($_SERVER);
	Html::render_view('layout');
	// include('/views/shared/layout.php');
}
