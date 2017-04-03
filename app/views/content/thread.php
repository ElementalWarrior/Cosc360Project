<?php
	global $user;

	$thread = $view_data['thread'];
	$posts = $view_data['posts'];
 ?>
<?php Html::render_view('sidebar'); ?>
<section id="thread">
	<?php if($user['admin']) { ?>
		<div class="text-right">
			<a class="btn-alt" href="<?php global $sub_path; echo $sub_path; ?>/content/edit_thread/<?php echo $thread['thread_id']; ?>" id="btnEditThread">Edit Thread</a>
		</div>
	<?php } ?>
	<section class="thread-content">
		<img src="data:image/<?php echo $thread['content_type'] . ';base64,' . base64_encode($thread['image']);?>" alt="">
		<h3>
			<?php echo Html::special_chars($thread['thread_name']); ?>
			<a href="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $thread['account_id']; ?>" class="author"><?php echo Html::special_chars($thread['username']); ?></a>
		</h3>
		<p><?php echo Html::special_chars($thread['thread_body']); ?></p>
		<a href="<?php echo $sub_path; ?>/content/activity_by_date/<?php echo (new DateTime($thread['date_created']))->format('Y-m-d'); ?>" class="date-posted"><?php echo $thread['date_created']; ?></a>
	</section>
	<section id="posts">
		<?php foreach($posts as $post) { ?>
		<div class="post">
			<img src="data:image/<?php echo $post['content_type'] . ';base64,' . base64_encode($post['image']);?>" alt="">
			<div class="response-by">Response By:</div>
			<a href="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $post['account_id']; ?>" class="author"><?php echo $post['username']; ?></a>
			<p><?php echo Html::special_chars($post['post_body']); ?></p>
			<?php if($user['admin']) { ?>
				<a href="<?php global $sub_path; echo $sub_path; ?>/content/edit_post/<?php echo $thread['thread_id']; ?>/<?php echo $post['post_id']; ?>" class="btn-alt btn-small btnEditPost">Edit Post</a>
				<button type="button" name="button" class="btn-alt btn-small btnRemovePost" data-thread-id="<?php echo $thread['thread_id']; ?>" data-post-id="<?php echo $post['post_id']; ?>">Remove Thread</button>
			<?php } ?>
			<a href="<?php echo $sub_path; ?>/content/activity_by_date/<?php echo (new DateTime($post['date_created']))->format('Y-m-d'); ?>" class="date-posted"><?php echo $post['date_created']; ?></a>
		</div>
		<?php } ?>
	</section>
	<?php if(is_array($user)) { ?>
	<section id="respond">
		<h3>Post a response:</h3>
		<form class="" action="<?php global $sub_path; echo $sub_path; ?>/content/reply/<?php echo Html::special_chars($thread['thread_id']); ?>" method="post">
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


<script type="text/javascript">
	$(document).ready(function() {
		$('.btnRemovePost').on('click', function(e) {
			var post_id = $(e.target).attr('data-post-id');
			var thread_id = $(e.target).attr('data-thread-id');
			$('[data-post-id=' + post_id + ']').closest('.post').remove();
			$.ajax({url: '/content/remove_post/' + thread_id + '/' + post_id}).fail(function() {
				alert('There was a problem removing the post');
			});
		});
	})
</script>


<script type="text/javascript">
	var sub_path = '<?php echo $sub_path; ?>';
	var date_last_updated = new Date('<?php echo (new DateTime())->format('Y-m-d H:i:s'); ?>');
	console.log(date_last_updated);
	window.setInterval(function() {
		$.ajax({url: '<?php echo $sub_path;?>/content/check_posts/' + moment(date_last_updated).format('YYYY-MM-DD HH:mm:ss'), success: function(data) {
			
			data = JSON.parse(data);
			for(var i = 0; i < data.length; i++) {
				var thread = data[i];
				var thread_id = thread.thread_id;
				var article = $('.post:nth-child(1)').clone();
				article.hide();
				article.find('[data-thread-id]').each(function() { $(this).attr('data-thread-id', thread_id); });
				article.find('[data-post-id]').each(function() { $(this).attr('data-post-id', post_id); });
				article.find('p').html(thread.post_body);
				article.find('.replies').html(thread.num_posts);
				// article.find('.date-posted').html(thread.num_posts);
				article.find('.thread-link').prop('href', sub_path + '/content/thread/' + thread_id).html(thread.thread_name);
				article.find('.account-link').prop('href', sub_path + '/account/profile/' + thread.account_id);
				$('#thread_articles').prepend(article);
			}
			if(data.length > 0) {
				window.date_last_updated = new Date();
			}
		}
		})
	}, 1000);
</script>

		<div class="post">
			<img src="data:image/<?php echo $post['content_type'] . ';base64,' . base64_encode($post['image']);?>" alt="">
			<div class="response-by">Response By:</div>
			<a href="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $post['account_id']; ?>" class="account-link author"><?php echo $post['username']; ?></a>
			<p><?php echo Html::special_chars($post['post_body']); ?></p>
			<?php if($user['admin']) { ?>
				<a href="<?php global $sub_path; echo $sub_path; ?>/content/edit_post/<?php echo $thread['thread_id']; ?>/<?php echo $post['post_id']; ?>" class="btn-alt btn-small btnEditPost">Edit Post</a>
				<button type="button" name="button" class="btn-alt btn-small btnRemovePost" data-thread-id="<?php echo $thread['thread_id']; ?>" data-post-id="<?php echo $post['post_id']; ?>">Remove Thread</button>
			<?php } ?>
			<a href="<?php echo $sub_path; ?>/content/activity_by_date/<?php echo (new DateTime($post['date_created']))->format('Y-m-d'); ?>" class="date-posted"><?php echo $post['date_created']; ?></a>
		</div>
