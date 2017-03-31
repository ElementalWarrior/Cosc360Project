<?php
	global $view_data;
 ?>
<section id="forgot_password" class="single-centered">
	<form class="" action="<?php global $sub_path; echo $sub_path; ?>/account/forgot_password" method="post">
		<h2>Password Recovery</h2>
		<?php if(!empty($view_data['error'])) {
			echo "<h3><strong>" . $view_data['error'] . "</strong></h3>";
		}?>
		<p>In order to recover your password, enter the email you used to register.</p>
		<div class="">
			<input type="email" name="email" value="" required placeholder="Email Address"/>
		</div>
		<button type="submit" name="button" class="btn" id="btnForgotSubmit">Send Password Reset Email</button>
	</form>
</section>
