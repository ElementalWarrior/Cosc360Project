<?php
	global $view_data;
	$thread = $view_data['thread'];
	$posts = $view_data['posts'];
 ?>
<?php Html::render_view('sidebar'); ?>
<section id="thread">
	<section class="thread-content">
		<h3>
			<?php echo Html::special_chars($thread['thread_name']); ?>
			<div class="author"><?php echo Html::special_chars($thread['username']); ?></div>
		</h3>
		<p><?php echo Html::special_chars($thread['thread_body']); ?></p>
	</section>
	<section id="posts">
		<?php foreach($posts as $post) { ?>
		<div class="post">
			<div class="response-by">Response By:</div>
			<div class="author"><?php echo $post['username']; ?></div>
			<p><?php echo Html::special_chars($post['post_body']); ?></p>
			<div class="date-posted"><?php echo $post['date_created']; ?></div>
		</div>
		<?php } ?>
	</section>
	<section id="respond">
		<h3>Post a response:</h3>
		<form class="" action="/content/reply/<?php echo Html::special_chars($thread['thread_id']); ?>" method="post">
			<textarea name="respond" rows="8" cols="80" required></textarea>
			<input type="submit" name="submit" value="Submit your response!">
		</form>
	</section>
</section>
