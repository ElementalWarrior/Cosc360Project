<?php

class controller {
	public function render_action($action, $controller, $view_data = null) {
		ob_start();
		Html::render_view($action, $controller, $view_data);
		$page_body = ob_get_contents();
		ob_end_clean();
		return $page_body;
	}
	public static function create_db_connection() {
		$host = DB_URL;
		$port = DB_PORT;
		$db = DB_NAME;
		$user = DB_USER;
		$pass = DB_PASS;
		$conn_string = "mysql:host=$host;port=$port;dbname=$db";
		$dbh = new PDO($conn_string, $user, $pass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		return $dbh;
	}
	protected function log_activity($action, $thread_id = null, $post_id = null, $admin_action = null) {
		global $user;
		global $path;
		if($thread_id != null) {
			$thread_id = (int)$thread_id;
		}
		if($post_id != null) {
			$post_id = (int)$post_id;
		}
		if($admin_action != null) {
			$admin_action = (bool)$admin_action;
		}
		$dbh = $this->create_db_connection();
		$stmt = $dbh->prepare('INSERT into activity_log(user_agent, ip, account_id, username, action, request_uri, thread_id, post_id, admin_action) select :user_agent, :ip, :account_id, :username, :action, :uri, :thread_id, :post_id, :admin_action');
		$stmt->execute(array(
			':user_agent' => Html::special_chars($_SERVER['HTTP_USER_AGENT']),
			':ip' => $_SERVER['REMOTE_ADDR'],
			':account_id' => empty($user['account_id']) ? null : $user['account_id'],
			':username' => empty($user['username']) ? null : $user['username'],
			':action' => $action,
			':uri' => $path,
			':thread_id' => $thread_id,
			':post_id' => $post_id,
			':admin_action' => $admin_action
		));
	}
}
 ?>
