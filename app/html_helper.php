<?php
class Html {
	public static function RenderView($action, $controller = null) {
		if(strlen($controller) == 0) {
			$path = '/views/shared/';
		} else {
			$path = "/views/$controller/";
		}

		// echo "<br/><br/>" . "$path" . "$action.php". '<br/>';
		if(!app_file_exists("$path" . "$action.php")) {
			echo ("Could not find view");
		} else {
			app_include("$path" . "$action.php");
		}
	}
}
