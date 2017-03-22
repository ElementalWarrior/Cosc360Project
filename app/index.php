<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="styles.css">
		<title>Index</title>
	</head>
	<body>
		<?php include("header.php") ?>
		<main>
			<!-- <div id="breadcrumbs">
				<ul>
					<li>
						<a href="#">Home</a>
					</li>
				</ul>
			</div> -->
			<?php
			$path = $_SERVER['REQUEST_URI'];
			switch($path){
				case '/login.php':
				include('login.php');
				break;

				case '/thread.php':
				include('thread.php');
				break;

				case '/register.php':
				include('register.php');
				break;

				default:
				include('home.php');
				break;
			}
			?>
		</main>
		<?php include("footer.php") ?>
	</body>
</html>
