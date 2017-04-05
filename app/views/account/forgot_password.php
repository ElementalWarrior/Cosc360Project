<?php
global $sub_path;
global $page_title;
$page_title = "Forgot your Password";

 ?>
<section id="forgot_password" class="single-centered">
	<form class="" action="<?php  echo $sub_path; ?>/account/forgot_password" method="post">
		<h2>Password Recovery</h2>
		<?php if(!empty($view_data['error'])) {
			echo "<h3 role=\"alert\"><strong>" . $view_data['error'] . "</strong></h3>";
		}?>
		<p>In order to recover your password, enter the email you used to register.</p>
		<div class="">
			<input type="email" name="email" value="" required aria-required="true" placeholder="Email Address"/>
		</div>
		<button type="submit" name="button" class="btn" id="btnForgotSubmit">Send Password Reset Email</button>
	</form>
</section>
