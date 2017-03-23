<?php
class Html {
	public static function render_view($view, $controller = null) {
		if(strlen($controller) == 0) {
			$path = '/views/shared/';
		} else {
			$path = "/views/$controller/";
		}

		$full_path = "$path" . "$view.php";
		if(!app_file_exists($full_path)) {
			echo ("Could not find view '$view' on '$controller' controller");
		} else {
			app_include($full_path);
		}
	}
}
