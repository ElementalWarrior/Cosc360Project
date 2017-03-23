<?php app_include_once("/html_helper.php"); ?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="/content/styles/styles.css">
		<title>Index</title>
	</head>
	<body>
		<?php Html::RenderView("header") ?>
		<main>
			<!-- <div id="breadcrumbs">
				<ul>
					<li>
						<a href="#">Home</a>
					</li>
				</ul>
			</div> -->
			<?php
		  Html::RenderView($view, $controller);
			?>
		</main>
		<?php Html::RenderView("footer") ?>
	</body>
</html>
