<?php
class Html {
	public static function render_view($view, $controller = null, $view_data = null) {
		if(strlen($controller) == 0) {
			$path = '/views/shared/';
		} else {
			$path = "/views/$controller/";
		}

		$full_path = "$path" . "$view.php";
		if(!app_file_exists($full_path)) {
			echo ("Could not find view '$view' on '$controller' controller");
		} else {
			include(resolve_path($full_path));
		}
	}
	public static function render_action($action, $controller, $params = []) {
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
			http_response_code(500);
			if(stripos($_SERVER['SERVER_NAME'],'localhost') !== false) {
				throw $e;
			} else {
				throw new Exception('An error occurred.');
			}
		}
		if(is_string($action_result) || is_numeric($action_result)) {
			$page_body = $action_result;
		} else if(is_bool($action_result)){
			$page_body =  $action_result ? 'true' : 'false';
		}
		return $page_body;
	}
	public static function special_chars($str) {
		return htmlspecialchars($str, ENT_XHTML, 'UTF-8');
	}
}
