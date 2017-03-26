<?php
require_once('controller.php');
class error_controller extends controller {
	public function file_not_found() {
		return $this->render_action('file_not_found', 'error');
	}
}


 ?>
