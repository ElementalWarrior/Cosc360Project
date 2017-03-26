<?php

class controller {
	protected function render_action($action, $controller, $view_data = null) {
		return array('action' => $action, 'controller' => $controller, 'view_data' => $view_data);
	}
	protected function create_db_connection() {
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
}
 ?>
