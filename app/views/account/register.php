
<section id="register" class="single-centered">
	<form class="" action="/account/register" method="post" enctype="multipart/form-data">
		<h3>Register:</h3>
		<div class="">
			<input type="text" name="username" value="" placeholder="Username" required>
		</div>
		<div class="">
			<input type="password" name="password" value="" placeholder="Password" required>
		</div>
		<div class="">
			<input type="password" name="password-check" value="" placeholder="Confirm Password" required>
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
			<input type="submit" name="submit" value="Login!">
		</div>
	</form>
</section>
