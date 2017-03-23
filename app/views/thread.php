<?php include('sidebar.php'); ?>
<section id="thread">
	<section class="thread-content">
		<h3>
			This is the title!
			<div class="author">FooBar123</div>
		</h3>
		<p>This is the content body of the thread. It shall be followed up by posts from users!</p>
	</section>
	<section id="posts">
		<?php foreach(range(0,10) as $i) { ?>
		<div class="post">
			<div class="response-by">Response By:</div>
			<div class="author">FooBar321</div>
			<p>This is the post body of a thread reply. Here people will flame the crap out of <thead>
				OP and generally spread hate and troll each other!
			</thead></p>
		</div>
		<?php } ?>
	</section>
	<section id="respond">
		<h3>Post a response:</h3>
		<form class="" action="index.html" method="post">
			<textarea name="respond" rows="8" cols="80" required></textarea>
			<input type="submit" name="submit" value="Submit your response!">
		</form>
	</section>

</section>
