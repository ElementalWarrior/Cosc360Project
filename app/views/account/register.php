<?php
	global $view_data;
 ?>
<section id="register" class="single-centered">
	<form class="" id="frm-register" action="/account/register" method="post" enctype="multipart/form-data">
		<?php if(!empty($view_data['error'])) {
			echo "<h3><strong>" . $view_data['error'] . "</strong></h3>";
		}?>
		<h3>Register:</h3>
		<div class="">
			<input type="text" name="username" value="" placeholder="Username" required>
		</div>
		<div class="">
			<input type="password" name="password" id="password" value="" placeholder="Password" required>
		</div>
		<div class="">
			<input type="password" name="password-check" id="password-check" value="" placeholder="Confirm Password" required>
		</div>
		<div class="">
			<input type="email" name="email" value="" placeholder="Email" required>
		</div>
		<div id="image-upload">
			<div class="">
				Choose a profile image:
			</div>
			<input type="file" name="image" required>
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

			if(pass !== check) {
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
