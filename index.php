<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" href="styles.css">
		<title>Index</title>
	</head>
	<body>
		<section id="posts">
			<div id="post-header" class="clearfix">
				<div class="post-right">
					<span class="fa fa-reply"></span>
				</div>
			</div>
			<?php for($i = 0; $i < 10; $i++) { ?>
				<article>
					<div class="post-right">
						<span class="replies"><?php echo 20-$i*2; ?></span>
					</div>
					<h3>Post <?php echo $i?></h3>
					<author>FooBar123</author>
				</article>
			<?php } ?>
		</articles>
	</body>
</html>
