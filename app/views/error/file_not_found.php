
<section id="missing">
	<img src="<?php global $sub_path; echo $sub_path; ?>/content_static/images/confused_unicorn.jpg" alt="" />
	<h1>Oh snap!</h1
	<p>You must be lost!
		<br/><br/>
		Click <a href="<?php global $sub_path; echo $sub_path; ?>/">here</a> to go to the home page!
	</p>
</section>

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/error/file_not_found",
			text: "File Not Found"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
