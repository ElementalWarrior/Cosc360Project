<?php
	global $user;
	 global $sub_path;
?>

<?php echo Html::render_view("sidebar") ?>
<section id="posts">
<!-- <div id="post-header" class="clearfix">
	<div class="post-right">
		<span class="fa fa-reply"></span>
	</div>
</div> -->
<?php if(count($view_data) == 0) { ?>
	<h2>There are no threads to show, why don't you create one?</h2>
<?php } ?>
<div id="new_threads_to_show">
	<a href="javascript:ShowNewThreads();">Click here to show the new discussions!</a>
</div>
<div id="thread_articles">
	<?php foreach($view_data as $row) { ?>
		<article class="index-thread" data-thread-id="<?php echo $row['thread_id']; ?>">
			<div class="thread-right" aria-hidden="true">
				<span class="replies"><?php echo $row['num_posts']; ?></span>
				<?php if($user['admin']) { ?>
					<button type="button" name="button" class="btn-alt btn-small btnRemoveThread" data-thread-id="<?php echo $row['thread_id']; ?>" aria-hidden="true">Remove Thread</button>
				<?php } ?>
			</div>
			<h3 aria-describedby="thread<?php echo $row['thread_id'];?>"><a id="thread<?php echo $row['thread_id'];?>" class="thread-link" href="<?php echo $sub_path; ?>/content/thread/<?php echo $row['thread_id']; ?>"><?php echo Html::special_chars($row['thread_name'])?></a></h3>
			<a aria-label="Posted by <?php echo $row['username']; ?>" href="<?php echo $sub_path; ?>/account/profile/<?php echo $row['account_id']; ?>" class="author account-link"><?php echo $row['username']; ?></a>
		</article>
	<?php } ?>
</div>
	<?php if(is_array($user)) { ?>
	<div class="text-right">
		<a href="<?php echo $sub_path; ?>/content/new_thread/" class="btn" aria-label="Create a new thread">New Thread</a>
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

<script type="text/javascript">
	var sub_path = '<?php echo $sub_path; ?>';
	var date_last_updated = new Date('<?php echo (new DateTime())->format('Y-m-d H:i:s'); ?>');
	window.setInterval(function() {
		$.ajax({url: '<?php echo $sub_path;?>/content/check_thread/' + moment(date_last_updated).format('YYYY-MM-DD HH:mm:ss'), success: function(data) {

			data = JSON.parse(data);
			for(var i = 0; i < data.length; i++) {
				var thread = data[i];
				var thread_id = thread.thread_id;
				var article = $('.index-thread:nth-child(1)').clone();
				article.hide();
				article.find('[data-thread-id]').each(function() { $(this).attr('data-thread-id', thread_id); });
				article.find('.replies').html(thread.num_posts);
				article.find('.thread-link').prop('href', sub_path + '/content/thread/' + thread_id).html(thread.thread_name);
				article.find('.account-link').prop('href', sub_path + '/account/profile/' + thread.account_id);
				$('#thread_articles').prepend(article);
			}
			if(data.length > 0) {
				$('#new_threads_to_show').show();
			window.date_last_updated = new Date();
			}
		}
		})
	}, 10000);

	function ShowNewThreads() {
		$('#new_threads_to_show').hide();
		$('#thread_articles article').show();
	}
</script>
