<?php
session_start();
require_once('helper.php');
require_once('html_helper.php');
require_once('config.php');
require_once('controllers/controller.php');
app_include('routing.php');

$path = $_SERVER['REQUEST_URI'];

$action = 'index';
$controller = 'content';
$params = array();

function handle_error($errno, $errstr, $errfile, $errline) {
	$msg = "";
	switch ($errno) {
	    case E_USER_ERROR:
	        $msg .= "<b>My ERROR</b> [$errno] $errstr<br />\n";
	        $msg .= "  Fatal error on line $errline in file $errfile<br />\n";
	        $msg .= " PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
	        exit(1);
	        break;

	    case E_USER_WARNING:
	        $msg .=  "<b>My WARNING</b> [$errno] $errstr<br />\n";
	        $msg .= "  on line $errline in file $errfile<br />\n";
	        break;

	    case E_USER_NOTICE:
	        $msg .=  "<b>My NOTICE</b> [$errno] $errstr<br />\n";
	        $msg .= " on line $errline in file $errfile<br />\n";
	        break;

	    default:
	        $msg .=  "Unknown error type: [$errno] $errstr<br />\n";
	        $msg .= "  on line $errline in file $errfile<br />\n";
	        break;
    }
	$e = new Exception($msg);
	$dbh = controller::create_db_connection();
	$stmt = $dbh->prepare('INSERT into error_log(message, stack_trace) select :msg, :trace');
	$stmt->execute(array(
		':msg' => $msg,
		':trace' => $e->getTraceAsString()
	));
	if(stripos($_SERVER['SERVER_NAME'],'localhost') !== false) {
		echo $e->getMessage();
	}
}
set_error_handler('handle_error');

function error_page(&$action, &$controller) {
	$action = 'file_not_found';
	$controller = 'error';
}

$routing_info = check_route($_SERVER['REQUEST_URI']);
switch($routing_info['code']) {

	//404
	case 0:
		error_page($action, $controller);
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
		if(is_file($path)) {
			$ext = substr($path, strrpos($path, '.')+1);
			$mime = mime_types()[$ext];
			header("Content-Type:$mime");
			readfile($path);
		} else {
			error_page($action, $controller);
			$routing_info['code'] = 0;
		}
		break;
}
$user = null;
if(!empty($_SESSION['account_id'])) {
	$user = array(
		'account_id' => $_SESSION['account_id'],
		'username' => $_SESSION['username'],
		'admin' => $_SESSION['admin']
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
	try {
		$controller_object = new $class();
		$action_result = call_user_func_array(array($controller_object, $action), $params);
	} catch (Exception $e) {
		$dbh = controller::create_db_connection();
		$stmt = $dbh->prepare('INSERT into error_log(message, stack_trace) select :msg, :trace');
		$stmt->execute(array(
			':msg' => $e->getMessage(),
			':trace' => $e->getTraceAsString()
		));
		if(stripos($_SERVER['SERVER_NAME'],'localhost') !== false) {
			throw $e;
		}
	}
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
