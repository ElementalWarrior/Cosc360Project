<?php
	global $user;
	global $view_data;
?>
<?php Html::render_view("sidebar") ?>
<section id="posts">
<!-- <div id="post-header" class="clearfix">
	<div class="post-right">
		<span class="fa fa-reply"></span>
	</div>
</div> -->
<?php if(count($view_data) == 0) { ?>
	<h2>There are no threads to show, why don't you create one?</h2>
<?php } ?>
<?php foreach($view_data as $row) { ?>
	<article>
		<div class="thread-right">
			<span class="replies"><?php echo $row['num_posts']; ?></span>
			<?php if($user['admin']) { ?>
				<button type="button" name="button" class="btn-alt btn-small btnRemoveThread" data-thread-id="<?php echo $row['thread_id']; ?>">Remove Thread</button>
			<?php } ?>
		</div>
		<h3><a href="/content/thread/<?php echo $row['thread_id']; ?>"><?php echo Html::special_chars($row['thread_name'])?></a></h3>
		<a href="/account/profile/<?php echo $row['account_id']; ?>" class="author"><?php echo $row['username']; ?></a>
	</article>
<?php } ?>
	<?php if(is_array($user)) { ?>
	<div class="text-right">
		<a href="/content/new_thread/" class="btn">New Thread</a>
	</div>
	<?php } ?>
</section>

<script type="text/javascript">
	$(document).ready(function() {
		$('.btnRemoveThread').on('click', function(e) {
			var thread_id = $(e.target).attr('data-thread-id');
			$('[data-thread-id=' + thread_id + ']').closest('article').remove();
			$.ajax({url: '/content/remove_thread/' + thread_id}).fail(function() {
				alert('There was a problem removing the thread');
			});
		});
	})
</script>
