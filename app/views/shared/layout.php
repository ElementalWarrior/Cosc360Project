
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="/content_static/styles/styles.css">
		<title>Index</title>
		<script src="/content_static/scripts/jquery-3.2.0.min.js"></script>
		<script type="text/javascript">
			function Breadcrumbs(crumbs) {
				var html = "";
				for (var i = 0; i < crumbs.length; i++) {
					var crumb = crumbs[i];
					html += '<li><a href="' + crumb.href + '">' + crumb.text + '</a></li>';
				}
				$('#breadcrumbs ul').html(html);
			}
		</script>
	</head>
	<body>
		<?php Html::render_view("header") ?>
		<main>
			<div id="breadcrumbs">
				<ul>
					<li>
						<a href="#">Home</a>
					</li>
				</ul>
			</div>
			<?php

		  		render_body();
			?>
		</main>
		<?php Html::render_view("footer") ?>
	</body>
</html>
