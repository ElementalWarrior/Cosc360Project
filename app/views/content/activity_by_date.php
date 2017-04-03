<?php
global $sub_path;
?>
<table class="table-padded table-striped" id="table-activity">
	<thead>
		<tr>
			<th class="text-left">Time</th>
			<th class="text-left">Activity</th>
		</tr>
	</thead>
		<tbody>
			<?php foreach($view_data as $activity) {
				if(!in_array($activity['action'], ['thread_submit', 'post_submit'])) {
					continue;
				}
				?>
					<tr>
						<td><?php echo (new DateTime($activity['date_created']))->format('Y-m-d H:i:s'); ?></td>
						<td><?php
						switch($activity['action']) {
							case 'thread_submit':
								echo '<a href="' . $sub_path . '/account/profile/' . $activity['account_id'] . '">'. $activity['username'] . '</a> created the thread <a href="$sub_path/content/thread/' . $activity['thread_id'] . '">' . $activity['thread_name'] . '</a>`';
								break;
							case 'post_submit':
								echo '<a href="' . $sub_path . '/account/profile/' . $activity['account_id'] . '">'. $activity['username'] . '</a> replied to the thread <a href="$sub_path/content/thread/' . $activity['thread_id'] . '">' . $activity['thread_name'] . '</a>`';
								break;
						}
						?></td>
					</tr>
			<?php } ?>

	</tbody>
</table>
