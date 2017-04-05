<?php

	global $page_title;
	$page_title = "File not found.";
?>
<section id="missing">
	<div style="opacity: 0;" role="alert">An error has occurred. This has been logged.</div>
	<img src="<?php global $sub_path; echo $sub_path; ?>/content_static/images/confused_unicorn.jpg" alt="Funny looking unicorn image for satirical reasons." />
	<h1>Something went wrong!</h1
	<p>
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
			href: "/error/server_error",
			text: "An error has occurred"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
