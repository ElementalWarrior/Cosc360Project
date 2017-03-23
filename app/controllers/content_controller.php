<?php
require_once('controller.php');
class content_controller extends controller{
	public function index() {
		return $this->render_action('index', 'content');
	}
}
