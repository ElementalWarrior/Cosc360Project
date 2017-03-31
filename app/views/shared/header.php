<?php
global $user;
 ?>
	<header>
		<h1><a href="<?php global $sub_path; echo $sub_path; ?>/">MyDiscussionForum</a></h1>
		<nav>
			<ul>
				<li><a href="<?php global $sub_path; echo $sub_path; ?>/">Home</a></li>
				<li><a href="<?php global $sub_path; echo $sub_path; ?>/content/search">Search</a></li>
				<?php if(!isset($user)) { ?>
					<li><a href="<?php global $sub_path; echo $sub_path; ?>/account/login">Login</a></li>
					<li><a href="<?php global $sub_path; echo $sub_path; ?>/account/register">Register</a></li>
				<?php } else { ?>
					<li><a href="<?php global $sub_path; echo $sub_path; ?>/account/profile">Profile</a></li>
					<li><a href="<?php global $sub_path; echo $sub_path; ?>/account/logout">Logout</a></li>
				<?php } ?>
			</ul>
		</nav>
	</header>
