<?php
	global $sub_path;
	global $page_title;
	$page_title = "Login Page";
 ?>
<section id="login" class="single-centered">
	<form class="" action="<?php echo $sub_path; ?>/account/login" method="post">
		<h2>Login:</h2>
			<?php if(!empty($view_data['error'])) {
				echo "<h3 role=\"alert\"><strong>" . $view_data['error'] . "</strong></h3>";
			}?>
		<div class="">
			<input type="text" name="username" value="" placeholder="Username" required aria-required="true">
		</div>
		<div class="">
			<input type="password" name="password" value="" placeholder="Password" required aria-required="true">
			<div class="">
				<a href="<?php echo $sub_path; ?>/account/forgot_password">Forgot your password?</a>
			</div>
		</div>
		<div class="text-center">
			<input type="submit" name="submit" class="btn" value="Login!">
		</div>
	</form>
</section>

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/account/login",
			text: "Login"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
