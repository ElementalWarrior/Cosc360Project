<?php
	global $view_data;
?>
<?php Html::render_view("sidebar") ?>
<section id="posts">
<!-- <div id="post-header" class="clearfix">
	<div class="post-right">
		<span class="fa fa-reply"></span>
	</div>
</div> -->
<?php foreach($view_data as $row) { ?>
	<article>
		<div class="thread-right">
			<span class="replies"><?php echo $row['num_posts']; ?></span>
		</div>
		<h3><a href="/content/thread/<?php echo $row['thread_id']; ?>"><?php echo $row['thread_name']?></a></h3>
		<span class="author"><?php echo $row['username']; ?></author>
	</article>
<?php } ?>
</section>
