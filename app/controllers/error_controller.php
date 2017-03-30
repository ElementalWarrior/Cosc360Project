<?php
require_once('controller.php');
class error_controller extends controller {
	public function file_not_found() {
		header("HTTP/1.0 404 Not Found");
		return $this->render_action('file_not_found', 'error');
	}
}


 ?>
