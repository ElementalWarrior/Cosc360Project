<?php
	global $user;
	global $view_data;
	$thread = $view_data['thread'];
	$posts = $view_data['posts'];
 ?>
<?php Html::render_view('sidebar'); ?>
<section id="thread">
	<section class="thread-content">
		<img src="data:image/<?php echo $thread['content_type'] . ';base64,' . base64_encode($thread['image']);?>" alt="">
		<h3>
			<?php echo Html::special_chars($thread['thread_name']); ?>
			<div class="author"><?php echo Html::special_chars($thread['username']); ?></div>
		</h3>
		<p><?php echo Html::special_chars($thread['thread_body']); ?></p>
	</section>
	<section id="posts">
		<?php foreach($posts as $post) { ?>
		<div class="post">
			<img src="data:image/<?php echo $post['content_type'] . ';base64,' . base64_encode($post['image']);?>" alt="">
			<div class="response-by">Response By:</div>
			<div class="author"><?php echo $post['username']; ?></div>
			<p><?php echo Html::special_chars($post['post_body']); ?></p>
			<div class="date-posted"><?php echo $post['date_created']; ?></div>
		</div>
		<?php } ?>
	</section>
	<?php if(is_array($user)) { ?>
	<section id="respond">
		<h3>Post a response:</h3>
		<form class="" action="/content/reply/<?php echo Html::special_chars($thread['thread_id']); ?>" method="post">
			<textarea name="respond" rows="8" cols="80" required></textarea>
			<input type="submit" name="submit" value="Submit your response!">
		</form>
	</section>
	<?php } ?>
</section>

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/content/thread/<?php echo $thread['thread_id']; ?>",
			text: "<?php echo Html::special_chars($thread['thread_name']); ?>"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
