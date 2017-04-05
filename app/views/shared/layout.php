
<!DOCTYPE html>
<html lang="English">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="<?php global $sub_path; echo $sub_path; ?>/content_static/styles/styles.css">
		<title><?php echo page_title(); ?></title>
		<script src="<?php global $sub_path; echo $sub_path; ?>/content_static/scripts/jquery-3.2.0.min.js"></script>

		<script type="text/javascript" src="<?php echo "$sub_path/content_static/scripts/moment.min.js"; ?>"></script>
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
			<nav id="breadcrumbs" aria-label="Breadcrumbs">
				<ul>
					<li>
						<a href="#">Home</a>
					</li>
				</ul>
			</nav>
			<?php

		  		render_body();
			?>
		</main>
		<?php Html::render_view("footer") ?>
	</body>
</html>
