<?php
	global $user;
	global $page_title;
	$page_title = "Discussion Thread: " . Html::special_chars($view_data['thread']['thread_name']);

	$thread = $view_data['thread'];
	$posts = $view_data['posts'];
 ?>
 <?php echo Html::render_view('sidebar'); ?>
<section id="thread">
	<?php if($user['admin']) { ?>
		<div class="text-right">
			<a class="btn-alt" href="<?php global $sub_path; echo $sub_path; ?>/content/edit_thread/<?php echo $thread['thread_id']; ?>" id="btnEditThread">Edit Thread</a>
		</div>
	<?php } ?>
	<section class="thread-content">
		<img src="data:image/<?php echo $thread['content_type'] . ';base64,' . base64_encode($thread['image']);?>" alt="<?php echo $thread['username'];?>&apos;s profile picture">
		<h3>
			<?php echo Html::special_chars($thread['thread_name']); ?>
			<a href="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $thread['account_id']; ?>" class="author"><?php echo Html::special_chars($thread['username']); ?></a>
		</h3>
		<p><?php echo Html::special_chars($thread['thread_body']); ?></p>
		<a aria-label="View activity for <?php echo date_helper::convertFromUtc($thread['date_created'])->format('l F d, Y'); ?>" href="<?php echo $sub_path; ?>/content/activity_by_date/<?php echo date_helper::convertFromUtc($thread['date_created'])->format('Y-m-d'); ?>" class="date-posted"><?php echo date_helper::convertFromUtc($thread['date_created'])->format('Y-m-d H:i:s'); ?></a>
	</section>
	<section id="posts">
		<?php foreach($posts as $post) { ?>
		<div class="post" data-post-id="<?php echo $post['post_id']; ?>">
			<img src="data:image/<?php echo $post['content_type'] . ';base64,' . base64_encode($post['image']);?>" alt="<?php echo $post['username'];?>&apos;s profile picture">
			<div class="response-by">Response By:</div>
			<a href="<?php global $sub_path; echo $sub_path; ?>/account/profile/<?php echo $post['account_id']; ?>" class="author"><?php echo $post['username']; ?></a>
			<p><?php echo Html::special_chars($post['post_body']); ?></p>
			<?php if($user['admin']) { ?>
				<a href="<?php global $sub_path; echo $sub_path; ?>/content/edit_post/<?php echo $thread['thread_id']; ?>/<?php echo $post['post_id']; ?>" class="btn-alt btn-small btnEditPost">Edit Post</a>
				<button type="button" name="button" class="btn-alt btn-small btnRemovePost" data-thread-id="<?php echo $thread['thread_id']; ?>" data-post-id="<?php echo $post['post_id']; ?>">Remove Post</button>
			<?php } ?>
			<a aria-label="View activity for <?php echo date_helper::convertFromUtc($post['date_created'])->format('l F d, Y'); ?>" href="<?php echo $sub_path; ?>/content/activity_by_date/<?php echo date_helper::convertFromUtc($post['date_created'])->format('Y-m-d'); ?>" class="date-posted"><?php echo date_helper::convertFromUtc($post['date_created'])->format('Y-m-d H:i:s'); ?></a>
		</div>
		<?php } ?>
	</section>
	<?php if(is_array($user)) { ?>
	<section id="respond">
		<h3>Post a response:</h3>
		<form class="" action="<?php global $sub_path; echo $sub_path; ?>/content/reply/<?php echo Html::special_chars($thread['thread_id']); ?>" method="post">
			<textarea name="respond" rows="8" cols="80" required aria-required="true" aria-label="Post a response text box"></textarea>
			<input type="submit" name="submit" value="Submit your response!" aria-label='Submit response'>
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
			$('.btnRemovePost[data-post-id=' + post_id + ']').closest('.post').remove();
			$.ajax({url: '/content/remove_post/' + thread_id + '/' + post_id}).fail(function() {
				alert('There was a problem removing the post');
			});
		});
	})
</script>


<script type="text/javascript">
	String.prototype.encode = function(){return this.replace(/[^]/g,function(e){return"&#"+e.charCodeAt(0)+";"})}
	function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function(match, p1) {
        return String.fromCharCode('0x' + p1);
    }));
}
	var sub_path = '<?php echo $sub_path; ?>';
	var date_last_updated = new Date(moment().valueOf() + moment().utcOffset() * -60*1000);
	window.setInterval(function() {
		$.ajax({url: '<?php echo $sub_path;?>/content/check_posts/' + moment(date_last_updated).format('YYYY-MM-DD HH:mm:ss') + '/<?php echo $thread['thread_id']; ?>', success: function(data) {

			data = JSON.parse(data);
			var posts = $('[data-post-id]').map(function(ind, ele) {
				return parseInt($(ele).attr('data-post-id'));
			}).toArray();
			for(var i = 0; i < data.length; i++) {
				var post = data[i];
				var thread_id = post.thread_id;
				var article = $('.post:nth-child(1)').clone();
				if(posts.indexOf(post.post_id) > -1) {
					continue;
				}
				article.find('img').prop('src', 'data:image/' + post.content_type + ';base64,' + post.image);
				article.find('[data-thread-id]').each(function() { $(this).attr('data-thread-id', thread_id); });
				article.find('[data-post-id]').each(function() { $(this).attr('data-post-id', post.post_id); });
				article.find('p').html(post.post_body.encode());
				article.find('.replies').html(post.num_posts);
				// article.find('.date-posted').html(thread.num_posts);
				article.find('.thread-link').prop('href', sub_path + '/content/thread/' + thread_id).html(post.thread_name);
				article.find('.account-link').prop('href', sub_path + '/account/profile/' + post.account_id).html(post.username);
				article.find('.btnEditPost').prop('href', sub_path + '/content/edit_post/' + post.thread_id + '/' + post.post_id);
				article.find('.date-posted').prop('href', sub_path + '/content/activity_by_date/' + moment(post.date_created).format('YYYY-MM-DD HH:mm:ss')).html(post.date_created);
				$('#posts').append(article);
			}
			if(data.length > 0){
				window.date_last_updated = new Date(moment().valueOf() + moment().utcOffset() * -60*1000);
			}
		}
		})
	}, 10000);
</script>
