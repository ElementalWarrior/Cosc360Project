<?php

 ?>
<section id="register" class="single-centered">
	<form class="" id="frm-register" action="<?php global $sub_path; echo $sub_path; ?>/account/register" method="post" enctype="multipart/form-data">
		<h2>Register:</h2>
			<?php if(!empty($view_data['error'])) {
				echo "<h3 role=\"alert\"><strong>" . $view_data['error'] . "</strong></h3>";
			}?>
		<div class="">
			<input type="text" name="username" value="<?php echo empty($view_data['username']) ? '' : $view_data['username']; ?>" placeholder="Username" required aria-required="true">
		</div>
		<div class="">
			<input type="password" name="password" id="password" value="" placeholder="Password" required aria-required="true">
		</div>
		<div class="">
			<input type="password" name="password-check" id="password-check" value="" placeholder="Confirm Password" required aria-required="true">
		</div>
		<div class="">
			<input type="email" name="email" value="<?php echo empty($view_data['email']) ? '' : $view_data['email']; ?>" placeholder="Email" required aria-required="true">
		</div>
		<div id="image-upload">
			<div class="">
				Choose a profile image:
			</div>
			<input type="file" name="image" required aria-required="true">
		</div>
		<div class="text-center">
			<input type="submit" name="submit" class="btn" value="Login!">
		</div>
	</form>
</section>

<script type="text/javascript">
	window.addEventListener('load', function() {

		document.getElementById('frm-register').addEventListener('submit', function(e) {
			var pass = document.getElementById('password');
			var check = document.getElementById('password-check');

			if(pass.value !== check.value) {
				alert('Passwords don\'t match');
				e.preventDefault();
			}
		})
	})
</script>

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/account/register",
			text: "Register"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
