<?php global $sub_path; ?>
<?php if(count($view_data)) { ?>
<article id="hot_threads">
	<h2>Hot Threads</h2>
	<ul class="list-unstyled list-striped">
		<?php foreach($view_data as $thread) { ?>
			<li class="hot-thread">
				<a href="<?php echo "$sub_path/content/thread/" . $thread['thread_id']; ?>"><?php echo $thread['thread_name']; ?></a>
				<a href="<?php echo "$sub_path/content/thread/" . $thread['thread_id']; ?>" class="a-alt comments"><?php echo $thread['num_posts']; ?> comments</a>
			</li>
		<?php } ?>
	</ul>
</article>

<?php } ?>
