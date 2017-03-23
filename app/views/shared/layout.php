
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="/content_static/styles/styles.css">
		<title>Index</title>
	</head>
	<body>
		<?php Html::render_view("header") ?>
		<main>
			<!-- <div id="breadcrumbs">
				<ul>
					<li>
						<a href="#">Home</a>
					</li>
				</ul>
			</div> -->
			<?php

		  		render_body();
			?>
		</main>
		<?php Html::render_view("footer") ?>
	</body>
</html>
