<?php
global $sub_path;
global $user;
global $page_title;
$page_title = "Activity By Date: " . $view_data['date'];
if(empty($view_data['date'])) {
	$page_title = "Activity";
}
// print_r($view_data['results']);;
?>
<table class="table-padded table-striped" id="table-activity">
	<thead>
		<tr>
			<th class="text-left">Time</th>
			<?php if(!empty($user['admin']) && $user['admin']) { ?>
				<th class="text-left">Action Taken</th>
			<?php } ?>
			<th class="text-left">Activity</th>
			<th class="text-left">Request URI</th>
			<?php if(!empty($user['admin']) && $user['admin']) { ?>
				<th class="text-left">User Agent</th>
				<th class="text-left">IP Address</th>
			<?php } ?>
		</tr>
	</thead>
		<tbody>
			<?php foreach($view_data['results'] as $activity) {
				if((empty($user['admin']) || !$user['admin']) && !in_array($activity['action'], ['view', 'thread_submit', 'post_submit'])) {
					continue;
				}
				$username = $activity['username'];
				$account_id = $activity['account_id'];
				$thread_id = $activity['thread_id'];
				$thread_name = Html::special_chars($activity['thread_name']);
				$request_uri = Html::special_chars($activity['request_uri']);
				$useragent = $activity['user_agent'];
				$ip = $activity['ip'];
				$profile_link = "<a href=\"$sub_path/account/profile/$account_id\">$username</a>";
				?>
					<tr
					<?php if(!empty($user['admin']) && $user['admin']) {
						echo 'data-action="' . $activity['action'] . '"';
					} ?>
					>
						<td><?php echo (new DateTime($activity['date_created']))->format('Y-m-d H:i:s'); ?></td>
						<?php if(!empty($user['admin']) && $user['admin']) { ?>
							<td class="tdAction">
								<?php echo $activity['action']; ?>
							</td>
						<?php } ?>
						<td><?php

						if(empty($account_id)){
							$profile_link = "anonymous";
						}

						switch($activity['action']) {
							case 'thread_submit':
								echo "$profile_link created the thread <a href=\"$sub_path/content/thread/$thread_id\">$thread_name</a>";
								break;

							case 'post_submit':
								echo "$profile_link replied to the thread <a href=\"$sub_path/content/thread/$thread_id\">$thread_name</a>";
								break;

							case 'view':
								echo "$profile_link viewed the page <a href=\"$request_uri\">$request_uri</a>";
								break;

							case 'search':
								echo "$profile_link submitted a search.";
								break;

							case 'thread_remove':
								echo "$profile_link removed the thread `$thread_name`";
								break;

							case 'post_remove':
								echo "$profile_link removed a post($thread_id) on the thread <a href=\"$sub_path/content/thread/$thread_id\">$thread_name</a>";
								break;

							case 'thread_edit':
								echo "$profile_link edited the thread <a href=\"$sub_path/content/thread/$thread_id\">$thread_name</a>";
								break;

							case 'post_edit':
								echo "$profile_link edited a post($post_id) on <a href=\"$sub_path/content/thread/$thread_id\">$thread_name</a>";
								break;

							case 'login':
								echo "$profile_link logged in.";
								break;

							case 'logout':
								echo "$profile_link logged out.";
								break;

							case 'register':
								echo "$profile_link registered an account.";
								break;

							case 'profile_update':
								echo "$profile_link updated their <a href=\"$sub_path/account/profile/$account_id\">profile</a>.";
								break;

							case 'forgot_password_invalid':
								echo "$profile_link entered an incorrect email when trying to recover their password.";
								break;

							case 'forgot_password':
								echo "$profile_link submitted a forgot password request.";
								break;

							case 'recover_password':
								echo "$profile_link recovered their password";
								break;

							case 'account_status':
								$set_id = (int)preg_replace('/.*?\/([0-9]+).*/', '$1', $request_uri);
								$value = ((bool)preg_replace('/.*\/([0-9]+)/', '$1', $request_uri)) ? "enabled" : "disabled";
								echo "$profile_link set the account status of account_id: <a href=\"$sub_path/account/profile/$set_id\">$set_id</a> to $value";
								break;


						}
						?></td>
						<td><?php echo $request_uri; ?></td>
						<?php if(!empty($user['admin']) && $user['admin']) { ?>
							<td class="tdUseragent"><?php echo $useragent; ?></td>
							<td><?php echo $ip; ?></td>
						<?php } ?>
					</tr>
			<?php } ?>

	</tbody>
</table>
