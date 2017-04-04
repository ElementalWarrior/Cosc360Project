
<section id="missing">
	<div style="opacity: 0;" role="alert">File not found. You must have taken a wrong turn.</div>
	<img src="<?php global $sub_path; echo $sub_path; ?>/content_static/images/confused_unicorn.jpg" alt="Funny looking unicorn image for satirical reasons." />
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
