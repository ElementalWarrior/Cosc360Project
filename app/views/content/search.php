<?php
global $view_data;
Html::render_view('sidebar');
?>

<section id="search">
	<div id="query">

		<form class="" action="/content/search" method="post">

			<h2>Search <?php echo count($view_data['content_results']); ?></h2>
			<p>Search by either Username, Email, or post content</p>
			<div>
				<input type="text" name="username" value="<?php echo $view_data['username']; ?>" placeholder="Username" />
			</div>
			<div class="or">
				or
			</div>
			<div>
				<input type="text" name="email" value="<?php echo $view_data['email']; ?>" placeholder="Email" />
			</div>
			<div class="or">
				or
			</div>
			<div>
				<input type="text" name="posts" value="<?php echo $view_data['posts']; ?>" placeholder="Post content" />
			</div>
			<button type="submit" name="search" class="btn" id="btnSearch">Search!</button>
		</form>
	</div>
	<?php
	function render_user($user) {
			echo '<img id="profile-image" class="search-image" src="data:image/' . $user['content_type'] . ';base64,' . base64_encode($user['image']) . '" alt="">';
			echo '<div class="search-username"><a href="/account/profile/' . $user['account_id'] . '">' . $user['username'] . '</a></div>';
			echo '<div class="search-email">' . $user['email'] . '</div>';
	}
	 ?>
	<?php if(!empty($view_data['user_results'])) { ?>
	<div id="results">
		<h2>Results:</h2>
		<ul>

			<?php foreach($view_data['user_results'] as $user) {
					echo '<li>';
				render_user($user);
				echo '</li>';

			} ?>
		</ul>
	</div>
	<?php } else if(!empty($view_data['content_results'])) { ?>
	<div id="results">
		<h2>Results:</h2>
		<ul id="ul-threads">

			<?php
			$last_thread_id = null;
			foreach($view_data['content_results'] as $search) {
				$render_thread = false;
				if($last_thread_id != null && $last_thread_id != $search) {
					echo '</ul></li>';
					$render_thread = true;
				} else if($last_thread_id == null) {
					$render_thread = true;
				}
				if($render_thread) {
					$last_thread_id = $search['thread_id'];
					echo '<li>';
					echo '<h4>' . $search['thread_name'] . '</h4>';
					echo '<p>' . $search['thread_body'] . '</p>';
					$user = array(
						'account_id' => $search['thread_account_id'],
						'username' => $search['thread_username'],
						'email' => $search['thread_email'],
						'image' => $search['thread_image'],
						'content_type' => $search['thread_content_type']
					);
					render_user($user);
					echo '<ul class="ui-posts">';
				}
				if(!empty($search['post_id']))
					echo '<li>';
					echo '<p>' . $search['post_body'] . '</p>';
					echo '</li>';
			} ?>
		</ul>
	</div>
	<?php } ?>
</section>
