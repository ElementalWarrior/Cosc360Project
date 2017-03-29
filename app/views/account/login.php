<?php
global $view_data;
 ?>
<section id="login" class="single-centered">
	<form class="" action="/account/login" method="post">
		<h3>Login:</h3>
		<div class="">
			<input type="text" name="username" value="" placeholder="Username" required>
		</div>
		<div class="">
			<input type="password" name="password" value="" placeholder="Password" required>
			<div class="">
				<a href="/forgot-password.php">Forgot your password?</a>
			</div>
		</div>
		<div class="text-center">
			<input type="submit" name="submit" class="btn" value="Login!">
		</div>
	</form>
</section>
