<?php
date_default_timezone_set("America/Vancouver");
session_start();
require_once('helper.php');
require_once('html_helper.php');
require_once('config.php');
require_once('controllers/controller.php');
app_include('routing.php');

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
		if($errno == E_USER_ERROR)
		{
			http_response_code(500);
			throw $e;
		} else {
			echo $e->getMessage();
		}
	}
}
set_error_handler('handle_error');

$file = array_reverse(explode('/', $_SERVER['SCRIPT_NAME']))[0];
$sub_path = str_replace($file, '', $_SERVER['SCRIPT_NAME']);
$sub_path = substr($sub_path, 0, strlen($sub_path)-1);
$path = '';
if($sub_path !== '/') {
	$path = str_replace($sub_path, '', $_SERVER['REQUEST_URI']);
} else {
	$path = $_SERVER['REQUEST_URI'];
}
$action = 'index';
$controller = 'content';
$params = array();

function error_page(&$action, &$controller) {
	$action = 'file_not_found';
	$controller = 'error';
}

function construct_user() {
	global $user;
	$user = array(
		'account_id' => $_SESSION['account_id'],
		'username' => $_SESSION['username'],
		'admin' => $_SESSION['admin']
	);
}
$user = null;
if(!empty($_SESSION['account_id'])) {
	construct_user();
}

$routing_info = check_route($path);

$handler = '';
switch($routing_info['code']) {
	case 0:
	case 1:
	 	$handler = 'controllers';
		break;
	case 2:
		$handler = 'static';
}

$path_noquery = $path;
$query = null;
if(strpos($path, '?') !== false) {
	$path_noquery = substr($path_noquery, 0, strpos($path_noquery, '?'));
	$query = substr($path, strpos($path,'?'));
}
$dbh = controller::create_db_connection();
$stmt = $dbh->prepare('INSERT into request_log (user_ip, account_id, username, handler, request_uri, request_query, request_type) select :ip, :account_id, :username, :handler, :uri, :query, :type');
$stmt->execute(array(
	':ip' => $_SERVER['REMOTE_ADDR'],
	':account_id' => empty($user['account_id']) ? null : $user['account_id'],
	':username' => empty($user['username']) ? null : $user['username'],
	':handler' => $handler,
	':uri' => $path_noquery,
	':query' => $query,
	':type' => $_SERVER['REQUEST_METHOD']
));
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
$page_body = '';
function render_body(){
	global $page_body;
	echo $page_body;
}
if($routing_info['code'] != 2) {
	$page_body = Html::render_action($action, $controller, $params);
	// print_r($_SERVER);
	Html::render_view('layout');
	// include('/views/shared/layout.php');
}
