<?php

global $page_title;
$page_title = "Recover your Password";
global $sub_path;
 ?>
<section id="recover_password">
	<form class="" action="<?php echo $sub_path; ?>/account/recover_password/<?php echo $view_data['token']; ?>" method="post">
		<h2>Change Password</h2>
		<?php if(!empty($view_data['error'])) {
			echo "<h3 role=\"alert\"><strong>" . $view_data['error'] . "</strong></h3>";
		}?>
		<input type="password" name="password" value="" placeholder="Password">
		<input type="password" name="password-check" value="" placeholder="Re-enter Password">
		<button type="submit" name="button" class="btn">Reset Password</button>
	</form>
</section>
