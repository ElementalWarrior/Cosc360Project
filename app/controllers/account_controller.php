<?php
require_once('controller.php');
class account_controller extends controller {
	public function login() {
		$view_data = array('foo' => 'bar');
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$view_data['is_post'] = true;
		} else {
			$view_data['is_post'] = false;
		}
		return $this->render_action('login', 'account', $view_data);
	}
}
