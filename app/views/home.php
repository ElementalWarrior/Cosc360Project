
<?php include("sidebar.php") ?>
<section id="posts">
<!-- <div id="post-header" class="clearfix">
	<div class="post-right">
		<span class="fa fa-reply"></span>
	</div>
</div> -->
<?php for($i = 0; $i < 10; $i++) { ?>
	<article>
		<div class="thread-right">
			<span class="replies"><?php echo 20-$i*2; ?></span>
		</div>
		<h3><a href="/thread.php">Thread <?php echo $i?></a></h3>
		<span class="author">FooBar123</author>
	</article>
<?php } ?>
</section>