<?php
global $user;
global $sub_path; 
 ?>
	<header>
		<h1><a href="<?php echo $sub_path; ?>/">MyDiscussionForum</a></h1>
		<nav>
			<ul>
				<li><a href="<?php echo $sub_path; ?>/">Home</a></li>

				<?php if(!empty($user['admin']) && $user['admin']) { ?>
					<li><a href="<?php echo $sub_path; ?>/content/administrate">Admin</a></li>
				<?php } ?>

				<li><a href="<?php echo $sub_path; ?>/content/search">Search</a></li>
				<?php if(!isset($user)) { ?>
					<li><a href="<?php echo $sub_path; ?>/account/login">Login</a></li>
					<li><a href="<?php echo $sub_path; ?>/account/register">Register</a></li>
				<?php } else { ?>
					<li><a href="<?php echo $sub_path; ?>/account/profile">Profile</a></li>
					<li><a href="<?php echo $sub_path; ?>/account/logout">Logout</a></li>
				<?php } ?>
			</ul>
		</nav>
	</header>
