<?php

class controller {
	protected function render_action($action, $controller, $view_data = null) {
		return array('action' => $action, 'controller' => $controller, 'view_data' => $view_data);
	}
}
 ?>
