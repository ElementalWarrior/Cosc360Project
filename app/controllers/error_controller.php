<?php
require_once('controller.php');
class error_controller extends controller {
	public function file_not_found() {
		header("HTTP/1.0 404 Not Found");
		return $this->render_action('file_not_found', 'error');
	}
	public function server_error() {
		http_response_code(500);
		return $this->render_action('server_error', 'error');
	}
}


 ?>
