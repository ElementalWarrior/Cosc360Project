<?php

global $sub_path;
Html::render_view('sidebar');
?>

<section id="search">
	<div id="query">

		<form class="" action="<?php global $sub_path; echo $sub_path; ?>/content/search" method="post">

			<h2>Search</h2>
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
	function render_user($user, $hide_email = false, $hide_image = false, $include_by = false) {

		global $sub_path;
		echo '<div class="user">';
		if(!$hide_image) {
			echo '<img id="search-image" class="search-image" src="data:image/' . $user['content_type'] . ';base64,' . base64_encode($user['image']) . '" alt="">';
		}
		echo '<div class="search-username">' . ($include_by ? 'by ' : '') . '<a href="' . $sub_path . '/account/profile/' . $user['account_id'] . '">' . str_replace($view_data['username'], '<span class="underline">' . $view_data['username'] . '</span>', $user['username']) . '</a></div>';
		if(!$hide_email) {
			echo '<div class="search-email">' . str_replace($view_data['email'], '<span class="underline">' . $view_data['email'] . '</span>', $user['email']) . '</div>';
		}
		echo '</div>';
	}
	 ?>
	 <?php if(empty($view_data['user_results']) && empty($view_data['content_results'])) { ?>
		 <h2>There are no results to display.</h2>
	 <?php }?>
	<?php if(!empty($view_data['user_results'])) { ?>
	<div id="user_results" class="results">
		<h2>Results:</h2>
		<ul class="striped">

			<?php foreach($view_data['user_results'] as $user) {
					echo '<li>';
				render_user($user);
				echo '</li>';

			} ?>
		</ul>
	</div>
	<?php } else if(!empty($view_data['content_results'])) { ?>
	<div id="content_results" class="results">
		<h2>Results:</h2>
		<ul id="ul-threads">

			<?php
			$last_thread_id = null;
			foreach($view_data['content_results'] as $search) {
				$render_thread = false;
				if($last_thread_id != null && $last_thread_id != $search['thread_id']) {
					echo '</ul></li>';
					$render_thread = true;
				} else if($last_thread_id == null) {
					$render_thread = true;
				}
				if($render_thread) {
					$last_thread_id = $search['thread_id'];
					$user = array(
						'account_id' => $search['thread_account_id'],
						'username' => $search['thread_username'],
						'email' => $search['thread_email'],
						'image' => $search['thread_image'],
						'content_type' => $search['thread_content_type']
					);
					echo '<li><div class="post_header">';
					render_user($user, true);
					echo '<h4><a href="' . $sub_path . '/content/thread/' . $search['thread_id'] . '">' . str_replace($view_data['posts'], '<span class="underline">' . $view_data['posts'] . '</span>', Html::special_chars($search['thread_name'])) . '</a></h4>';
					echo '<p>' . str_replace($view_data['posts'], '<span class="underline">' . $view_data['posts'] . '</span>', Html::special_chars($search['thread_body'])) . '</p></div>';
					echo '<ul class="ui-posts striped">';
				}
				if(!empty($search['post_id'])) {
					echo '<li>';
					$user = array(
						'account_id' => $search['post_account_id'],
						'username' => $search['post_username'],
						'email' => $search['post_email'],
						'image' => $search['post_image'],
						'content_type' => $search['post_content_type']
					);

					render_user($user, true, true, true);
					echo '<p>' . str_replace($view_data['posts'], '<span class="underline">' . $view_data['posts'] . '</span>', Html::special_chars($search['post_body'])) . '</p>';
					echo '</li>';
				}
			} ?>
		</ul>
	</div>
	<?php } ?>
</section>

<script type="text/javascript">
	var crumbs = [
		{
			href: "/",
			text: "Home"
		},
		{
			href: "/content/search",
			text: "Search"
		}
	]
	$(document).ready(Breadcrumbs(crumbs))
</script>
