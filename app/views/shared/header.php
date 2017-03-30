<?php
global $user;
 ?>
	<header>
		<h1><a href="/">MyDiscussionForum</a></h1>
		<nav>
			<ul>
				<li><a href="/">Home</a></li>
				<li><a href="/content/search">Search</a></li>
				<?php if(!isset($user)) { ?>
					<li><a href="/account/login">Login</a></li>
					<li><a href="/account/register">Register</a></li>
				<?php } else { ?>
					<li><a href="/account/profile">Profile</a></li>
					<li><a href="/account/logout">Logout</a></li>
				<?php } ?>
			</ul>
		</nav>
	</header>
