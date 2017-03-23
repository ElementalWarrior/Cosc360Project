<?php
class error_controller {
	public function file_not_found() {
		return $this->render_action('file_not_found', 'error')
	}
}


 ?>
